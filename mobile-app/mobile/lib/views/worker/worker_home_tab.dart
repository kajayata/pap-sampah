import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/cleanup_task.dart';
import '../../providers/auth_provider.dart';
import '../../providers/notification_provider.dart';
import '../../providers/public_provider.dart';
import '../../providers/worker_task_provider.dart';
import '../auth/auth_widgets.dart';
import '../shared/notifications_screen.dart';
import '../shared/profile_screen.dart';
import 'worker_task_detail_screen.dart';

// ---- Ilustrasi header (khusus petugas) --------------------------------
const _heroAsset = 'assets/images/worker_header.png';
// Ubah ke true jika bagian atas ilustrasimu gelap (teks sapaan jadi putih).
const _heroIsDark = false;

// ---- Palet (sama dengan beranda warga) --------------------------------
const _bg = Color(0xFFFBFFFA);
const _border = Color(0xFFD5DEE6);
const _sage = Color(0xFFEBF7E3);
const _aqua = Color(0xFF9EF4E7);
const _avatarBlue = Color(0xFF5B8DEF);
const _amber = Color(0xFFB7791F);
const _blue = Color(0xFF2563EB);

class WorkerHomeTab extends StatefulWidget {
  const WorkerHomeTab({super.key});

  @override
  State<WorkerHomeTab> createState() => _WorkerHomeTabState();
}

class _WorkerHomeTabState extends State<WorkerHomeTab> {
  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.currentUser;
    final mq = MediaQuery.of(context);
    final topPad = mq.padding.top;
    final heroH = topPad + (mq.size.width * 0.78).clamp(260.0, 340.0).toDouble();
    const overlap = 56.0; // seberapa jauh kartu pertama menimpa ilustrasi

    return Scaffold(
      backgroundColor: _bg,
      body: RefreshIndicator(
        color: AuthColors.green,
        onRefresh: () async {
          final pubProv = Provider.of<PublicProvider>(context, listen: false);
          final taskProv = Provider.of<WorkerTaskProvider>(context, listen: false);
          await Future.wait([
            pubProv.refreshHomeData(),
            taskProv.loadTasks(refresh: true),
          ]);
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Stack(
            children: [
              Positioned(
                top: 0,
                left: 0,
                right: 0,
                height: heroH,
                child: _buildHero(),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  SizedBox(height: heroH - overlap),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildSummaryCard(context),
                  ),
                  const SizedBox(height: 26),
                  _sectionTitle('Daftar Tugas yang Perlu Dikerjakan'),
                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildActiveTaskList(context),
                  ),
                  const SizedBox(height: 110),
                ],
              ),
              // Sapaan di paling atas supaya tombolnya selalu bisa diklik.
              Positioned(
                top: topPad + 14,
                left: 20,
                right: 20,
                child: _buildHeader(context, user?.name, user?.villageName),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ------------------------------------------------------------ Ilustrasi

  Widget _buildHero() {
    return Stack(
      fit: StackFit.expand,
      children: [
        Image.asset(
          _heroAsset,
          fit: BoxFit.cover,
          alignment: Alignment.bottomCenter,
          errorBuilder: (context, error, stackTrace) => _heroFallback(),
        ),
        // Pudar ke warna latar agar ilustrasi menyatu dengan halaman.
        Positioned(
          left: 0,
          right: 0,
          bottom: 0,
          height: 90,
          child: DecoratedBox(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [_bg.withValues(alpha: 0), _bg],
              ),
            ),
          ),
        ),
      ],
    );
  }

  // Tampil jika file gambar belum ada / path salah.
  Widget _heroFallback() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Colors.white, _aqua],
        ),
      ),
      alignment: Alignment.bottomRight,
      padding: const EdgeInsets.fromLTRB(20, 0, 20, 70),
      child: kDebugMode
          ? const Text(
              'Letakkan gambar di\n$_heroAsset',
              textAlign: TextAlign.right,
              style: TextStyle(fontSize: 11, color: AuthColors.muted),
            )
          : null,
    );
  }

  // ---------------------------------------------------------------- Header

  Widget _buildHeader(BuildContext context, String? name, String? village) {
    final textColor = _heroIsDark ? Colors.white : AuthColors.ink;
    final subColor = _heroIsDark
        ? Colors.white.withValues(alpha: 0.8)
        : AuthColors.muted;
    final location =
        village != null ? 'Kelurahan $village' : 'Kecamatan Sumbersari, Jember';

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            GestureDetector(
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const ProfileScreen()),
                );
              },
              child: Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: _avatarBlue,
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white, width: 2),
                ),
                alignment: Alignment.center,
                child: Text(
                  _initial(name),
                  style: const TextStyle(
                    fontSize: 19,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Portal Petugas,',
                    style: TextStyle(fontSize: 12.5, color: subColor),
                  ),
                  Text(
                    name ?? 'Petugas Kebersihan',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: 19,
                      fontWeight: FontWeight.w800,
                      color: textColor,
                      letterSpacing: -0.3,
                      height: 1.15,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            Consumer<NotificationProvider>(
              builder: (ctx, notifProv, _) {
                return Stack(
                  clipBehavior: Clip.none,
                  children: [
                    _roundButton(
                      Icons.notifications_none_rounded,
                      () {
                        Navigator.of(context).push(
                          MaterialPageRoute(builder: (_) => const NotificationsScreen()),
                        );
                      },
                    ),
                    if (notifProv.unreadCount > 0)
                      Positioned(
                        right: -2,
                        top: -2,
                        child: Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: AppTheme.accentDanger,
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 1.5),
                          ),
                          child: Text(
                            notifProv.unreadCount > 9 ? '9+' : '${notifProv.unreadCount}',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 8,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                  ],
                );
              },
            ),
          ],
        ),
        const SizedBox(height: 12),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: [
            _headerPill(Icons.location_on_rounded, location),
            _buildWeatherPill(),
          ],
        ),
      ],
    );
  }

  Widget _roundButton(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 42,
        height: 42,
        decoration: BoxDecoration(
          color: Colors.white,
          shape: BoxShape.circle,
          border: Border.all(color: _border),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Icon(icon, size: 21, color: AuthColors.ink),
      ),
    );
  }

  Widget _headerPill(IconData icon, String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.92),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _border),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: AuthColors.green),
          const SizedBox(width: 6),
          Text(
            text,
            style: const TextStyle(
              fontSize: 11.5,
              fontWeight: FontWeight.w600,
              color: AuthColors.ink,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildWeatherPill() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        final w = prov.weather;
        final text = w != null
            ? '${w.temperature.round()}${w.temperatureUnit} · ${w.condition}'
            : '28°C · Cerah Berawan';
        return _headerPill(Icons.wb_sunny_rounded, text);
      },
    );
  }

  String _initial(String? name) {
    if (name == null || name.trim().isEmpty) return 'P';
    return name.trim()[0].toUpperCase();
  }

  // ------------------------------------------------------------- Helper UI

  Widget _card({required Widget child, EdgeInsetsGeometry? padding, bool shadow = false}) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: _border),
        boxShadow: shadow
            ? [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.06),
                  blurRadius: 20,
                  offset: const Offset(0, 6),
                )
              ]
            : null,
      ),
      child: child,
    );
  }

  Widget _sectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 17,
          fontWeight: FontWeight.w800,
          color: AuthColors.ink,
          letterSpacing: -0.3,
        ),
      ),
    );
  }

  // ------------------------------------------------------- Kartu ringkasan

  Widget _buildSummaryCard(BuildContext context) {
    return Consumer<WorkerTaskProvider>(
      builder: (context, prov, child) {
        final tasks = prov.tasks;
        final activeTasks =
            tasks.where((t) => !t.isMyAssignmentCompleted && !t.isTaskCompleted).toList();
        final pending = activeTasks.where((t) => !t.isMyAssignmentAccepted).length;
        final inProgress = activeTasks.where((t) => t.isMyAssignmentAccepted).length;
        final resolved =
            tasks.where((t) => t.isMyAssignmentCompleted || t.isTaskCompleted).length;
        final ratio = tasks.isEmpty ? 0.0 : resolved / tasks.length;

        return _card(
          shadow: true,
          padding: const EdgeInsets.fromLTRB(18, 16, 18, 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    '${activeTasks.length}',
                    style: const TextStyle(
                      fontSize: 34,
                      fontWeight: FontWeight.w800,
                      color: AuthColors.ink,
                      height: 1,
                      letterSpacing: -1,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Padding(
                    padding: EdgeInsets.only(bottom: 3),
                    child: Text(
                      'tugas aktif',
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: AuthColors.muted,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: LinearProgressIndicator(
                  value: ratio,
                  minHeight: 8,
                  backgroundColor: _sage,
                  valueColor: const AlwaysStoppedAnimation(AuthColors.green),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                tasks.isEmpty
                    ? 'Belum ada tugas yang ditugaskan kepadamu.'
                    : '$resolved dari ${tasks.length} tugas sudah selesai',
                style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
              ),
              const SizedBox(height: 14),
              const Divider(height: 1, color: _border),
              const SizedBox(height: 14),
              Row(
                children: [
                  _statItem('Menunggu', pending, _amber),
                  _statItem('Diproses', inProgress, _blue),
                  _statItem('Selesai', resolved, AuthColors.green),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _statItem(String label, int value, Color color) {
    return Expanded(
      child: Row(
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
          ),
          const SizedBox(width: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$value',
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w800,
                  color: AuthColors.ink,
                  height: 1.1,
                ),
              ),
              Text(
                label,
                style: const TextStyle(fontSize: 11, color: AuthColors.muted),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ----------------------------------------------------------- Daftar tugas

  Widget _buildActiveTaskList(BuildContext context) {
    return Consumer<WorkerTaskProvider>(
      builder: (context, prov, child) {
        if (prov.isLoading) {
          return const Center(
            child: Padding(
              padding: EdgeInsets.all(32),
              child: CircularProgressIndicator(color: AuthColors.green),
            ),
          );
        }

        final taskList =
            prov.tasks.where((t) => !t.isMyAssignmentCompleted && !t.isTaskCompleted).toList();

        if (taskList.isEmpty) {
          return _card(
            padding: const EdgeInsets.all(24),
            child: Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: const BoxDecoration(
                      color: _sage,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.check_circle_rounded,
                        size: 28, color: AuthColors.green),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Tidak ada tugas pembersihan baru saat ini',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: AuthColors.ink,
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Semua area di Sumbersari terpantau bersih.',
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 12, color: AuthColors.muted),
                  ),
                ],
              ),
            ),
          );
        }

        return Column(
          children: [
            for (final task in taskList) ...[
              _buildTaskCard(context, task),
              const SizedBox(height: 12),
            ],
          ],
        );
      },
    );
  }

  Widget _buildTaskCard(BuildContext context, CleanupTask task) {
    Color statusColor = _amber;
    Color statusBg = const Color(0xFFFDF3D8);
    if (task.isMyAssignmentAccepted) {
      statusColor = _blue;
      statusBg = const Color(0xFFE4EEFF);
    }
    if (task.isMyAssignmentCompleted) {
      statusColor = AuthColors.green;
      statusBg = const Color(0xFFE2F4E8);
    }
    if (task.isMyAssignmentRejected) {
      statusColor = AppTheme.accentDanger;
      statusBg = const Color(0xFFFFDCDD);
    }

    final hasNotes = task.notes != null && task.notes!.trim().isNotEmpty;

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: _border),
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
                style: const TextStyle(
                  fontWeight: FontWeight.w800,
                  fontSize: 15,
                  color: AuthColors.ink,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusBg,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  task.myStatusLabel,
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: statusColor,
                  ),
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
                Expanded(
                  child: Text(
                    task.report!.villageName != null
                        ? 'Kelurahan ${task.report!.villageName!}'
                        : 'Kecamatan Sumbersari',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontWeight: FontWeight.w600,
                      fontSize: 13,
                      color: AuthColors.ink,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  'Kode: ${task.report!.reportCode}',
                  style: const TextStyle(fontSize: 11, color: AuthColors.muted),
                ),
              ],
            ),
            const SizedBox(height: 8),
          ],
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              color: _sage,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.handyman_rounded, color: AuthColors.green, size: 16),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    hasNotes
                        ? 'Alat: ${task.notes!}'
                        : 'Instruksi: Peralatan kebersihan standar.',
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AuthColors.ink,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${task.photos.length} foto diunggah',
                style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
              ),
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AuthColors.green,
                  foregroundColor: Colors.white,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => WorkerTaskDetailScreen(taskId: task.id),
                    ),
                  );
                },
                icon: const Icon(Icons.arrow_forward_rounded, size: 16),
                label: const Text(
                  'Buka Detail',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12.5),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}