import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/theme/app_theme.dart';
import '../../models/cleanup_task.dart';
import '../../providers/worker_task_provider.dart';

class WorkerTaskDetailScreen extends StatefulWidget {
  final int taskId;

  const WorkerTaskDetailScreen({super.key, required this.taskId});

  @override
  State<WorkerTaskDetailScreen> createState() => _WorkerTaskDetailScreenState();
}

class _WorkerTaskDetailScreenState extends State<WorkerTaskDetailScreen> {
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<WorkerTaskProvider>(context, listen: false).loadTaskDetail(widget.taskId);
    });
  }

  Future<void> _handleAccept() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Terima Tugas Pembersihan'),
        content: const Text('Apakah Anda siap menerima tugas ini dan membawa peralatan yang diinstruksikan?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryColor),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Terima Tugas'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final prov = Provider.of<WorkerTaskProvider>(context, listen: false);
      final success = await prov.acceptTask(widget.taskId);
      if (mounted) {
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Tugas berhasil diterima! Silakan mulai pembersihan.'),
              backgroundColor: AppTheme.emphasis,
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(prov.errorMessage ?? 'Gagal menerima tugas.'),
              backgroundColor: AppTheme.accentDanger,
            ),
          );
        }
      }
    }
  }

  Future<void> _handleReject() async {
    final reasonCtrl = TextEditingController();
    final formKey = GlobalKey<FormState>();

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Tolak Tugas Pembersihan'),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Mohon berikan alasan penolakan agar admin desa dapat menugaskan petugas lain:',
                style: TextStyle(fontSize: 13, color: AppTheme.textMuted),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: reasonCtrl,
                maxLines: 3,
                decoration: const InputDecoration(
                  hintText: 'Contoh: Sedang menangani titik lain / sakit...',
                  border: OutlineInputBorder(),
                ),
                validator: (val) {
                  if (val == null || val.trim().length < 5) {
                    return 'Alasan penolakan minimal 5 karakter';
                  }
                  return null;
                },
              ),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.accentDanger),
            onPressed: () {
              if (formKey.currentState?.validate() == true) {
                Navigator.pop(ctx, true);
              }
            },
            child: const Text('Kirim Penolakan'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      final prov = Provider.of<WorkerTaskProvider>(context, listen: false);
      final success = await prov.rejectTask(widget.taskId, reasonCtrl.text.trim());
      if (mounted) {
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Penolakan tugas berhasil dikirim.'),
              backgroundColor: AppTheme.accentDanger,
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(prov.errorMessage ?? 'Gagal menolak tugas.'),
              backgroundColor: AppTheme.accentDanger,
            ),
          );
        }
      }
    }
  }

  Future<void> _pickAndUploadPhoto() async {
    String selectedType = 'AFTER';

    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Unggah Bukti Pembersihan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 12),
              const Text('Pilih jenis foto bukti:', style: TextStyle(fontSize: 13, color: AppTheme.textMuted)),
              const SizedBox(height: 8),
              Row(
                children: [
                  ChoiceChip(
                    label: const Text('Foto SEBELUM (Before)'),
                    selected: selectedType == 'BEFORE',
                    onSelected: (val) {
                      if (val) setSheetState(() => selectedType = 'BEFORE');
                    },
                  ),
                  const SizedBox(width: 8),
                  ChoiceChip(
                    label: const Text('Foto SESUDAH (After)'),
                    selected: selectedType == 'AFTER',
                    onSelected: (val) {
                      if (val) setSheetState(() => selectedType = 'AFTER');
                    },
                  ),
                ],
              ),
              const SizedBox(height: 20),
              ListTile(
                leading: const Icon(Icons.camera_alt_rounded, color: AppTheme.primaryColor),
                title: const Text('Buka Kamera HP'),
                onTap: () => Navigator.pop(ctx, ImageSource.camera),
              ),
              ListTile(
                leading: const Icon(Icons.photo_library_rounded, color: AppTheme.primaryColor),
                title: const Text('Pilih dari Galeri'),
                onTap: () => Navigator.pop(ctx, ImageSource.gallery),
              ),
            ],
          ),
        ),
      ),
    );

    if (source == null) return;

    try {
      final picked = await _picker.pickImage(source: source, imageQuality: 80);
      if (picked == null) return;

      double? lat;
      double? lng;
      try {
        final pos = await Geolocator.getCurrentPosition();
        lat = pos.latitude;
        lng = pos.longitude;
      } catch (_) {}

      if (mounted) {
        final prov = Provider.of<WorkerTaskProvider>(context, listen: false);
        final ok = await prov.uploadPhoto(
          taskId: widget.taskId,
          photoFile: File(picked.path),
          type: selectedType,
          latitude: lat,
          longitude: lng,
        );

        if (mounted) {
          if (ok) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Foto bukti $selectedType berhasil diunggah!'),
                backgroundColor: AppTheme.emphasis,
              ),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(prov.errorMessage ?? 'Gagal mengunggah foto.'),
                backgroundColor: AppTheme.accentDanger,
              ),
            );
          }
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: AppTheme.accentDanger),
        );
      }
    }
  }

  Future<void> _handleComplete(CleanupTask task) async {
    // Invariant #3: Check if at least 1 AFTER photo exists
    if (task.afterPhotos.isEmpty) {
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.warning_amber_rounded, color: AppTheme.accentWarning),
              SizedBox(width: 8),
              Text('Foto AFTER Wajib'),
            ],
          ),
          content: const Text(
            'Sesuai SOP Kebersihan, Anda wajib mengunggah minimal 1 foto bukti SESUDAH (AFTER) pembersihan sebelum menandai tugas selesai.',
          ),
          actions: [
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryColor),
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Mengerti'),
            ),
          ],
        ),
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Selesaikan Tugas'),
        content: const Text('Apakah pembersihan telah tuntas dan Anda yakin ingin menandai tugas ini selesai?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryColor),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Ya, Selesai'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final prov = Provider.of<WorkerTaskProvider>(context, listen: false);
      final success = await prov.completeTask(widget.taskId);
      if (mounted) {
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Selamat! Tugas pembersihan berhasil diselesaikan.'),
              backgroundColor: AppTheme.emphasis,
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(prov.errorMessage ?? 'Gagal menyelesaikan tugas.'),
              backgroundColor: AppTheme.accentDanger,
            ),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<WorkerTaskProvider>(
      builder: (context, prov, child) {
        final task = prov.currentTask;

        return Scaffold(
          appBar: AppBar(
            title: Text(
              task != null ? 'Tugas #${task.id}' : 'Detail Tugas',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
            ),
            actions: [
              IconButton(
                icon: const Icon(Icons.refresh_rounded),
                onPressed: () => prov.loadTaskDetail(widget.taskId),
              ),
            ],
          ),
          body: prov.isLoading && task == null
              ? const Center(child: CircularProgressIndicator(color: AppTheme.primaryColor))
              : task == null
                  ? Center(
                      child: Text(
                        prov.errorMessage ?? 'Tugas tidak ditemukan.',
                        style: const TextStyle(color: AppTheme.textMuted),
                      ),
                    )
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Status Header Card
                          _buildStatusHeaderCard(task),
                          const SizedBox(height: 16),

                          // INSTRUCTION BOX FOR EQUIPMENT FROM VILLAGE ADMIN
                          _buildEquipmentInstructionCard(task),
                          const SizedBox(height: 20),

                          // Waste Report Information
                          _buildReportInfoSection(task),
                          const SizedBox(height: 20),

                          // Cleanup Photos Section
                          _buildCleanupPhotosSection(task),
                          const SizedBox(height: 24),

                          // Assigned Team Workers
                          _buildWorkerTeamSection(task),
                          const SizedBox(height: 32),

                          // Action Buttons
                          _buildActionButtons(task, prov.isActionLoading),
                          const SizedBox(height: 24),
                        ],
                      ),
                    ),
        );
      },
    );
  }

  Widget _buildStatusHeaderCard(CleanupTask task) {
    Color myStatusColor = const Color(0xFFD97706);
    if (task.isMyAssignmentAccepted) myStatusColor = const Color(0xFF2563EB);
    if (task.isMyAssignmentCompleted) myStatusColor = AppTheme.emphasis;
    if (task.isMyAssignmentRejected) myStatusColor = AppTheme.accentDanger;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Status Tugas', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                  const SizedBox(height: 2),
                  Text(
                    task.statusLabel,
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: myStatusColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: myStatusColor.withValues(alpha: 0.3)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    CircleAvatar(radius: 4, backgroundColor: myStatusColor),
                    const SizedBox(width: 6),
                    Text(
                      'Respon Saya: ${task.myStatusLabel}',
                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: myStatusColor),
                    ),
                  ],
                ),
              ),
            ],
          ),
          if (task.myRejectionReason != null && task.myRejectionReason!.isNotEmpty) ...[
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: const Color(0xFFFEF2F2),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFFECACA)),
              ),
              child: Text(
                'Alasan penolakan: ${task.myRejectionReason}',
                style: const TextStyle(fontSize: 12, color: AppTheme.accentDanger),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildEquipmentInstructionCard(CleanupTask task) {
    final hasNotes = task.notes != null && task.notes!.trim().isNotEmpty;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFFFFFBEB), // Warm yellow light
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFFDE68A), width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              CircleAvatar(
                radius: 14,
                backgroundColor: Color(0xFFF59E0B),
                child: Icon(Icons.handyman_rounded, color: Colors.white, size: 16),
              ),
              SizedBox(width: 10),
              Text(
                'Instruksi Peralatan & Catatan Admin',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF92400E),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            hasNotes ? task.notes! : 'Tidak ada instruksi peralatan khusus dari admin desa.',
            style: TextStyle(
              fontSize: 14,
              height: 1.5,
              fontWeight: hasNotes ? FontWeight.w600 : FontWeight.normal,
              color: hasNotes ? const Color(0xFF78350F) : AppTheme.textMuted,
            ),
          ),
          if (task.assignedByName != null) ...[
            const SizedBox(height: 8),
            Text(
              'Instruksi dari: ${task.assignedByName!}',
              style: const TextStyle(fontSize: 11, color: Color(0xFFB45309), fontStyle: FontStyle.italic),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildReportInfoSection(CleanupTask task) {
    final rep = task.report;
    if (rep == null) return const SizedBox();

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Lokasi & Laporan Sampah', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          const SizedBox(height: 12),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.location_on_rounded, color: AppTheme.primaryColor, size: 20),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      rep.villageName != null ? 'Kelurahan ${rep.villageName!}' : 'Kecamatan Sumbersari',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                    Text(
                      'Kode: ${rep.reportCode} • ${rep.category?.name ?? 'Sampah'}',
                      style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                    ),
                  ],
                ),
              ),
            ],
          ),

          // GPS Coordinates + Maps Button
          if (rep.latitude != null && rep.longitude != null) ...[
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFF0FDF4),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: const Color(0xFFBBF7D0)),
              ),
              child: Row(
                children: [
                  const Icon(Icons.my_location_rounded, size: 18, color: AppTheme.primaryColor),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Koordinat GPS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.primaryColor)),
                        Text(
                          '${rep.latitude!.toStringAsFixed(6)}, ${rep.longitude!.toStringAsFixed(6)}',
                          style: const TextStyle(fontSize: 12, fontFamily: 'monospace', color: AppTheme.textDark),
                        ),
                      ],
                    ),
                  ),
                  // Copy coordinates
                  IconButton(
                    icon: const Icon(Icons.copy_rounded, size: 16, color: AppTheme.textMuted),
                    tooltip: 'Salin koordinat',
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
                    onPressed: () {
                      Clipboard.setData(
                        ClipboardData(text: '${rep.latitude}, ${rep.longitude}'),
                      );
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Koordinat disalin ke clipboard'),
                          duration: Duration(seconds: 2),
                        ),
                      );
                    },
                  ),
                  // Open in Google Maps
                  OutlinedButton.icon(
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppTheme.primaryColor,
                      side: const BorderSide(color: AppTheme.primaryColor),
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    onPressed: () async {
                      final lat = rep.latitude!;
                      final lng = rep.longitude!;
                      // Try Google Maps app first, fallback to web
                      final geoUri = Uri.parse('geo:$lat,$lng?q=$lat,$lng(Titik Sampah)');
                      final mapsUri = Uri.parse('https://maps.google.com/?q=$lat,$lng');
                      if (await canLaunchUrl(geoUri)) {
                        await launchUrl(geoUri);
                      } else if (await canLaunchUrl(mapsUri)) {
                        await launchUrl(mapsUri, mode: LaunchMode.externalApplication);
                      } else {
                        await Clipboard.setData(ClipboardData(text: 'https://maps.google.com/?q=$lat,$lng'));
                        if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Link Google Maps disalin ke clipboard')),
                          );
                        }
                      }
                    },
                    icon: const Icon(Icons.map_rounded, size: 14),
                    label: const Text('Buka Peta', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
          ],

          if (rep.description != null && rep.description!.isNotEmpty) ...[
            const SizedBox(height: 12),
            Text(rep.description!, style: const TextStyle(fontSize: 13, color: Color(0xFF4B5563))),
          ],
          if (rep.photos.isNotEmpty) ...[
            const SizedBox(height: 14),
            const Text('Foto Lokasi dari Warga (Before):', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            SizedBox(
              height: 120,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                itemCount: rep.photos.length,
                separatorBuilder: (context, index) => const SizedBox(width: 8),
                itemBuilder: (context, index) {
                  return ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.network(
                      rep.photos[index].url,
                      height: 120,
                      width: 160,
                      fit: BoxFit.cover,
                    ),
                  );
                },
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildCleanupPhotosSection(CleanupTask task) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Foto Hasil Kerja Petugas', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              if (task.isMyAssignmentAccepted && !task.isTaskCompleted)
                ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryColor,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: _pickAndUploadPhoto,
                  icon: const Icon(Icons.add_a_photo_rounded, size: 16),
                  label: const Text('Unggah Foto', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                ),
            ],
          ),
          const SizedBox(height: 12),
          if (task.photos.isEmpty)
            Container(
              height: 100,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: const Center(
                child: Text('Belum ada foto bukti pembersihan dari petugas', style: TextStyle(color: AppTheme.textMuted, fontSize: 12)),
              ),
            )
          else
            SizedBox(
              height: 140,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                itemCount: task.photos.length,
                separatorBuilder: (context, index) => const SizedBox(width: 10),
                itemBuilder: (context, index) {
                  final photo = task.photos[index];
                  return Stack(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(14),
                        child: Image.network(
                          photo.url,
                          height: 140,
                          width: 180,
                          fit: BoxFit.cover,
                        ),
                      ),
                      Positioned(
                        top: 8,
                        left: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: photo.isAfter ? AppTheme.emphasis : const Color(0xFFD97706),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            photo.type.toUpperCase(),
                            style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ),
                    ],
                  );
                },
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildWorkerTeamSection(CleanupTask task) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Tim Petugas Kebersihan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          const SizedBox(height: 10),
          ...task.workers.map((w) {
            return Padding(
              padding: const EdgeInsets.symmetric(vertical: 4),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 14,
                    backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.1),
                    child: const Icon(Icons.person, size: 16, color: AppTheme.primaryColor),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(w.workerName, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(w.statusLabel, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildActionButtons(CleanupTask task, bool isLoading) {
    if (isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.primaryColor));
    }

    if (task.isMyAssignmentPending) {
      return Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              style: OutlinedButton.styleFrom(
                foregroundColor: AppTheme.accentDanger,
                side: const BorderSide(color: AppTheme.accentDanger),
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              onPressed: _handleReject,
              icon: const Icon(Icons.close_rounded, size: 18),
              label: const Text('Tolak Tugas', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryColor,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              onPressed: _handleAccept,
              icon: const Icon(Icons.check_rounded, size: 18),
              label: const Text('Terima Tugas', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      );
    }

    if (task.isMyAssignmentAccepted && !task.isTaskCompleted) {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          style: ElevatedButton.styleFrom(
            backgroundColor: AppTheme.emphasis,
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          ),
          onPressed: () => _handleComplete(task),
          icon: const Icon(Icons.done_all_rounded, size: 20),
          label: const Text('Selesaikan Tugas Pembersihan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
        ),
      );
    }

    return const SizedBox();
  }
}
