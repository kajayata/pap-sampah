<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CleanupTask;
use App\Services\CleanupTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorkerTaskController extends Controller
{
    public function __construct(
        protected CleanupTaskService $cleanupTaskService
    ) {}

    /**
     * Get tasks assigned to the authenticated cleaning worker.
     */
    public function index(Request $request): JsonResponse
    {
        $worker = $request->user();

        $query = CleanupTask::with([
            'report' => function ($q) {
                $q->withCoordinates()->with(['category', 'village', 'photos']);
            },
            'assignedBy:id,name,email',
            'workers.worker:id,name,phone',
            'photos',
        ])
        ->whereHas('workers', function ($q) use ($worker) {
            $q->where('worker_id', $worker->id);
        });

        // Filter by worker status if requested
        if ($request->filled('status') && $request->input('status') !== 'ALL') {
            $status = strtoupper($request->input('status'));
            $query->whereHas('workers', function ($q) use ($worker, $status) {
                $q->where('worker_id', $worker->id)->where('status', $status);
            });
        }

        $tasks = $query->orderByDesc('assigned_at')->paginate(15);

        // Transform collection to include the worker's own status directly
        $items = collect($tasks->items())->map(function ($task) use ($worker) {
            $myWorkerRecord = $task->workers->firstWhere('worker_id', $worker->id);
            return [
                'id' => $task->id,
                'status' => $task->status,
                'status_label' => $task->status_label,
                'notes' => $task->notes,
                'assigned_at' => $task->assigned_at,
                'started_at' => $task->started_at,
                'completed_at' => $task->completed_at,
                'my_status' => $myWorkerRecord?->status,
                'my_status_label' => $myWorkerRecord?->status_label,
                'my_rejection_reason' => $myWorkerRecord?->rejection_reason,
                'assigned_by' => $task->assignedBy,
                'report' => $task->report,
                'workers' => $task->workers,
                'photos_count' => $task->photos->count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Get detail of a specific cleanup task.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $worker = $request->user();

        $task = CleanupTask::with([
            'report' => function ($q) {
                $q->withCoordinates()->with(['category', 'village', 'photos', 'statusHistories.user']);
            },
            'assignedBy:id,name,phone',
            'workers.worker:id,name,phone',
            'photos.uploader:id,name',
        ])
        ->find($id);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tugas pembersihan tidak ditemukan.',
            ], 404);
        }

        // Verify assignment
        $myAssignment = $task->workers->firstWhere('worker_id', $worker->id);
        if (!$myAssignment && $worker->role?->name !== 'super_admin_kecamatan') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak ditugaskan pada pekerjaan ini.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'task' => $task,
                'my_assignment' => $myAssignment,
            ],
        ]);
    }

    /**
     * Worker accepts assignment.
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $task = CleanupTask::findOrFail($id);

        try {
            $assignment = $this->cleanupTaskService->acceptTask($task, $request->user());

            return response()->json([
                'status' => 'success',
                'message' => 'Tugas pembersihan berhasil diterima. Silakan bersiap dan mulai pembersihan.',
                'data' => $assignment,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Worker rejects assignment with mandatory reason.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan tugas wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal 5 karakter.',
            'reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $task = CleanupTask::findOrFail($id);

        try {
            $assignment = $this->cleanupTaskService->rejectTask($task, $request->user(), $request->input('reason'));

            return response()->json([
                'status' => 'success',
                'message' => 'Penolakan tugas berhasil dikirim.',
                'data' => $assignment,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Upload before/after photo evidence.
     */
    public function uploadPhoto(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:BEFORE,AFTER,before,after'],
            'photo' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'type.required' => 'Tipe foto (BEFORE atau AFTER) wajib ditentukan.',
            'type.in' => 'Tipe foto harus BEFORE atau AFTER.',
            'photo.required' => 'File foto wajib diunggah.',
            'photo.image' => 'File harus berupa format gambar valid.',
            'photo.max' => 'Ukuran foto maksimal 10MB.',
        ]);

        $task = CleanupTask::findOrFail($id);

        try {
            $photo = $this->cleanupTaskService->uploadTaskPhoto(
                task: $task,
                worker: $request->user(),
                file: $request->file('photo'),
                type: $request->input('type'),
                latitude: $request->filled('latitude') ? (float) $request->input('latitude') : null,
                longitude: $request->filled('longitude') ? (float) $request->input('longitude') : null
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Foto bukti pembersihan berhasil diunggah.',
                'data' => $photo,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Worker marks their assignment as completed.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $task = CleanupTask::findOrFail($id);

        try {
            $assignment = $this->cleanupTaskService->completeTaskByWorker($task, $request->user());

            return response()->json([
                'status' => 'success',
                'message' => 'Bagian tugas Anda berhasil ditandai selesai.',
                'data' => $assignment,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
