<?php

namespace App\Services;

use App\Models\CleanupTask;
use App\Models\CleanupTaskPhoto;
use App\Models\CleanupTaskWorker;
use App\Models\User;
use App\Models\WasteReport;
use App\Models\WasteReportStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CleanupTaskService
{
    public function __construct(
        protected ImageStorageService $imageStorageService
    ) {}

    /**
     * Assign cleanup task to one or more village cleaning workers.
     *
     * @param WasteReport $report
     * @param User $admin
     * @param int[] $workerIds
     * @param string|null $notes Instructions & tools to bring (e.g. gloves, shovel, hat)
     * @return CleanupTask
     * @throws ValidationException
     */
    public function assignTask(WasteReport $report, User $admin, array $workerIds, ?string $notes = null): CleanupTask
    {
        $this->authorizeAdmin($report, $admin);

        if (!$report->isValidated() && $report->status !== WasteReport::STATUS_ASSIGNED) {
            throw new \DomainException("Hanya laporan berstatus 'Tervalidasi' yang dapat ditugaskan ke petugas.");
        }

        if (empty($workerIds)) {
            throw ValidationException::withMessages([
                'worker_ids' => ['Pilih minimal 1 orang petugas kebersihan desa.'],
            ]);
        }

        // Invariant #4: Ensure all workers belong to the report's village and have petugas_desa role
        $validWorkers = User::whereIn('id', $workerIds)
            ->where('village_id', $report->village_id)
            ->where('is_active', true)
            ->whereHas('role', fn($q) => $q->where('name', 'petugas_desa'))
            ->get();

        if ($validWorkers->count() !== count(array_unique($workerIds))) {
            throw ValidationException::withMessages([
                'worker_ids' => ['Satu atau lebih petugas yang dipilih tidak valid atau berada di luar wilayah kelurahan ini.'],
            ]);
        }

        return DB::transaction(function () use ($report, $admin, $validWorkers, $notes) {
            // Find existing or create new cleanup task (unique on report_id)
            $task = CleanupTask::firstOrNew(['report_id' => $report->id]);
            $task->assigned_by = $admin->id;
            $task->status = CleanupTask::STATUS_ASSIGNED;
            $task->notes = $notes;
            $task->assigned_at = now();
            $task->save();

            // Sync workers: keep or create worker records
            foreach ($validWorkers as $worker) {
                CleanupTaskWorker::updateOrCreate(
                    ['task_id' => $task->id, 'worker_id' => $worker->id],
                    [
                        'status' => CleanupTaskWorker::STATUS_PENDING,
                        'rejection_reason' => null,
                        'assigned_at' => now(),
                        'accepted_at' => null,
                        'started_at' => null,
                        'completed_at' => null,
                    ]
                );
            }

            // Remove any previously assigned workers not in current selection
            CleanupTaskWorker::where('task_id', $task->id)
                ->whereNotIn('worker_id', $validWorkers->pluck('id'))
                ->delete();

            // Update waste_report status to ASSIGNED
            $report->update([
                'status' => WasteReport::STATUS_ASSIGNED,
            ]);

            $workerNames = $validWorkers->pluck('name')->join(', ');
            $historyNote = "Laporan ditugaskan kepada petugas: {$workerNames}.";
            if (!empty($notes)) {
                $historyNote .= " Instruksi kerja: {$notes}";
            }

            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_ASSIGNED,
                'changed_by' => $admin->id,
                'note' => $historyNote,
            ]);

            return $task->fresh(['workers.worker', 'report']);
        });
    }

    /**
     * Worker accepts their task assignment.
     */
    public function acceptTask(CleanupTask $task, User $worker): CleanupTaskWorker
    {
        $taskWorker = CleanupTaskWorker::where('task_id', $task->id)
            ->where('worker_id', $worker->id)
            ->firstOrFail();

        if ($taskWorker->status === CleanupTaskWorker::STATUS_ACCEPTED) {
            return $taskWorker;
        }

        return DB::transaction(function () use ($task, $taskWorker, $worker) {
            $taskWorker->update([
                'status' => CleanupTaskWorker::STATUS_ACCEPTED,
                'accepted_at' => now(),
                'started_at' => now(),
            ]);

            // Update task status to IN_PROGRESS if not already
            if ($task->status === CleanupTask::STATUS_ASSIGNED || $task->status === CleanupTask::STATUS_VERIFICATION_REJECTED) {
                $task->update([
                    'status' => CleanupTask::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                ]);
            }

            // Update report status to IN_PROGRESS if not already
            $report = $task->report;
            if ($report && $report->status === WasteReport::STATUS_ASSIGNED) {
                $report->update(['status' => WasteReport::STATUS_IN_PROGRESS]);
            }

            WasteReportStatusHistory::create([
                'report_id' => $task->report_id,
                'status' => WasteReport::STATUS_IN_PROGRESS,
                'changed_by' => $worker->id,
                'note' => "Petugas {$worker->name} menerima tugas dan siap memulai pembersihan.",
            ]);

            return $taskWorker;
        });
    }

    /**
     * Worker rejects their task assignment with mandatory reason.
     */
    public function rejectTask(CleanupTask $task, User $worker, string $reason): CleanupTaskWorker
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan penolakan tugas wajib diisi.');
        }

        $taskWorker = CleanupTaskWorker::where('task_id', $task->id)
            ->where('worker_id', $worker->id)
            ->firstOrFail();

        return DB::transaction(function () use ($task, $taskWorker, $worker, $reason) {
            $taskWorker->update([
                'status' => CleanupTaskWorker::STATUS_REJECTED,
                'rejection_reason' => $reason,
            ]);

            WasteReportStatusHistory::create([
                'report_id' => $task->report_id,
                'status' => $task->report->status,
                'changed_by' => $worker->id,
                'note' => "Petugas {$worker->name} menolak tugas. Alasan: {$reason}",
            ]);

            return $taskWorker;
        });
    }

    /**
     * Upload photo evidence for a task (BEFORE or AFTER).
     */
    public function uploadTaskPhoto(
        CleanupTask $task,
        User $worker,
        UploadedFile $file,
        string $type,
        ?float $latitude = null,
        ?float $longitude = null
    ): CleanupTaskPhoto {
        $type = strtoupper($type);
        if (!in_array($type, [CleanupTaskPhoto::TYPE_BEFORE, CleanupTaskPhoto::TYPE_AFTER], true)) {
            throw new \InvalidArgumentException("Tipe foto harus 'BEFORE' atau 'AFTER'.");
        }

        // Verify worker is assigned to this task
        $taskWorker = CleanupTaskWorker::where('task_id', $task->id)
            ->where('worker_id', $worker->id)
            ->first();

        if (!$taskWorker) {
            throw new AccessDeniedHttpException('Anda tidak ditugaskan pada pekerjaan pembersihan ini.');
        }

        $meta = $this->imageStorageService->processAndStore($file, 'cleanup');

        $locationRaw = null;
        if ($latitude !== null && $longitude !== null) {
            $locationRaw = DB::raw("ST_SetSRID(ST_MakePoint({$longitude}, {$latitude}), 4326)");
        }

        $photoId = DB::table('cleanup_task_photos')->insertGetId([
            'task_id' => $task->id,
            'type' => $type,
            'storage_key' => $meta['storage_key'],
            'mime_type' => $meta['mime_type'],
            'file_size' => $meta['file_size'],
            'width' => $meta['width'],
            'height' => $meta['height'],
            'captured_at' => now(),
            'uploaded_by' => $worker->id,
            'location' => $locationRaw,
            'created_at' => now(),
        ]);

        return CleanupTaskPhoto::findOrFail($photoId);
    }

    /**
     * Worker marks their part of cleanup as completed.
     * Evaluates Invariant #3: task moves to PENDING_VERIFICATION only when
     * ALL accepted workers have marked their part completed.
     */
    public function completeTaskByWorker(CleanupTask $task, User $worker): CleanupTaskWorker
    {
        $taskWorker = CleanupTaskWorker::where('task_id', $task->id)
            ->where('worker_id', $worker->id)
            ->firstOrFail();

        if ($taskWorker->status !== CleanupTaskWorker::STATUS_ACCEPTED) {
            throw new \DomainException("Hanya tugas berstatus 'Menerima Tugas' yang dapat ditandai selesai.");
        }

        // Verify that at least 1 AFTER photo has been uploaded by the team
        $hasAfterPhoto = CleanupTaskPhoto::where('task_id', $task->id)
            ->where('type', CleanupTaskPhoto::TYPE_AFTER)
            ->exists();

        if (!$hasAfterPhoto) {
            throw ValidationException::withMessages([
                'photos' => ['Unggah minimal 1 foto bukti kondisi setelah pembersihan (AFTER) sebelum menyelesaikan tugas.'],
            ]);
        }

        return DB::transaction(function () use ($task, $taskWorker, $worker) {
            $taskWorker->update([
                'status' => CleanupTaskWorker::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // Invariant #3: Check if all ACCEPTED workers on this task are now COMPLETED
            $remainingAccepted = CleanupTaskWorker::where('task_id', $task->id)
                ->where('status', CleanupTaskWorker::STATUS_ACCEPTED)
                ->count();

            if ($remainingAccepted === 0) {
                // All accepted workers have completed their work!
                $task->update([
                    'status' => CleanupTask::STATUS_PENDING_VERIFICATION,
                    'completed_at' => now(),
                ]);

                $report = $task->report;
                if ($report) {
                    $report->update(['status' => WasteReport::STATUS_PENDING_VERIFICATION]);
                }

                WasteReportStatusHistory::create([
                    'report_id' => $task->report_id,
                    'status' => WasteReport::STATUS_PENDING_VERIFICATION,
                    'changed_by' => $worker->id,
                    'note' => 'Seluruh petugas kebersihan telah menyelesaikan pembersihan. Menunggu verifikasi Admin Desa.',
                ]);
            } else {
                WasteReportStatusHistory::create([
                    'report_id' => $task->report_id,
                    'status' => WasteReport::STATUS_IN_PROGRESS,
                    'changed_by' => $worker->id,
                    'note' => "Petugas {$worker->name} telah menyelesaikan bagian tugasnya. Menunggu petugas lainnya selesai.",
                ]);
            }

            return $taskWorker;
        });
    }

    /**
     * Admin Desa verifies cleanup result and marks report as RESOLVED (Invariants #5 & #6).
     */
    public function verifyAndResolve(CleanupTask $task, User $admin): WasteReport
    {
        $report = $task->report;
        $this->authorizeAdmin($report, $admin);

        if ($task->status !== CleanupTask::STATUS_PENDING_VERIFICATION) {
            throw new \DomainException("Hanya tugas berstatus 'Menunggu Verifikasi' yang dapat diselesaikan.");
        }

        return DB::transaction(function () use ($task, $report, $admin) {
            $task->update([
                'status' => CleanupTask::STATUS_COMPLETED,
            ]);

            $report->update([
                'status' => WasteReport::STATUS_RESOLVED,
                'resolved_at' => now(),
            ]);

            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_RESOLVED,
                'changed_by' => $admin->id,
                'note' => 'Hasil pembersihan telah diverifikasi dan disetujui oleh Admin Desa. Laporan dinyatakan selesai (RESOLVED).',
            ]);

            return $report->fresh(['cleanupTask.workers.worker', 'cleanupTask.photos', 'statusHistories']);
        });
    }

    /**
     * Admin Desa rejects cleanup result (insufficient cleaning) and requests re-work.
     */
    public function rejectVerification(CleanupTask $task, User $admin, string $notes): CleanupTask
    {
        $report = $task->report;
        $this->authorizeAdmin($report, $admin);

        if ($task->status !== CleanupTask::STATUS_PENDING_VERIFICATION) {
            throw new \DomainException("Hanya tugas berstatus 'Menunggu Verifikasi' yang dapat ditolak.");
        }

        if (trim($notes) === '') {
            throw new \InvalidArgumentException('Catatan kekurangan/alasan perbaikan wajib diisi.');
        }

        return DB::transaction(function () use ($task, $report, $admin, $notes) {
            $task->update([
                'status' => CleanupTask::STATUS_VERIFICATION_REJECTED,
                'notes' => $notes,
            ]);

            // Re-open COMPLETED workers back to ACCEPTED so they can continue working
            CleanupTaskWorker::where('task_id', $task->id)
                ->where('status', CleanupTaskWorker::STATUS_COMPLETED)
                ->update(['status' => CleanupTaskWorker::STATUS_ACCEPTED]);

            $report->update([
                'status' => WasteReport::STATUS_IN_PROGRESS,
            ]);

            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_IN_PROGRESS,
                'changed_by' => $admin->id,
                'note' => "Hasil pembersihan belum memenuhi standar. Petugas diminta melakukan pembersihan ulang. Catatan: {$notes}",
            ]);

            return $task->fresh(['workers.worker', 'report']);
        });
    }

    /**
     * Security check: ensure admin has jurisdiction over the report's village.
     */
    protected function authorizeAdmin(WasteReport $report, User $admin): void
    {
        $roleName = $admin->role?->name;

        if ($roleName === 'super_admin_kecamatan') {
            return;
        }

        if ($roleName === 'admin_desa') {
            if ((int) $admin->village_id !== (int) $report->village_id) {
                throw new AccessDeniedHttpException('Anda tidak berwenang mengelola pekerjaan di luar kelurahan Anda.');
            }
            return;
        }

        throw new AccessDeniedHttpException('Role Anda tidak memiliki izin untuk mengelola pekerjaan pembersihan.');
    }
}
