import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/cleanup_task.dart';
import '../../providers/worker_task_provider.dart';
import '../auth/auth_widgets.dart';
import 'worker_task_detail_screen.dart';

const _pageBg = Color(0xFFF4F6F2);
const _cardBorder = Color(0xFFE4EAE4);

class WorkerHistoryTab extends StatelessWidget {
  const WorkerHistoryTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _pageBg,
      body: Consumer<WorkerTaskProvider>(
        builder: (context, prov, child) {
          final List<CleanupTask> taskList = prov.tasks
              .where((t) => t.isMyAssignmentCompleted || t.isTaskCompleted)
              .toList();

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Sub-header banner sederhana
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 52, 20, 16),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  border: Border(bottom: BorderSide(color: _cardBorder)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.history_rounded, size: 20, color: AuthColors.green),
                    const SizedBox(width: 8),
                    const Text(
                      'Riwayat Tugas Selesai',
                      style: TextStyle(fontWeight: FontWeight.w800, fontSize: 17, color: AuthColors.ink),
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AuthColors.field,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        '${taskList.length}',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: AuthColors.green),
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: prov.isLoading
                    ? const Center(child: CircularProgressIndicator(color: AuthColors.green))
                    : taskList.isEmpty
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  width: 52,
                                  height: 52,
                                  decoration: BoxDecoration(
                                    color: AuthColors.green.withValues(alpha: 0.1),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(Icons.history_edu_rounded, size: 28, color: AuthColors.green),
                                ),
                                const SizedBox(height: 12),
                                const Text(
                                  'Belum ada riwayat tugas selesai',
                                  style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w700, color: AuthColors.ink),
                                ),
                                const SizedBox(height: 4),
                                const Text(
                                  'Tugas yang telah selesai akan muncul di sini.',
                                  style: TextStyle(fontSize: 12, color: AuthColors.muted),
                                ),
                              ],
                            ),
                          )
                        : RefreshIndicator(
                            color: AuthColors.green,
                            onRefresh: () => prov.loadTasks(refresh: true),
                            child: ListView.separated(
                              padding: const EdgeInsets.all(20),
                              itemCount: taskList.length,
                              separatorBuilder: (context, index) => const SizedBox(height: 12),
                              itemBuilder: (context, index) {
                                final task = taskList[index];
                                return _buildHistoryCard(context, task);
                              },
                            ),
                          ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildHistoryCard(BuildContext context, CleanupTask task) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: _cardBorder),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Tugas #${task.id}',
                style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AuthColors.ink),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFFE2F4E8),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Selesai',
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AuthColors.green),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          if (task.report != null) ...[
            Row(
              children: [
                const Icon(Icons.location_on_rounded, size: 16, color: AuthColors.green),
                const SizedBox(width: 4),
                Text(
                  task.report!.villageName != null ? 'Kelurahan ${task.report!.villageName!}' : 'Kecamatan Sumbersari',
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AuthColors.ink),
                ),
                const Spacer(),
                Text(
                  'Kode: ${task.report!.reportCode}',
                  style: const TextStyle(fontSize: 11, color: AuthColors.muted),
                ),
              ],
            ),
            const SizedBox(height: 8),
          ],
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${task.photos.length} foto bukti diunggah',
                style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
              ),
              OutlinedButton(
                style: OutlinedButton.styleFrom(
                  foregroundColor: AuthColors.green,
                  side: const BorderSide(color: _cardBorder),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                ),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => WorkerTaskDetailScreen(taskId: task.id)),
                  );
                },
                child: const Text('Lihat Hasil', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
              ),
            ],
          ),
        ],
      ),
    );
  }
}