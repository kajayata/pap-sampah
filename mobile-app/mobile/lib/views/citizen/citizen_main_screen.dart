import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/notification_provider.dart';
import '../shared/profile_screen.dart';
import 'citizen_home_tab.dart';
import 'create_report_screen.dart';
import 'report_list_screen.dart';
import 'spatial_map_tab.dart';
import 'waste_banks_screen.dart';

class CitizenMainScreen extends StatefulWidget {
  const CitizenMainScreen({super.key});

  @override
  State<CitizenMainScreen> createState() => _CitizenMainScreenState();
}

class _CitizenMainScreenState extends State<CitizenMainScreen> {
  int _currentIndex = 0;
  final GlobalKey<SpatialMapTabState> _mapKey = GlobalKey<SpatialMapTabState>();
  late final NotificationProvider _notifProvider;

  @override
  void initState() {
    super.initState();
    _notifProvider = context.read<NotificationProvider>();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      // Start polling for report status changes (citizen)
      _notifProvider.startPolling(isCitizen: true);
    });
  }

  @override
  void dispose() {
    _notifProvider.stopPolling();
    super.dispose();
  }

  void _onTabSelected(int index) {
    // Index 4 (Profil) membuka halaman profil tersendiri, bukan mengganti tab.
    if (index == 4) {
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

  void _openCreateReport() {
    Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => const CreateReportScreen()),
    ).then((created) {
      if (created == true) {
        _onTabSelected(2); // Switch to reports tab to view newly created report
      }
    });
  }

  void _navigateToMapCoordinates(double lat, double lng) {
    setState(() => _currentIndex = 1);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _mapKey.currentState?.moveToCoordinates(lat, lng, zoom: 16.5);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      floatingActionButton: FloatingActionButton.extended(
        heroTag: 'citizen_lapor_fab',
        onPressed: _openCreateReport,
        backgroundColor: const Color(0xFF1F6B45),
        foregroundColor: Colors.white,
        icon: const Icon(Icons.camera_alt_rounded),
        label: const Text('Lapor Sampah', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: [
          CitizenHomeTab(onSwitchTab: _onTabSelected),
          SpatialMapTab(key: _mapKey),
          const ReportListScreen(),
          WasteBanksScreen(onNavigateToMap: _navigateToMapCoordinates),
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
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home_rounded, color: Color(0xFF1F6B45)),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.map_outlined),
            selectedIcon: Icon(Icons.map_rounded, color: Color(0xFF1F6B45)),
            label: 'Peta',
          ),
          NavigationDestination(
            icon: Icon(Icons.assignment_outlined),
            selectedIcon: Icon(Icons.assignment_rounded, color: Color(0xFF1F6B45)),
            label: 'Laporan',
          ),
          NavigationDestination(
            icon: Icon(Icons.recycling_outlined),
            selectedIcon: Icon(Icons.recycling_rounded, color: Color(0xFF1F6B45)),
            label: 'Bank Sampah',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline_rounded),
            selectedIcon: Icon(Icons.person_rounded, color: Color(0xFF1F6B45)),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}