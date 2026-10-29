import 'package:flutter/material.dart';
import 'auth_widgets.dart';
import 'login_screen.dart';
import 'register_screen.dart';

class OnboardingScreen extends StatelessWidget {
  const OnboardingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AuthColors.greenDark,
      body: Stack(
        children: [
          const Positioned.fill(
            child: CustomPaint(painter: HeroPainter(hillBase: 0.62)),
          ),
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(28, 16, 28, 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Row(
                    children: [
                      const BrandMark(size: 36),
                      const SizedBox(width: 10),
                      const Text(
                        'PapSampah',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                          letterSpacing: -0.3,
                        ),
                      ),
                    ],
                  ),
                  const Expanded(child: _Illustration()),
                  Text.rich(
                    TextSpan(
                      style: const TextStyle(
                        fontSize: 30,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        height: 1.15,
                        letterSpacing: -0.8,
                      ),
                      children: const [
                        TextSpan(text: 'Kelola & pantau sampah '),
                        TextSpan(
                          text: 'lebih mudah',
                          style: TextStyle(color: AuthColors.yellow),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    'Laporkan tumpukan sampah liar di sekitar Kecamatan Sumbersari dan pastikan wilayah kita bersih bersama petugas.',
                    style: TextStyle(
                      fontSize: 14,
                      height: 1.5,
                      color: Colors.white.withOpacity(0.78),
                    ),
                  ),
                  const SizedBox(height: 28),
                  SizedBox(
                    height: 54,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.white,
                        foregroundColor: AuthColors.green,
                        elevation: 0,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(27)),
                      ),
                      onPressed: () => Navigator.of(context).push(
                        MaterialPageRoute(builder: (_) => const LoginScreen()),
                      ),
                      child: const Text('Masuk',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                    ),
                  ),
                  const SizedBox(height: 10),
                  SizedBox(
                    height: 54,
                    child: OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.white,
                        side: BorderSide(color: Colors.white.withOpacity(0.45), width: 1.5),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(27)),
                      ),
                      onPressed: () => Navigator.of(context).push(
                        MaterialPageRoute(builder: (_) => const RegisterScreen()),
                      ),
                      child: const Text('Daftar Akun Baru',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Ilustrasi: dua kartu laporan yang menyerupai tampilan di dalam aplikasi.
class _Illustration extends StatelessWidget {
  const _Illustration();

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, c) {
        final circle = (c.maxHeight * 0.78).clamp(160.0, 260.0);
        return Stack(
          alignment: Alignment.center,
          children: [
            Container(
              width: circle,
              height: circle,
              decoration: const BoxDecoration(
                color: AuthColors.yellow,
                shape: BoxShape.circle,
              ),
              child: const Center(
                child: Icon(
                  Icons.delete, // Ganti Icons.trash dengan Icons.delete di Flutter
                  color: Colors.white, // Sesuaikan warna ikonnya (misal: putih atau hitam)
                  size: 100,// Sesuaikan ukuran ikonnya
                ),
              ),
            ),
            Positioned(
              left: 0,
              top: c.maxHeight * 0.16,
              child: Transform.rotate(
                angle: -0.07,
                child: const _ReportCard(
                  icon: Icons.delete_outline_rounded,
                  title: 'Sampah menumpuk',
                  meta: 'Sumbersari · 2 menit lalu',
                  chip: 'Dilaporkan',
                  chipColor: Color(0xFFFFEDC2),
                  chipTextColor: Color(0xFF8A5A00),
                ),
              ),
            ),
            Positioned(
              right: 0,
              bottom: c.maxHeight * 0.14,
              child: Transform.rotate(
                angle: 0.06,
                child: const _ReportCard(
                  icon: Icons.check_rounded,
                  title: 'Sudah dibersihkan',
                  meta: 'Oleh petugas lapangan',
                  chip: 'Selesai',
                  chipColor: Color(0xFFDDF0E3),
                  chipTextColor: AuthColors.green,
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}

class _ReportCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String meta;
  final String chip;
  final Color chipColor;
  final Color chipTextColor;

  const _ReportCard({
    required this.icon,
    required this.title,
    required this.meta,
    required this.chip,
    required this.chipColor,
    required this.chipTextColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 232,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.18),
            blurRadius: 24,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: const Color(0xFFE6F0E9),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: AuthColors.green, size: 22),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 13, fontWeight: FontWeight.w700, color: AuthColors.ink)),
                const SizedBox(height: 2),
                Text(meta,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 11, color: AuthColors.muted)),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: chipColor,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(chip,
                      style: TextStyle(
                          fontSize: 10.5,
                          fontWeight: FontWeight.w700,
                          color: chipTextColor)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}