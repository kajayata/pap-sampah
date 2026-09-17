import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../providers/report_provider.dart';
import 'report_detail_screen.dart';

class ReportListScreen extends StatefulWidget {
  const ReportListScreen({super.key});

  @override
  State<ReportListScreen> createState() => _ReportListScreenState();
}

class _ReportListScreenState extends State<ReportListScreen> {
  final List<Map<String, String>> _statusTabs = [
    {'key': 'ALL', 'label': 'Semua'},
    {'key': 'PENDING_VALIDATION', 'label': 'Menunggu'},
    {'key': 'VALIDATED', 'label': 'Tervalidasi'},
    {'key': 'IN_PROGRESS', 'label': 'Diproses'},
    {'key': 'RESOLVED', 'label': 'Selesai'},
    {'key': 'REJECTED', 'label': 'Ditolak'},
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReportProvider>().loadReports();
    });
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
    final provider = context.watch<ReportProvider>();
    final reports = provider.reports;
    final isLoading = provider.isLoading;
    final currentFilter = provider.currentStatusFilter;

    return Scaffold(
      body: Column(
        children: [
          // Filter Chips Scrollable
          Container(
            height: 48,
            margin: const EdgeInsets.only(top: 8, bottom: 4),
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              scrollDirection: Axis.horizontal,
              itemCount: _statusTabs.length,
              separatorBuilder: (ctx, i) => const SizedBox(width: 8),
              itemBuilder: (ctx, idx) {
                final tab = _statusTabs[idx];
                final isSelected = currentFilter == tab['key'];
                return ChoiceChip(
                  label: Text(
                    tab['label']!,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      color: isSelected ? Colors.white : AppTheme.textPrimary,
                    ),
                  ),
                  selected: isSelected,
                  selectedColor: AppTheme.emphasis,
                  backgroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                    side: BorderSide(
                      color: isSelected ? AppTheme.emphasis : AppTheme.borderDefault,
                    ),
                  ),
                  showCheckmark: false,
                  onSelected: (val) {
                    if (val) {
                      provider.loadReports(status: tab['key']!, refresh: true);
                    }
                  },
                );
              },
            ),
          ),

          // Report List
          Expanded(
            child: RefreshIndicator(
              onRefresh: () => provider.loadReports(refresh: true),
              child: isLoading && reports.isEmpty
                  ? const Center(child: CircularProgressIndicator())
                  : reports.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.inbox_outlined, size: 54, color: AppTheme.textMuted.withValues(alpha: 0.5)),
                              const SizedBox(height: 12),
                              const Text('Belum ada laporan sampah', style: TextStyle(fontWeight: FontWeight.bold)),
                              const SizedBox(height: 4),
                              const Text(
                                'Laporan yang Anda kirimkan akan muncul di sini.',
                                style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
                              ),
                            ],
                          ),
                        )
                      : ListView.separated(
                          padding: const EdgeInsets.all(16),
                          itemCount: reports.length,
                          separatorBuilder: (ctx, i) => const SizedBox(height: 12),
                          itemBuilder: (ctx, idx) {
                            final rep = reports[idx];
                            final statusColor = _getStatusColor(rep.status);
                            final photoUrl = rep.primaryPhotoUrl;

                            return GestureDetector(
                              onTap: () {
                                Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) => ReportDetailScreen(reportId: rep.id),
                                  ),
                                );
                              },
                              child: Container(
                                padding: const EdgeInsets.all(14),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(20),
                                  border: Border.all(color: AppTheme.borderDefault),
                                  boxShadow: const [
                                    BoxShadow(
                                      color: Color(0x05000000),
                                      blurRadius: 10,
                                      offset: Offset(0, 4),
                                    ),
                                  ],
                                ),
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Thumbnail Photo
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(14),
                                      child: photoUrl != null && photoUrl.isNotEmpty
                                          ? Image.network(
                                              photoUrl,
                                              width: 74,
                                              height: 74,
                                              fit: BoxFit.cover,
                                              errorBuilder: (c, e, s) => Container(
                                                width: 74,
                                                height: 74,
                                                color: Colors.grey.shade100,
                                                child: const Icon(Icons.broken_image_rounded, size: 28, color: Colors.grey),
                                              ),
                                            )
                                          : Container(
                                              width: 74,
                                              height: 74,
                                              color: Colors.grey.shade100,
                                              child: const Icon(Icons.image_outlined, size: 28, color: Colors.grey),
                                            ),
                                    ),
                                    const SizedBox(width: 14),

                                    // Info
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                rep.reportCode,
                                                style: const TextStyle(
                                                  fontSize: 12,
                                                  fontWeight: FontWeight.bold,
                                                  fontFamily: 'monospace',
                                                ),
                                              ),
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                                decoration: BoxDecoration(
                                                  color: statusColor.withValues(alpha: 0.12),
                                                  borderRadius: BorderRadius.circular(8),
                                                  border: Border.all(color: statusColor.withValues(alpha: 0.3)),
                                                ),
                                                child: Text(
                                                  rep.statusLabel,
                                                  style: TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: statusColor,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 6),
                                          Text(
                                            'Kelurahan ${rep.villageName ?? 'Sumbersari'}',
                                            style: const TextStyle(
                                              fontSize: 13,
                                              fontWeight: FontWeight.bold,
                                              color: AppTheme.textPrimary,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Row(
                                            children: [
                                              if (rep.category != null) ...[
                                                Text(
                                                  rep.category!.name,
                                                  style: const TextStyle(fontSize: 11, color: AppTheme.emphasis, fontWeight: FontWeight.w600),
                                                ),
                                                const SizedBox(width: 6),
                                                const Text('•', style: TextStyle(color: AppTheme.textMuted, fontSize: 10)),
                                                const SizedBox(width: 6),
                                              ],
                                              Text(
                                                rep.createdAt != null
                                                    ? DateFormat('dd MMM, HH:mm').format(rep.createdAt!)
                                                    : '',
                                                style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
            ),
          ),
        ],
      ),
    );
  }
}
