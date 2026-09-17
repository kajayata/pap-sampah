import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/cleanup_task.dart';
import '../../providers/auth_provider.dart';
import '../../providers/worker_task_provider.dart';
import '../auth/login_screen.dart';
import 'worker_task_detail_screen.dart';

class WorkerMainScreen extends StatefulWidget {
  const WorkerMainScreen({super.key});

  @override
  State<WorkerMainScreen> createState() => _WorkerMainScreenState();
}

class _WorkerMainScreenState extends State<WorkerMainScreen> {
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<WorkerTaskProvider>(context, listen: false).loadTasks();
    });
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Keluar Akun'),
        content: const Text('Apakah Anda yakin ingin keluar dari aplikasi petugas?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.accentDanger),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      await Provider.of<AuthProvider>(context, listen: false).logout();
      if (mounted) {
        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (route) => false,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.currentUser;

    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppTheme.primaryColor.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.cleaning_services_rounded, color: AppTheme.primaryColor, size: 20),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Portal Petugas',
                  style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
                ),
                Text(
                  user?.villageName != null ? 'Kelurahan ${user!.villageName!}' : 'Kecamatan Sumbersari',
                  style: const TextStyle(fontSize: 11, color: AppTheme.textMuted, fontWeight: FontWeight.normal),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'Segarkan',
            onPressed: () => context.read<WorkerTaskProvider>().loadTasks(refresh: true),
          ),
          IconButton(
            icon: const Icon(Icons.logout_rounded, size: 20, color: AppTheme.accentDanger),
            tooltip: 'Keluar',
            onPressed: _handleLogout,
          ),
        ],
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _WorkerTasksTab(isHistory: false),
          _WorkerTasksTab(isHistory: true),
          const _WorkerProfileTab(),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (idx) => setState(() => _currentIndex = idx),
        backgroundColor: Colors.white,
        elevation: 2,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.assignment_outlined),
            selectedIcon: Icon(Icons.assignment_rounded, color: AppTheme.primaryColor),
            label: 'Tugas Aktif',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_rounded),
            selectedIcon: Icon(Icons.history_rounded, color: AppTheme.primaryColor),
            label: 'Riwayat Selesai',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline_rounded),
            selectedIcon: Icon(Icons.person_rounded, color: AppTheme.primaryColor),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}

class _WorkerTasksTab extends StatelessWidget {
  final bool isHistory;

  const _WorkerTasksTab({required this.isHistory});

  @override
  Widget build(BuildContext context) {
    return Consumer<WorkerTaskProvider>(
      builder: (context, prov, child) {
        final List<CleanupTask> taskList = isHistory
            ? prov.tasks.where((t) => t.isMyAssignmentCompleted || t.isTaskCompleted).toList()
            : prov.tasks.where((t) => !t.isMyAssignmentCompleted && !t.isTaskCompleted).toList();

        return Column(
          children: [
            // Sub-header banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
              color: Colors.white,
              child: Row(
                children: [
                  Icon(
                    isHistory ? Icons.check_circle_outline_rounded : Icons.pending_actions_rounded,
                    size: 18,
                    color: isHistory ? AppTheme.emphasis : const Color(0xFFD97706),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    isHistory ? 'Daftar Tugas yang Telah Tuntas' : 'Daftar Tugas yang Perlu Dikerjakan',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppTheme.textDark),
                  ),
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      '${taskList.length}',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                    ),
                  ),
                ],
              ),
            ),
            const Divider(height: 1),

            Expanded(
              child: prov.isLoading
                  ? const Center(child: CircularProgressIndicator(color: AppTheme.primaryColor))
                  : taskList.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                isHistory ? Icons.history_edu_rounded : Icons.task_alt_rounded,
                                size: 64,
                                color: Colors.grey.shade300,
                              ),
                              const SizedBox(height: 12),
                              Text(
                                isHistory
                                    ? 'Belum ada riwayat tugas selesai'
                                    : 'Tidak ada tugas pembersihan baru saat ini',
                                style: const TextStyle(color: AppTheme.textMuted),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          color: AppTheme.primaryColor,
                          onRefresh: () => prov.loadTasks(refresh: true),
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: taskList.length,
                            separatorBuilder: (context, index) => const SizedBox(height: 12),
                            itemBuilder: (context, index) {
                              final task = taskList[index];
                              return _buildTaskCard(context, task);
                            },
                          ),
                        ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildTaskCard(BuildContext context, CleanupTask task) {
    Color statusColor = const Color(0xFFD97706);
    if (task.isMyAssignmentAccepted) statusColor = const Color(0xFF2563EB);
    if (task.isMyAssignmentCompleted) statusColor = AppTheme.emphasis;
    if (task.isMyAssignmentRejected) statusColor = AppTheme.accentDanger;

    final hasNotes = task.notes != null && task.notes!.trim().isNotEmpty;

    return Container(
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
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header row: ID & status badges
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Tugas #${task.id}',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.textDark),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  task.myStatusLabel,
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: statusColor),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Village & Report details
          if (task.report != null) ...[
            Row(
              children: [
                const Icon(Icons.location_on_rounded, size: 16, color: AppTheme.primaryColor),
                const SizedBox(width: 4),
                Text(
                  task.report!.villageName != null ? 'Kelurahan ${task.report!.villageName!}' : 'Kecamatan Sumbersari',
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppTheme.textDark),
                ),
                const Spacer(),
                Text(
                  'Kode: ${task.report!.reportCode}',
                  style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                ),
              ],
            ),
            const SizedBox(height: 8),
          ],

          // INSTRUCTION SNIPPET FOR EQUIPMENT
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              color: const Color(0xFFFFFBEB),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFFFDE68A)),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.handyman_rounded, color: Color(0xFFD97706), size: 16),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    hasNotes ? 'Alat: ${task.notes!}' : 'Instruksi: Peralatan kebersihan standar.',
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF78350F),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // Bottom Action
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${task.photos.length} foto diunggah',
                style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
              ),
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => WorkerTaskDetailScreen(taskId: task.id)),
                  );
                },
                icon: const Icon(Icons.arrow_forward_rounded, size: 16),
                label: const Text('Buka Detail', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _WorkerProfileTab extends StatelessWidget {
  const _WorkerProfileTab();

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().currentUser;

    return ListView(
      padding: const EdgeInsets.all(24),
      children: [
        Center(
          child: Column(
            children: [
              CircleAvatar(
                radius: 40,
                backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.15),
                child: const Icon(Icons.cleaning_services_rounded, size: 44, color: AppTheme.primaryColor),
              ),
              const SizedBox(height: 12),
              Text(
                user?.name ?? 'Petugas Kebersihan',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
              const SizedBox(height: 4),
              Text(user?.email ?? '', style: const TextStyle(fontSize: 13, color: AppTheme.textMuted)),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                decoration: BoxDecoration(
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Text(
                  'Peran: Petugas Kebersihan',
                  style: TextStyle(fontSize: 12, color: AppTheme.primaryColor, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 24),
        const Divider(),
        ListTile(
          leading: const Icon(Icons.location_city_rounded, color: AppTheme.primaryColor),
          title: const Text('Wilayah Operasional', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
          subtitle: Text(
            user?.villageName != null ? 'Kelurahan ${user!.villageName!}, Sumbersari' : 'Kecamatan Sumbersari',
            style: const TextStyle(fontSize: 13),
          ),
        ),
        ListTile(
          leading: const Icon(Icons.phone_rounded, color: AppTheme.primaryColor),
          title: const Text('Nomor Kontak', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
          subtitle: Text(user?.phone ?? '-', style: const TextStyle(fontSize: 13)),
        ),
        const SizedBox(height: 20),
        SizedBox(
          width: double.infinity,
          child: OutlinedButton.icon(
            style: OutlinedButton.styleFrom(
              foregroundColor: AppTheme.accentDanger,
              side: const BorderSide(color: AppTheme.accentDanger),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
              padding: const EdgeInsets.symmetric(vertical: 12),
            ),
            onPressed: () async {
              await context.read<AuthProvider>().logout();
              if (context.mounted) {
                Navigator.of(context).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
            icon: const Icon(Icons.logout_rounded, size: 18),
            label: const Text('Keluar Akun Petugas', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
        ),
      ],
    );
  }
}
