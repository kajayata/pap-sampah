import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/constants/api_constants.dart';
import '../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';
import 'auth/login_screen.dart';
import 'citizen/citizen_main_screen.dart';
import 'worker/worker_main_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkInitialState();
  }

  Future<void> _checkInitialState() async {
    await ApiConstants.init();
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
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.emphasis,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                boxShadow: const [
                  BoxShadow(
                    color: Colors.black12,
                    blurRadius: 16,
                    offset: Offset(0, 6),
                  ),
                ],
              ),
              child: const Icon(
                Icons.delete_sweep_rounded,
                color: AppTheme.emphasis,
                size: 48,
              ),
            ),
            const SizedBox(height: 20),
            RichText(
              text: const TextSpan(
                style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                children: [
                  TextSpan(text: 'Pap'),
                  TextSpan(text: 'Sampah', style: TextStyle(color: AppTheme.accentSecondary)),
                ],
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Kecamatan Sumbersari — Jember',
              style: TextStyle(color: Colors.white70, fontSize: 13, letterSpacing: 0.5),
            ),
            const SizedBox(height: 48),
            const SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white),
            ),
          ],
        ),
      ),
    );
  }
}
