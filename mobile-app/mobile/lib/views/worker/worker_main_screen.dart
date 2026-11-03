import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/cleanup_task.dart';
import '../../providers/auth_provider.dart';
import '../../providers/notification_provider.dart'; // <-- Pastikan ini ada
import '../../providers/public_provider.dart';     // <-- Pastikan ini ada
import '../../providers/worker_task_provider.dart';
import '../auth/auth_widgets.dart';
import '../auth/login_screen.dart';
import '../shared/profile_screen.dart';
import 'worker_home_tab.dart';
import 'worker_history_tab.dart';

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
      final pubProv = Provider.of<PublicProvider>(context, listen: false);
      final taskProv = Provider.of<WorkerTaskProvider>(context, listen: false);
      pubProv.refreshHomeData();
      taskProv.loadTasks();
      // Start polling for new task assignments (worker)
      context.read<NotificationProvider>().startPolling(isCitizen: false);
    });
  }

  @override
  void dispose() {
    context.read<NotificationProvider>().stopPolling();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF4F6F2),
      body: IndexedStack(
        index: _currentIndex,
        children: const [
          WorkerHomeTab(),
          WorkerHistoryTab(),
          ProfileScreen(), // Mengarah langsung ke shared profile screen
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
            selectedIcon: Icon(Icons.assignment_rounded, color: AuthColors.green),
            label: 'Tugas Aktif',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_rounded),
            selectedIcon: Icon(Icons.history_rounded, color: AuthColors.green),
            label: 'Riwayat Selesai',
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