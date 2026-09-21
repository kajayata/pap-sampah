import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../models/waste_report.dart';
import '../../services/report_service.dart';

class ReportDetailScreen extends StatefulWidget {
  final int reportId;

  const ReportDetailScreen({super.key, required this.reportId});

  @override
  State<ReportDetailScreen> createState() => _ReportDetailScreenState();
}

class _ReportDetailScreenState extends State<ReportDetailScreen> {
  WasteReport? _report;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  Future<void> _fetchDetail() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final rep = await ReportService.getReportDetail(widget.reportId);
      setState(() {
        _report = rep;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'PENDING_VALIDATION':
        return AppTheme.accentWarning;
      case 'VALIDATED':
      case 'ASSIGNED':
      case 'IN_PROGRESS':
        return const Color(0xFF0D9488);
      case 'PENDING_VERIFICATION':
        return const Color(0xFF7C3AED);
      case 'RESOLVED':
        return AppTheme.emphasis;
      case 'REJECTED':
        return AppTheme.accentDanger;
      default:
        return AppTheme.textMuted;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_report != null ? 'Laporan ${_report!.reportCode}' : 'Detail Laporan'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.error_outline_rounded, size: 48, color: AppTheme.accentDanger),
                        const SizedBox(height: 12),
                        Text(_error!, textAlign: TextAlign.center),
                        const SizedBox(height: 16),
                        ElevatedButton(onPressed: _fetchDetail, child: const Text('Coba Lagi')),
                      ],
                    ),
                  ),
                )
              : _buildContent(),
    );
  }

  Widget _buildContent() {
    final rep = _report!;
    final statusColor = _getStatusColor(rep.status);

    return ListView(
      padding: const EdgeInsets.all(20),
      children: [
        // Status & Code Card
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppTheme.borderDefault),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    rep.reportCode,
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, fontFamily: 'monospace'),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: statusColor.withValues(alpha: 0.3)),
                    ),
                    child: Text(
                      rep.statusLabel,
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: statusColor),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                'Wilayah: Kelurahan ${rep.villageName ?? 'Sumbersari'}',
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  const Icon(Icons.calendar_today_outlined, size: 13, color: AppTheme.textMuted),
                  const SizedBox(width: 4),
                  Text(
                    rep.createdAt != null ? DateFormat('d MMMM yyyy, HH:mm').format(rep.createdAt!) : '-',
                    style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                  ),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        // Photos Section
        const Text('Foto Kondisi Sampah', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),

        if (rep.photos.isNotEmpty)
          SizedBox(
            height: 200,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: rep.photos.length,
              separatorBuilder: (context, index) => const SizedBox(width: 10),
              itemBuilder: (ctx, idx) {
                final photo = rep.photos[idx];
                return ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: Image.network(
                    photo.url,
                    height: 200,
                    width: 260,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => Container(
                      height: 200,
                      width: 260,
                      color: Colors.grey.shade100,
                      child: const Center(child: Icon(Icons.broken_image_rounded)),
                    ),
                  ),
                );
              },
            ),
          )
        else
          Container(
            height: 120,
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Center(child: Text('Tidak ada foto laporan', style: TextStyle(color: AppTheme.textMuted))),
          ),
        const SizedBox(height: 20),

        // If Resolved: Show Cleanup After Photos
        if (rep.isResolved && rep.cleanupPhotos.isNotEmpty) ...[
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFFF0FDF4),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: const Color(0xFF86EFAC)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Row(
                  children: [
                    Icon(Icons.check_circle_rounded, color: AppTheme.emphasis, size: 20),
                    SizedBox(width: 8),
                    Text(
                      'Hasil Pembersihan (Petugas)',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.emphasis),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                SizedBox(
                  height: 160,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    itemCount: rep.cleanupPhotos.length,
                    separatorBuilder: (context, index) => const SizedBox(width: 10),
                    itemBuilder: (ctx, idx) {
                      return ClipRRect(
                        borderRadius: BorderRadius.circular(14),
                        child: Image.network(
                          rep.cleanupPhotos[idx].url,
                          height: 160,
                          width: 220,
                          fit: BoxFit.cover,
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
        ],

        // Description Card
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppTheme.borderDefault),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Keterangan / Patokan Lokasi', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
              const SizedBox(height: 6),
              Text(
                rep.description != null && rep.description!.isNotEmpty
                    ? rep.description!
                    : 'Tidak ada keterangan tambahan.',
                style: const TextStyle(fontSize: 13, color: AppTheme.textPrimary, height: 1.4),
              ),
              const SizedBox(height: 12),
              if (rep.latitude != null && rep.longitude != null)
                Row(
                  children: [
                    const Icon(Icons.pin_drop_outlined, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 4),
                    Text(
                      'Koordinat: ${rep.latitude!.toStringAsFixed(5)}, ${rep.longitude!.toStringAsFixed(5)}',
                      style: const TextStyle(fontSize: 11, fontFamily: 'monospace', color: AppTheme.textMuted),
                    ),
                  ],
                ),
            ],
          ),
        ),
        const SizedBox(height: 20),

        // Timeline Riwayat Status
        const Text('Riwayat Penanganan', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        const SizedBox(height: 10),

        if (rep.statusHistories.isNotEmpty)
          ...rep.statusHistories.map((h) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 10,
                    height: 10,
                    margin: const EdgeInsets.only(top: 4),
                    decoration: const BoxDecoration(
                      color: AppTheme.emphasis,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          h.toStatus,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                        ),
                        if (h.notes != null)
                          Text(
                            h.notes!,
                            style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                          ),
                        if (h.createdAt != null)
                          Text(
                            DateFormat('dd MMM yyyy, HH:mm').format(h.createdAt!),
                            style: const TextStyle(fontSize: 10, color: AppTheme.textMuted),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
            );
          })
        else
          const Text('Belum ada riwayat perubahan status.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
      ],
    );
  }
}
