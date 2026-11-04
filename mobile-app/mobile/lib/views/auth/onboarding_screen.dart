import 'dart:math' as math;
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'auth_widgets.dart';
import 'login_screen.dart';
import 'register_screen.dart';

// ---- Ilustrasi (PNG transparan: tong + sampah + gelembung notifikasi) ----
const _heroAsset = 'assets/images/sampah.png';

// ---- Pita diagonal (bagian dari tinggi area tengah) -------------------
const _bandThickness = 0.54; // tebal pita
const _bandSlope = 0.32; // seberapa miring (turun dari kiri ke kanan)

// ---- Ukuran ------------------------------------------------------------
const _maxWidth = 480.0; // lebar maksimum kolom di browser lebar
const _minMiddle = 230.0; // tinggi minimum area ilustrasi (layar pendek: scroll)

// ---- Palet (sama dengan beranda) ---------------------------------------
const _bg = Color(0xFFFBFFFA);
const _border = Color(0xFFD5DEE6);
const _sage = Color(0xFFEBF7E3);
const _sky = Color(0xFFDBF4FF);
const _aqua = Color(0xFF9EF4E7);
const _outside = Color(0xFFE9F1EC);

class OnboardingScreen extends StatelessWidget {
  const OnboardingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: _outside,
        body: LayoutBuilder(
          builder: (context, c) {
            final w = math.min(c.maxWidth, _maxWidth);
            return Center(
              child: SizedBox(
                width: w,
                height: c.maxHeight,
                child: ColoredBox(
                  color: _bg,
                  child: SingleChildScrollView(
                    physics: const ClampingScrollPhysics(),
                    child: ConstrainedBox(
                      constraints: BoxConstraints(minHeight: c.maxHeight),
                      child: IntrinsicHeight(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            _buildTop(mq.padding.top),
                            Expanded(child: _buildMiddle()),
                            _buildButtons(context, mq.padding.bottom),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  // ------------------------------------------------------------ Atas

  Widget _buildTop(double topPad) {
    return Padding(
      padding: EdgeInsets.fromLTRB(28, topPad + 18, 28, 8),
      child: _FadeUp(
        duration: const Duration(milliseconds: 600),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Row(
              children: [
                BrandMark(size: 38),
                SizedBox(width: 10),
                Text(
                  'Pap-Sampah',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: AuthColors.ink,
                    letterSpacing: -0.3,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 26),
            FittedBox(
              fit: BoxFit.scaleDown,
              alignment: Alignment.centerLeft,
              child: const Text.rich(
                TextSpan(
                  style: TextStyle(
                    fontSize: 32,
                    fontWeight: FontWeight.w800,
                    color: AuthColors.ink,
                    height: 1.12,
                    letterSpacing: -1,
                  ),
                  children: [
                    TextSpan(text: 'Kelola & pantau\nsampah '),
                    TextSpan(
                      text: 'lebih mudah',
                      style: TextStyle(color: AuthColors.green),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 12),
            const Text(
              'Laporkan tumpukan sampah liar di sekitar Kecamatan Sumbersari dan pastikan wilayah kita bersih bersama petugas.',
              style: TextStyle(
                fontSize: 13.5,
                height: 1.5,
                color: AuthColors.muted,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ------------------------------------------------------------ Tengah

  Widget _buildMiddle() {
    return ConstrainedBox(
      constraints: const BoxConstraints(minHeight: _minMiddle),
      child: Stack(
        children: [
          const Positioned.fill(
            child: CustomPaint(painter: _BandPainter()),
          ),
          Positioned.fill(
            child: _FadeUp(
              duration: const Duration(milliseconds: 900),
              child: Align(
                child: FractionallySizedBox(
                  widthFactor: 0.94,
                  heightFactor: 0.8,
                  child: Image.asset(
                    _heroAsset,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) =>
                        _heroFallback(),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // Tampil jika file gambar belum ada / path salah.
  Widget _heroFallback() {
    return Column(
      mainAxisSize: MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Container(
          width: 110,
          height: 110,
          decoration: const BoxDecoration(
            color: Colors.white,
            shape: BoxShape.circle,
          ),
          child: const Icon(Icons.recycling_rounded,
              size: 56, color: AuthColors.green),
        ),
        if (kDebugMode) ...[
          const SizedBox(height: 10),
          const Text(
            'Letakkan gambar di\n$_heroAsset',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 11.5, color: AuthColors.muted),
          ),
        ],
      ],
    );
  }

  // ------------------------------------------------------------ Tombol

  Widget _buildButtons(BuildContext context, double bottomPad) {
    return Padding(
      padding: EdgeInsets.fromLTRB(28, 12, 28, 24 + bottomPad),
      child: _FadeUp(
        duration: const Duration(milliseconds: 1000),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            SizedBox(
              width: double.infinity,
              height: 54,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AuthColors.green,
                  foregroundColor: Colors.white,
                  elevation: 4,
                  shadowColor: AuthColors.greenDark.withValues(alpha: 0.4),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(27)),
                ),
                onPressed: () => Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                ),
                child: const Text(
                  'Masuk',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                ),
              ),
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              height: 54,
              child: OutlinedButton(
                style: OutlinedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: AuthColors.ink,
                  side: const BorderSide(color: _border, width: 1.5),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(27)),
                ),
                onPressed: () => Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const RegisterScreen()),
                ),
                child: const Text(
                  'Daftar Akun Baru',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Pita diagonal di belakang ilustrasi. Bentuknya dihitung dari ukuran area,
/// jadi tetap proporsional di layar apa pun.
class _BandPainter extends CustomPainter {
  const _BandPainter();

  @override
  void paint(Canvas canvas, Size size) {
    canvas.clipRect(Offset.zero & size);

    final w = size.width;
    final h = size.height;
    final cy = h * 0.5;
    final t = h * _bandThickness;
    final slope = math.min(w * _bandSlope, h * _bandSlope);

    Path band(double dy, double thickness) => Path()
      ..moveTo(0, cy - slope / 2 - thickness / 2 + dy)
      ..lineTo(w, cy + slope / 2 - thickness / 2 + dy)
      ..lineTo(w, cy + slope / 2 + thickness / 2 + dy)
      ..lineTo(0, cy - slope / 2 + thickness / 2 + dy)
      ..close();

    // Garis aqua tipis di atas pita.
    const stripe = 8.0;
    const gap = 10.0;
    canvas.drawPath(
      band(-(t / 2 + gap + stripe / 2), stripe),
      Paint()..color = _aqua.withValues(alpha: 0.45),
    );

    // Pita utama.
    canvas.drawPath(
      band(0, t),
      Paint()
        ..shader = const LinearGradient(
          colors: [_sage, _sky],
        ).createShader(Offset.zero & size),
    );
  }

  @override
  bool shouldRepaint(covariant _BandPainter oldDelegate) => false;
}

/// Muncul pelan sambil naik sedikit.
class _FadeUp extends StatelessWidget {
  final Widget child;
  final Duration duration;
  const _FadeUp({required this.child, required this.duration});

  @override
  Widget build(BuildContext context) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0, end: 1),
      duration: duration,
      curve: Curves.easeOutCubic,
      child: child,
      builder: (context, v, child) => Opacity(
        opacity: v,
        child: Transform.translate(
          offset: Offset(0, 18 * (1 - v)),
          child: child,
        ),
      ),
    );
  }
}