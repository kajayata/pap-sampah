import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/constants/api_constants.dart';
import '../providers/auth_provider.dart';
import 'auth/auth_widgets.dart'; // Memuat HeroPainter & warna konsisten
import 'auth/onboarding_screen.dart';
import 'citizen/citizen_main_screen.dart';
import 'worker/worker_main_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    );
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeIn),
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
    await Future.delayed(const Duration(seconds: 2)); // Durasi splash agar estetis
    if (!mounted) return;

    final auth = context.read<AuthProvider>();
    await auth.checkAuthStatus();

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
    return Scaffold(
      backgroundColor: AuthColors.greenDark, // Warna dasar hijau tua senada Onboarding
      body: Stack(
        children: [
          // Latar belakang gelombang bukit khas (HeroPainter)
          const Positioned.fill(
            child: CustomPaint(
              painter: HeroPainter(hillBase: 0.75),
            ),
          ),
          SafeArea(
            child: Center(
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // Lingkaran ikon dengan aksen kuning hangat khas Onboarding
                    Container(
                      width: 96,
                      height: 96,
                      decoration: BoxDecoration(
                        color: AuthColors.yellow,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.15),
                            blurRadius: 24,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.delete_sweep_rounded,
                        color: AuthColors.greenDark,
                        size: 48,
                      ),
                    ),
                    const SizedBox(height: 24),
                    // Judul Aplikasi
                    RichText(
                      text: const TextSpan(
                        style: TextStyle(
                          fontSize: 32,
                          fontWeight: FontWeight.w800,
                          letterSpacing: -0.5,
                        ),
                        children: [
                          TextSpan(text: 'Pap', style: TextStyle(color: Colors.white)),
                          TextSpan(text: 'Sampah', style: TextStyle(color: AuthColors.yellow)),
                        ],
                      ),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'Kecamatan Sumbersari — Jember',
                      style: TextStyle(
                        color: Colors.white70,
                        fontSize: 13,
                        fontWeight: FontWeight.w500,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 48),
                    const SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(
                        strokeWidth: 2.5,
                        valueColor: AlwaysStoppedAnimation<Color>(AuthColors.yellow),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}