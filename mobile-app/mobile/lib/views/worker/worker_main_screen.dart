import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/notification_provider.dart';
import '../../providers/public_provider.dart';
import '../../providers/worker_task_provider.dart';
import '../auth/auth_widgets.dart';
import '../shared/profile_screen.dart';
import 'worker_history_tab.dart';
import 'worker_home_tab.dart';

class WorkerMainScreen extends StatefulWidget {
  const WorkerMainScreen({super.key});

  @override
  State<WorkerMainScreen> createState() => _WorkerMainScreenState();
}

class _WorkerMainScreenState extends State<WorkerMainScreen> {
  int _currentIndex = 0;
  late final NotificationProvider _notifProvider;

  @override
  void initState() {
    super.initState();
    _notifProvider = context.read<NotificationProvider>();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      context.read<PublicProvider>().refreshHomeData();
      context.read<WorkerTaskProvider>().loadTasks();
      // Mulai polling untuk penugasan baru (petugas).
      _notifProvider.startPolling(isCitizen: false);
    });
  }

  @override
  void dispose() {
    _notifProvider.stopPolling();
    super.dispose();
  }

  void _onTabSelected(int index) {
    // Index 2 (Profil) membuka halaman profil tersendiri, bukan mengganti tab.
    if (index == 2) {
      _openProfile();
      return;
    }
    setState(() => _currentIndex = index);
  }

  void _openProfile() {
    Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => const ProfileScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFBFFFA),
      body: IndexedStack(
        index: _currentIndex,
        children: const [
          WorkerHomeTab(),
          WorkerHistoryTab(),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: _onTabSelected,
        backgroundColor: Colors.white,
        indicatorColor: const Color(0xFFDDF0E3),
        elevation: 2,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.assignment_outlined),
            selectedIcon: Icon(Icons.assignment_rounded, color: AuthColors.green),
            label: 'Tugas',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_rounded),
            selectedIcon: Icon(Icons.history_rounded, color: AuthColors.green),
            label: 'Riwayat',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline_rounded),
            selectedIcon: Icon(Icons.person_rounded, color: AuthColors.green),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}