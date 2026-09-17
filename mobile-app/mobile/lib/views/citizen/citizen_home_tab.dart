import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/news_article.dart';
import '../../providers/auth_provider.dart';
import '../../providers/public_provider.dart';
import '../../providers/report_provider.dart';
import 'create_report_screen.dart';
import 'news_detail_screen.dart';

class CitizenHomeTab extends StatefulWidget {
  final Function(int tabIndex)? onSwitchTab;

  const CitizenHomeTab({super.key, this.onSwitchTab});

  @override
  State<CitizenHomeTab> createState() => _CitizenHomeTabState();
}

class _CitizenHomeTabState extends State<CitizenHomeTab> {
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

    return Scaffold(
      body: RefreshIndicator(
        color: AppTheme.primaryColor,
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
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Hero Green Header
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 52, 20, 24),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF43602A), AppTheme.primaryColor],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(28),
                    bottomRight: Radius.circular(28),
                  ),
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
                              const SizedBox(height: 2),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                decoration: BoxDecoration(
                                  color: AppTheme.secondaryColor.withValues(alpha: 0.3),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  user?.villageName != null
                                      ? 'Kelurahan ${user!.villageName!}'
                                      : 'Kecamatan Sumbersari, Jember',
                                  style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w600),
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
              ),

              const SizedBox(height: 20),

              // Report Statistics Card
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: _buildReportStatistics(),
              ),

              const SizedBox(height: 20),

              // Quick Actions Menu
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Layanan Cepat',
                      style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: _buildActionTile(
                            icon: Icons.camera_alt_rounded,
                            iconBg: const Color(0xFFE8F5E9),
                            iconColor: AppTheme.primaryColor,
                            title: 'Lapor Sampah',
                            subtitle: 'Kirim foto & GPS',
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (_) => const CreateReportScreen()),
                              );
                            },
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildActionTile(
                            icon: Icons.map_rounded,
                            iconBg: const Color(0xFFFEF3C7),
                            iconColor: const Color(0xFFB45309),
                            title: 'Peta Spasial',
                            subtitle: 'Sebaran titik sampah',
                            onTap: () => widget.onSwitchTab?.call(1),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: _buildActionTile(
                            icon: Icons.recycling_rounded,
                            iconBg: const Color(0xFFCCFBF1),
                            iconColor: const Color(0xFF0F766E),
                            title: 'Bank Sampah',
                            subtitle: 'Daftar lokasi & TPA',
                            onTap: () => widget.onSwitchTab?.call(3),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildActionTile(
                            icon: Icons.assignment_rounded,
                            iconBg: const Color(0xFFE0E7FF),
                            iconColor: const Color(0xFF4338CA),
                            title: 'Laporan Saya',
                            subtitle: 'Riwayat pengaduan',
                            onTap: () => widget.onSwitchTab?.call(2),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              // Educational Articles & News Section
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Edukasi & Berita Terkini',
                      style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                    Consumer<PublicProvider>(
                      builder: (context, pub, child) {
                        return Text(
                          '${pub.newsList.length} Artikel',
                          style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                        );
                      },
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),
              _buildNewsSection(),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildWeatherCard() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        final weather = prov.weather;

        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.15),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withValues(alpha: 0.25)),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: const Icon(Icons.wb_sunny_rounded, color: AppTheme.secondaryColor, size: 30),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Text(
                          weather != null ? '${weather.temperature.round()}${weather.temperatureUnit}' : '28°C',
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          weather?.condition ?? 'Cerah Berawan',
                          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.white),
                        ),
                      ],
                    ),
                    const SizedBox(height: 2),
                    Text(
                      weather != null
                          ? 'Kelembapan: ${weather.humidity}% • Angin: ${weather.windSpeed} km/h'
                          : 'Sumbersari, Kabupaten Jember',
                      style: TextStyle(fontSize: 11, color: Colors.white.withValues(alpha: 0.85)),
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

  Widget _buildReportStatistics() {
    return Consumer<ReportProvider>(
      builder: (context, prov, child) {
        final total = prov.reports.length;
        final pending = prov.reports.where((r) => r.isPending).length;
        final inProgress = prov.reports.where((r) => r.isInProgress || r.isValidated).length;
        final resolved = prov.reports.where((r) => r.isResolved).length;

        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
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
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatItem('Total', total.toString(), AppTheme.primaryColor),
              _buildStatDivider(),
              _buildStatItem('Menunggu', pending.toString(), const Color(0xFFD97706)),
              _buildStatDivider(),
              _buildStatItem('Diproses', inProgress.toString(), const Color(0xFF2563EB)),
              _buildStatDivider(),
              _buildStatItem('Selesai', resolved.toString(), AppTheme.emphasis),
            ],
          ),
        );
      },
    );
  }

  Widget _buildStatItem(String label, String value, Color color) {
    return Column(
      children: [
        Text(value, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: color)),
        const SizedBox(height: 2),
        Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted, fontWeight: FontWeight.w500)),
      ],
    );
  }

  Widget _buildStatDivider() {
    return Container(height: 28, width: 1, color: Colors.grey.shade200);
  }

  Widget _buildActionTile({
    required IconData icon,
    required Color iconBg,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.shade200),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.03),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: iconBg, borderRadius: BorderRadius.circular(14)),
              child: Icon(icon, color: iconColor, size: 24),
            ),
            const SizedBox(height: 12),
            Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
            const SizedBox(height: 2),
            Text(subtitle, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
          ],
        ),
      ),
    );
  }

  Widget _buildNewsSection() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        if (prov.isLoadingNews) {
          return const Center(
            child: Padding(
              padding: EdgeInsets.all(32),
              child: CircularProgressIndicator(color: AppTheme.primaryColor),
            ),
          );
        }

        if (prov.newsList.isEmpty) {
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: const Center(
                child: Text('Belum ada artikel edukasi lingkungan', style: TextStyle(color: AppTheme.textMuted)),
              ),
            ),
          );
        }

        return SizedBox(
          height: 230,
          child: ListView.separated(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            scrollDirection: Axis.horizontal,
            itemCount: prov.newsList.length,
            separatorBuilder: (context, index) => const SizedBox(width: 14),
            itemBuilder: (context, index) {
              final item = prov.newsList[index];
              return _buildNewsCard(item);
            },
          ),
        );
      },
    );
  }

  Widget _buildNewsCard(NewsArticle item) {
    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => NewsDetailScreen(article: item)),
        );
      },
      borderRadius: BorderRadius.circular(20),
      child: Container(
        width: 260,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.shade200),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 8,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Thumbnail
            SizedBox(
              height: 120,
              width: double.infinity,
              child: item.thumbnailUrl != null
                  ? Image.network(
                      item.thumbnailUrl!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        color: AppTheme.primaryColor.withValues(alpha: 0.1),
                        child: const Center(
                          child: Icon(Icons.eco_rounded, color: AppTheme.primaryColor, size: 36),
                        ),
                      ),
                    )
                  : Container(
                      color: AppTheme.primaryColor.withValues(alpha: 0.1),
                      child: const Center(
                        child: Icon(Icons.eco_rounded, color: AppTheme.primaryColor, size: 36),
                      ),
                    ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppTheme.textDark, height: 1.3),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    item.authorName ?? 'Admin Kebersihan',
                    style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
