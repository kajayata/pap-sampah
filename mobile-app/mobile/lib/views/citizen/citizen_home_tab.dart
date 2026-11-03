import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/news_article.dart';
import '../../providers/auth_provider.dart';
import '../../providers/public_provider.dart';
import '../../providers/report_provider.dart';
import '../auth/auth_widgets.dart';
import '../shared/notifications_screen.dart';
import '../shared/profile_screen.dart';
import 'create_report_screen.dart';
import 'news_detail_screen.dart';

// ---- Ilustrasi header -------------------------------------------------
const _heroAsset = 'assets/images/home_header.png';
// Ubah ke true jika bagian atas ilustrasimu gelap (teks sapaan jadi putih).
const _heroIsDark = false;

// ---- Palet (diambil dari mockup) --------------------------------------
const _bg = Color(0xFFFBFFFA);
const _border = Color(0xFFD5DEE6);
const _sage = Color(0xFFEBF7E3);
const _sky = Color(0xFFDBF4FF);
const _sand = Color(0xFFFBF1DC);
const _lilac = Color(0xFFEDE7F8);
const _aqua = Color(0xFF9EF4E7);
const _skyFg = Color(0xFF2F6DB5);
const _sandFg = Color(0xFFB7791F);
const _lilacFg = Color(0xFF6B4FA3);
const _avatarBlue = Color(0xFF5B8DEF);
const _amber = Color(0xFFB7791F);
const _blue = Color(0xFF2563EB);

class CitizenHomeTab extends StatefulWidget {
  final Function(int tabIndex)? onSwitchTab;

  const CitizenHomeTab({super.key, this.onSwitchTab});

  @override
  State<CitizenHomeTab> createState() => _CitizenHomeTabState();
}

class _CitizenHomeTabState extends State<CitizenHomeTab> {
  static const _tips = [
    'Pisahkan sampah organik dan anorganik sebelum dibuang, supaya mudah didaur ulang.',
    'Bilas botol dan kemasan plastik sebelum disetor ke bank sampah agar nilainya lebih tinggi.',
    'Sampah di pinggir jalan bisa dilaporkan lewat foto dan lokasi GPS, petugas akan menindaklanjuti.',
    'Kurangi plastik sekali pakai: bawa tas belanja dan botol minum sendiri.',
    'Sisa makanan bisa dijadikan kompos sederhana untuk tanaman di rumah.',
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final pubProv = Provider.of<PublicProvider>(context, listen: false);
      final repProv = Provider.of<ReportProvider>(context, listen: false);
      pubProv.refreshHomeData();
      repProv.loadReports();
    });
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).currentUser;
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
          final repProv = Provider.of<ReportProvider>(context, listen: false);
          await Future.wait([
            pubProv.refreshHomeData(),
            repProv.loadReports(),
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
                    child: _buildSummaryCard(),
                  ),
                  const SizedBox(height: 14),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildLevelCard(),
                  ),
                  const SizedBox(height: 26),
                  _sectionTitle('Mulai dari sini'),
                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildActions(),
                  ),
                  const SizedBox(height: 26),
                  _sectionTitle(
                    'Laporan terbaru',
                    actionLabel: 'Lihat semua',
                    onAction: () => widget.onSwitchTab?.call(2),
                  ),
                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildRecentReports(),
                  ),
                  const SizedBox(height: 26),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildTipCard(),
                  ),
                  const SizedBox(height: 26),
                  _sectionTitle('Edukasi & berita terkini'),
                  const SizedBox(height: 12),
                  _buildNewsSection(),
                  // Ruang untuk tombol "Lapor Sampah" yang melayang & bottom nav.
                  const SizedBox(height: 110),
                ],
              ),
              // Sapaan di paling atas supaya tombolnya selalu bisa diklik.
              Positioned(
                top: topPad + 14,
                left: 20,
                right: 20,
                child: _buildHeader(user?.name, user?.villageName),
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

  Widget _buildHeader(String? name, String? village) {
    final textColor = _heroIsDark ? Colors.white : AuthColors.ink;
    final subColor = _heroIsDark
        ? Colors.white.withValues(alpha: 0.8)
        : AuthColors.muted;
    final location = village != null
        ? 'Kelurahan $village'
        : 'Kecamatan Sumbersari, Jember';

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            GestureDetector(
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const ProfileScreen()),
              ),
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
                  Text('Hi,', style: TextStyle(fontSize: 12.5, color: subColor)),
                  Text(
                    name ?? 'Warga Sumbersari',
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
            _roundButton(
              Icons.notifications_none_rounded,
              () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => NotificationsScreen()),
              ),
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
    if (name == null || name.trim().isEmpty) return 'W';
    return name.trim()[0].toUpperCase();
  }

  // ------------------------------------------------------- Kartu ringkasan

  Widget _buildSummaryCard() {
    return Consumer<ReportProvider>(
      builder: (context, prov, child) {
        final total = prov.reports.length;
        final pending = prov.reports.where((r) => r.isPending).length;
        final inProgress =
            prov.reports.where((r) => r.isInProgress || r.isValidated).length;
        final resolved = prov.reports.where((r) => r.isResolved).length;
        final ratio = total == 0 ? 0.0 : resolved / total;

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
                    '$total',
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
                      'laporan kamu',
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: AuthColors.muted,
                      ),
                    ),
                  ),
                  const Spacer(),
                  GestureDetector(
                    onTap: () => widget.onSwitchTab?.call(2),
                    child: const Padding(
                      padding: EdgeInsets.only(bottom: 3),
                      child: Text(
                        'Riwayat ›',
                        style: TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w700,
                          color: AuthColors.green,
                        ),
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
                total == 0
                    ? 'Belum ada laporan. Yuk, mulai dari sampah di sekitarmu.'
                    : '$resolved dari $total laporan sudah dibersihkan',
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

  // ------------------------------------------------------------ Kartu level

  ({int level, String name, double progress, int remaining}) _levelInfo(int total) {
    const step = 3; // naik 1 level setiap 3 laporan
    final level = total ~/ step + 1;
    final progress = (total % step) / step;
    final remaining = step - (total % step);
    final name = level < 2
        ? 'Warga Baru'
        : level < 4
            ? 'Warga Peduli'
            : 'Pahlawan Bersih';
    return (level: level, name: name, progress: progress, remaining: remaining);
  }

  Widget _buildLevelCard() {
    return Consumer<ReportProvider>(
      builder: (context, prov, child) {
        final info = _levelInfo(prov.reports.length);

        return _card(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              SizedBox(
                width: 64,
                height: 64,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    SizedBox(
                      width: 64,
                      height: 64,
                      child: CircularProgressIndicator(
                        value: info.progress == 0 ? 0.02 : info.progress,
                        strokeWidth: 6,
                        backgroundColor: _sage,
                        valueColor: const AlwaysStoppedAnimation(AuthColors.green),
                        strokeCap: StrokeCap.round,
                      ),
                    ),
                    Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Text(
                          'Level',
                          style: TextStyle(fontSize: 9.5, color: AuthColors.muted),
                        ),
                        Text(
                          '${info.level}',
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.w800,
                            color: AuthColors.ink,
                            height: 1,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      info.name,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        color: AuthColors.ink,
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      'Buat ${info.remaining} laporan lagi untuk naik ke level ${info.level + 1}.',
                      style: const TextStyle(
                        fontSize: 12,
                        height: 1.4,
                        color: AuthColors.muted,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // ---------------------------------------------------------------- Aksi

  Widget _buildActions() {
    return Column(
      children: [
        IntrinsicHeight(
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Expanded(
                child: _actionTile(
                  icon: Icons.photo_camera_rounded,
                  tileBg: _sage,
                  iconColor: AuthColors.green,
                  title: 'Lapor Sampah',
                  subtitle: 'Kirim foto & lokasi GPS',
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const CreateReportScreen()),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _actionTile(
                  icon: Icons.map_rounded,
                  tileBg: _sky,
                  iconColor: _skyFg,
                  title: 'Peta Sebaran',
                  subtitle: 'Titik sampah di sekitarmu',
                  onTap: () => widget.onSwitchTab?.call(1),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        IntrinsicHeight(
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Expanded(
                child: _actionTile(
                  icon: Icons.recycling_rounded,
                  tileBg: _sand,
                  iconColor: _sandFg,
                  title: 'Bank Sampah',
                  subtitle: 'Lokasi bank sampah & TPA',
                  onTap: () => widget.onSwitchTab?.call(3),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _actionTile(
                  icon: Icons.assignment_rounded,
                  tileBg: _lilac,
                  iconColor: _lilacFg,
                  title: 'Laporan Saya',
                  subtitle: 'Pantau status pengaduan',
                  onTap: () => widget.onSwitchTab?.call(2),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _actionTile({
    required IconData icon,
    required Color tileBg,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Material(
      color: tileBg,
      borderRadius: BorderRadius.circular(24),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(24),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: const BoxDecoration(
                  color: Colors.white,
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, size: 22, color: iconColor),
              ),
              const SizedBox(height: 16),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 14.5,
                  fontWeight: FontWeight.w800,
                  color: AuthColors.ink,
                  letterSpacing: -0.2,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                subtitle,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  fontSize: 11.5,
                  height: 1.35,
                  color: Color(0xFF55655B),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ------------------------------------------------------ Laporan terbaru

  Widget _buildRecentReports() {
    return Consumer<ReportProvider>(
      builder: (context, prov, child) {
        final items = prov.reports.take(3).toList();

        if (items.isEmpty) {
          return _card(
            padding: const EdgeInsets.all(18),
            child: Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: const BoxDecoration(
                    color: _sage,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.add_a_photo_rounded,
                      size: 21, color: AuthColors.green),
                ),
                const SizedBox(width: 14),
                const Expanded(
                  child: Text(
                    'Belum ada laporan. Temukan tumpukan sampah liar? Laporkan sekarang.',
                    style: TextStyle(fontSize: 12.5, height: 1.4, color: AuthColors.muted),
                  ),
                ),
              ],
            ),
          );
        }

        return Column(
          children: [
            for (final r in items) ...[
              _reportRow(r),
              const SizedBox(height: 10),
            ],
          ],
        );
      },
    );
  }

  // Nama field model Report belum kuketahui, jadi dicoba beberapa kemungkinan.
  // Ganti dengan field aslimu, misalnya: r.title dan r.address.
  String? _pick(List<dynamic Function()> getters) {
    for (final g in getters) {
      try {
        final v = g();
        if (v is String && v.trim().isNotEmpty) return v.trim();
      } catch (_) {}
    }
    return null;
  }

  Widget _reportRow(dynamic r) {
    final title = _pick([() => r.title, () => r.description, () => r.category]) ??
        'Laporan sampah';
    final meta = _pick([() => r.address, () => r.locationName, () => r.location]) ??
        'Sumbersari, Jember';

    late final String status;
    late final Color color;
    late final Color bg;
    if (r.isResolved) {
      status = 'Selesai';
      color = AuthColors.green;
      bg = const Color(0xFFDDF0E3);
    } else if (r.isInProgress || r.isValidated) {
      status = 'Diproses';
      color = _blue;
      bg = const Color(0xFFDBE8FF);
    } else {
      status = 'Menunggu';
      color = _amber;
      bg = const Color(0xFFFFEDC2);
    }

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(20),
      child: InkWell(
        onTap: () => widget.onSwitchTab?.call(2),
        borderRadius: BorderRadius.circular(20),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: _border),
          ),
          child: Row(
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: _sage,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(Icons.delete_outline_rounded,
                    size: 22, color: AuthColors.green),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w700,
                        color: AuthColors.ink,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      meta,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: bg,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  status,
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: color,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // -------------------------------------------------------------- Tips

  Widget _buildTipCard() {
    final tip = _tips[DateTime.now().day % _tips.length];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFFBF7EA),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFF0E6C4)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: const BoxDecoration(
              color: Color(0xFFF8EBC4),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.lightbulb_rounded, size: 21, color: _amber),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Tahukah kamu?',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: AuthColors.ink,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  tip,
                  style: const TextStyle(
                    fontSize: 12.5,
                    height: 1.45,
                    color: Color(0xFF4A5A50),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // -------------------------------------------------------------- Berita

  Widget _buildNewsSection() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        if (prov.isLoadingNews) {
          return const Center(
            child: Padding(
              padding: EdgeInsets.all(32),
              child: CircularProgressIndicator(color: AuthColors.green),
            ),
          );
        }

        if (prov.newsList.isEmpty) {
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: _card(
              padding: const EdgeInsets.all(24),
              child: const Center(
                child: Text(
                  'Belum ada artikel edukasi lingkungan',
                  style: TextStyle(color: AuthColors.muted),
                ),
              ),
            ),
          );
        }

        return SizedBox(
          height: 224,
          child: ListView.separated(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            scrollDirection: Axis.horizontal,
            itemCount: prov.newsList.length,
            separatorBuilder: (context, index) => const SizedBox(width: 14),
            itemBuilder: (context, index) => _buildNewsCard(prov.newsList[index]),
          ),
        );
      },
    );
  }

  Widget _buildNewsCard(NewsArticle item) {
    Widget placeholder() => Container(
          color: _sage,
          child: const Center(
            child: Icon(Icons.eco_rounded, color: AuthColors.green, size: 36),
          ),
        );

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(24),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => NewsDetailScreen(article: item)),
        ),
        child: Container(
          width: 250,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: _border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              SizedBox(
                height: 116,
                width: double.infinity,
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    item.thumbnailUrl != null
                        ? Image.network(
                            item.thumbnailUrl!,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) =>
                                placeholder(),
                          )
                        : placeholder(),
                    Positioned(
                      left: 10,
                      top: 10,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 9, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.92),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Text(
                          'Edukasi',
                          style: TextStyle(
                            fontSize: 10.5,
                            fontWeight: FontWeight.w800,
                            color: AuthColors.green,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item.title,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 13.5,
                        color: AuthColors.ink,
                        height: 1.3,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      item.authorName ?? 'Admin Kebersihan',
                      style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ------------------------------------------------------------- Helper UI

  Widget _sectionTitle(String title, {String? actionLabel, VoidCallback? onAction}) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.w800,
              color: AuthColors.ink,
              letterSpacing: -0.3,
            ),
          ),
          if (actionLabel != null)
            GestureDetector(
              onTap: onAction,
              child: Text(
                actionLabel,
                style: const TextStyle(
                  fontSize: 12.5,
                  fontWeight: FontWeight.w700,
                  color: AuthColors.green,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _card({
    required Widget child,
    EdgeInsets padding = const EdgeInsets.all(16),
    bool shadow = false,
  }) {
    return Container(
      width: double.infinity,
      padding: padding,
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
                ),
              ]
            : null,
      ),
      child: child,
    );
  }
}