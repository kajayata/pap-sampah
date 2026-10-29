import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/news_article.dart';
import '../../providers/auth_provider.dart';
import '../../providers/public_provider.dart';
import '../../providers/report_provider.dart';
import '../auth/auth_widgets.dart';
import 'create_report_screen.dart';
import 'news_detail_screen.dart';
import '../shared/profile_screen.dart';

const _pageBg = Color(0xFFF4F6F2);
const _cardBorder = Color(0xFFE4EAE4);
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
    final topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: _pageBg,
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
              // Latar hijau di belakang header; kartu ringkasan menimpanya.
              Positioned(
                top: 0,
                left: 0,
                right: 0,
                height: topPad + 250,
                child: const CustomPaint(painter: HeroPainter(hillBase: 0.78)),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildHeader(user?.name, user?.villageName, topPad),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildSummaryCard(),
                  ),
                  const SizedBox(height: 14),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _buildLevelCard(),
                  ),
<<<<<<< Updated upstream
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 22,
                          backgroundColor: Colors.white.withValues(alpha: 0.2),
                          child: const Icon(Icons.person, color: Colors.white, size: 26),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Halo, ${user?.name ?? 'Warga Sumbersari'}',
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 18),
                    // Weather Card Widget
                    _buildWeatherCard(),
                  ],
                ),
=======
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
>>>>>>> Stashed changes
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ---------------------------------------------------------------- Header

    Widget _buildHeader(String? name, String? village, double topPad) {
    final location = village != null
        ? 'Kelurahan $village'
        : 'Kecamatan Sumbersari, Jember';

    return Padding(
      padding: EdgeInsets.fromLTRB(20, topPad + 18, 20, 20),
      child: Column(
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
                  decoration: const BoxDecoration(
                    color: AuthColors.yellow,
                    shape: BoxShape.circle,
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    _initial(name),
                    style: const TextStyle(
                      fontSize: 19,
                      fontWeight: FontWeight.w800,
                      color: AuthColors.greenDark,
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
                      'Halo,',
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors.white.withValues(alpha: 0.78),
                      ),
                    ),
                    Text(
                      name ?? 'Warga Sumbersari',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        letterSpacing: -0.4,
                        height: 1.15,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              _headerPill(Icons.location_on_rounded, location),
              _buildWeatherPill(),
            ],
          ),
        ],
      ),
    );
  }

  Widget _headerPill(IconData icon, String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.16),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: AuthColors.yellow),
          const SizedBox(width: 6),
          Text(
            text,
            style: const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: Colors.white,
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
                  backgroundColor: AuthColors.field,
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
              const Divider(height: 1, color: _cardBorder),
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
                        backgroundColor: AuthColors.field,
                        valueColor:
                            const AlwaysStoppedAnimation(Color(0xFF1F6B45)),
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
                child: _bigTile(
                  icon: Icons.photo_camera_rounded,
                  title: 'Lapor Sampah',
                  subtitle: 'Kirim foto & lokasi GPS',
                  background: const Color.fromRGBO(31, 107, 69, 1),
                  iconBg: AuthColors.yellow,
                  iconColor: AuthColors.greenDark,
                  titleColor: Colors.white,
                  subtitleColor: Colors.white.withValues(alpha: 0.78),
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const CreateReportScreen()),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _bigTile(
                  icon: Icons.map_rounded,
                  title: 'Peta Sebaran',
                  subtitle: 'Titik sampah di sekitarmu',
                  background: const Color(0xFFE3EFE6),
                  iconBg: Colors.white,
                  iconColor: AuthColors.green,
                  titleColor: AuthColors.ink,
                  subtitleColor: AuthColors.muted,
                  onTap: () => widget.onSwitchTab?.call(1),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        _rowTile(
          icon: Icons.recycling_rounded,
          title: 'Bank Sampah',
          subtitle: 'Lokasi bank sampah & TPA terdekat',
          onTap: () => widget.onSwitchTab?.call(3),
        ),
        const SizedBox(height: 10),
        _rowTile(
          icon: Icons.assignment_rounded,
          title: 'Laporan Saya',
          subtitle: 'Pantau status pengaduanmu',
          onTap: () => widget.onSwitchTab?.call(2),
        ),
      ],
    );
  }

  Widget _bigTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color background,
    required Color iconBg,
    required Color iconColor,
    required Color titleColor,
    required Color subtitleColor,
    required VoidCallback onTap,
  }) {
    return Material(
      color: background,
      borderRadius: BorderRadius.circular(22),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(22),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(color: iconBg, shape: BoxShape.circle),
                child: Icon(icon, size: 23, color: iconColor),
              ),
              const SizedBox(height: 22),
              Text(
                title,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: titleColor,
                  letterSpacing: -0.2,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                subtitle,
                style: TextStyle(fontSize: 11.5, height: 1.35, color: subtitleColor),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _rowTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: _cardBorder),
          ),
          child: Row(
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: AuthColors.field,
                  borderRadius: BorderRadius.circular(13),
                ),
                child: Icon(icon, size: 22, color: AuthColors.green),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AuthColors.ink,
                      ),
                    ),
                    const SizedBox(height: 1),
                    Text(
                      subtitle,
                      style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right_rounded, color: AuthColors.muted),
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
                  decoration: BoxDecoration(
                    color: AuthColors.yellow.withValues(alpha: 0.3),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.add_a_photo_rounded,
                      size: 21, color: AuthColors.greenDark),
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
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        onTap: () => widget.onSwitchTab?.call(2),
        borderRadius: BorderRadius.circular(18),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: _cardBorder),
          ),
          child: Row(
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: AuthColors.field,
                  borderRadius: BorderRadius.circular(13),
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
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AuthColors.yellow,
        borderRadius: BorderRadius.circular(22),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: const BoxDecoration(
              color: AuthColors.greenDark,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.lightbulb_rounded,
                size: 21, color: AuthColors.yellow),
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
                    color: AuthColors.greenDark,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  tip,
                  style: const TextStyle(
                    fontSize: 12.5,
                    height: 1.45,
                    color: AuthColors.greenDark,
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
          color: const Color(0xFFE3EFE6),
          child: const Center(
            child: Icon(Icons.eco_rounded, color: AuthColors.green, size: 36),
          ),
        );

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(22),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => NewsDetailScreen(article: item)),
        ),
        child: Container(
          width: 250,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: _cardBorder),
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
                          color: AuthColors.yellow,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Text(
                          'Edukasi',
                          style: TextStyle(
                            fontSize: 10.5,
                            fontWeight: FontWeight.w800,
                            color: AuthColors.greenDark,
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
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: _cardBorder),
        boxShadow: shadow
            ? [
                BoxShadow(
                  color: AuthColors.greenDark.withValues(alpha: 0.12),
                  blurRadius: 24,
                  offset: const Offset(0, 10),
                ),
              ]
            : null,
      ),
      child: child,
    );
  }
}