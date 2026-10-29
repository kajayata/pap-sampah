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

<<<<<<< Updated upstream
  void _showProfileModal() {
    final user = Provider.of<AuthProvider>(context, listen: false).currentUser;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey.shade300,
                  borderRadius: BorderRadius.circular(4),
                ),
              ),
              const SizedBox(height: 20),
              CircleAvatar(
                radius: 36,
                backgroundColor: AppTheme.primaryColor.withValues(alpha: 0.15),
                child: const Icon(Icons.person, size: 44, color: AppTheme.primaryColor),
              ),
              const SizedBox(height: 12),
              Text(
                user?.name ?? 'Warga Masyarakat',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
              const SizedBox(height: 4),
              Text(user?.email ?? '', style: const TextStyle(fontSize: 13, color: AppTheme.textMuted)),
              const SizedBox(height: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  'Peran: ${user?.role ?? 'Masyarakat'}',
                  style: const TextStyle(fontSize: 11, color: AppTheme.primaryColor, fontWeight: FontWeight.bold),
                ),
              ),
              const SizedBox(height: 24),
              const Divider(),
              const SizedBox(height: 8),
              ListTile(
                leading: const Icon(Icons.phone_rounded, color: AppTheme.primaryColor),
                title: const Text('Nomor Telepon', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                subtitle: Text(user?.phone ?? '-', style: const TextStyle(fontSize: 12)),
              ),
              const SizedBox(height: 12),
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
                    Navigator.pop(ctx);
                    await _handleLogout();
                  },
                  icon: const Icon(Icons.logout_rounded, size: 18),
                  label: const Text('Keluar Akun', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        ),
      );
      },
    );
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Keluar Akun'),
        content: const Text('Apakah Anda yakin ingin keluar dari aplikasi?'),
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

=======
>>>>>>> Stashed changes
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