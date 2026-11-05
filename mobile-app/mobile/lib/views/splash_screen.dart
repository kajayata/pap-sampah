import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../core/constants/api_constants.dart';
import '../providers/auth_provider.dart';
import 'auth/auth_widgets.dart';
import 'auth/onboarding_screen.dart';
import 'citizen/citizen_main_screen.dart';
import 'worker/worker_main_screen.dart';

// ---- Palet (sama dengan beranda) ---------------------------------------
const _bg = Color(0xFFFBFFFA);
const _sage = Color(0xFFEBF7E3);
const _sky = Color(0xFFDBF4FF);
const _aqua = Color(0xFF9EF4E7);

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _logoScale;
  late final Animation<double> _logoFade;
  late final Animation<double> _textFade;
  late final Animation<double> _loaderFade;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    );
    _logoScale = Tween<double>(begin: 0.6, end: 1.0).animate(
      CurvedAnimation(
        parent: _controller,
        curve: const Interval(0.0, 0.55, curve: Curves.easeOutBack),
      ),
    );
    _logoFade = CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.0, 0.4, curve: Curves.easeOut),
    );
    _textFade = CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.35, 0.8, curve: Curves.easeOut),
    );
    _loaderFade = CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.6, 1.0, curve: Curves.easeOut),
    );

    _controller.forward();
    _checkInitialState();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _checkInitialState() async {
    await ApiConstants.init();
    if (!mounted) return;

    final auth = context.read<AuthProvider>();
    // Cek sesi berjalan bersamaan dengan durasi splash minimum 2 detik,
    // jadi total tunggu = yang terlama dari keduanya (bukan dijumlahkan).
    await Future.wait([
      Future.delayed(const Duration(seconds: 2)),
      auth.checkAuthStatus(),
    ]);

    if (!mounted) return;

    if (auth.isAuthenticated) {
      final user = auth.currentUser;
      if (user != null && user.isPetugas) {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const WorkerMainScreen()),
        );
      } else {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const CitizenMainScreen()),
        );
      }
    } else {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const OnboardingScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: _bg,
        body: Stack(
          children: [
            // Latar: putih ke aqua lembut + bukit pastel di bawah.
            const Positioned.fill(
              child: CustomPaint(painter: _SplashBackgroundPainter()),
            ),
            SafeArea(
              child: Center(
                child: AnimatedBuilder(
                  animation: _controller,
                  builder: (context, _) {
                    return Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Opacity(
                          opacity: _logoFade.value,
                          child: Transform.scale(
                            scale: _logoScale.value,
                            child: _buildLogo(),
                          ),
                        ),
                        const SizedBox(height: 26),
                        Opacity(
                          opacity: _textFade.value,
                          child: Transform.translate(
                            offset: Offset(0, 14 * (1 - _textFade.value)),
                            child: _buildTexts(),
                          ),
                        ),
                        const SizedBox(height: 44),
                        Opacity(
                          opacity: _loaderFade.value,
                          child: const SizedBox(
                            width: 26,
                            height: 26,
                            child: CircularProgressIndicator(
                              strokeWidth: 2.8,
                              strokeCap: StrokeCap.round,
                              valueColor:
                                  AlwaysStoppedAnimation<Color>(AuthColors.green),
                              backgroundColor: _sage,
                            ),
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLogo() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        shape: BoxShape.circle,
        border: Border.all(color: const Color(0xFFD5DEE6)),
        boxShadow: [
          BoxShadow(
            color: AuthColors.greenDark.withValues(alpha: 0.12),
            blurRadius: 28,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: const BrandMark(size: 76),
    );
  }

  Widget _buildTexts() {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        RichText(
          text: const TextSpan(
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.w800,
              letterSpacing: -0.9,
            ),
            children: [
              TextSpan(text: 'Pap-', style: TextStyle(color: AuthColors.ink)),
              TextSpan(
                text: 'Sampah',
                style: TextStyle(color: AuthColors.green),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
          decoration: BoxDecoration(
            color: _sage,
            borderRadius: BorderRadius.circular(20),
          ),
          child: const Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.location_on_rounded,
                  size: 14, color: AuthColors.green),
              SizedBox(width: 6),
              Text(
                'Kecamatan Sumbersari, Jember',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: AuthColors.green,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

/// Latar splash: gradien putih → aqua lembut, lingkaran samar, dan tiga
/// lapis bukit pastel. Dihitung dari ukuran layar, jadi tetap proporsional.
class _SplashBackgroundPainter extends CustomPainter {
  const _SplashBackgroundPainter();

  @override
  void paint(Canvas canvas, Size size) {
    final w = size.width;
    final h = size.height;
    final rect = Offset.zero & size;

    canvas.drawRect(
      rect,
      Paint()
        ..shader = LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Colors.white, _bg, _aqua.withValues(alpha: 0.35)],
          stops: const [0.0, 0.55, 1.0],
        ).createShader(rect),
    );

    // Lingkaran samar
    canvas.drawCircle(
      Offset(w * 0.9, h * 0.1),
      w * 0.34,
      Paint()..color = _sky.withValues(alpha: 0.7),
    );
    canvas.drawCircle(
      Offset(w * 0.04, h * 0.3),
      w * 0.22,
      Paint()..color = _sage.withValues(alpha: 0.8),
    );

    // Bukit belakang (aqua)
    final back = Path()
      ..moveTo(0, h * 0.84)
      ..quadraticBezierTo(w * 0.3, h * 0.74, w * 0.6, h * 0.82)
      ..quadraticBezierTo(w * 0.85, h * 0.88, w, h * 0.8)
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(back, Paint()..color = _aqua.withValues(alpha: 0.5));

    // Bukit tengah (biru muda)
    final mid = Path()
      ..moveTo(0, h * 0.89)
      ..quadraticBezierTo(w * 0.35, h * 0.81, w * 0.7, h * 0.89)
      ..quadraticBezierTo(w * 0.9, h * 0.93, w, h * 0.88)
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(mid, Paint()..color = _sky);

    // Bukit depan (hijau pucat)
    final front = Path()
      ..moveTo(0, h * 0.94)
      ..quadraticBezierTo(w * 0.4, h * 0.88, w * 0.75, h * 0.94)
      ..quadraticBezierTo(w * 0.92, h * 0.97, w, h * 0.93)
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(front, Paint()..color = _sage);
  }

  @override
  bool shouldRepaint(covariant _SplashBackgroundPainter oldDelegate) => false;
}