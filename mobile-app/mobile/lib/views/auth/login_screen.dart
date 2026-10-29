import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/api_constants.dart';
import '../../core/theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import 'auth_widgets.dart';
import 'register_screen.dart';
import '../citizen/citizen_main_screen.dart';
import '../worker/worker_main_screen.dart';
import 'forgot_password_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    final auth = context.read<AuthProvider>();
    final success = await auth.login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
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
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(auth.errorMessage ??
              'Email atau kata sandi salah, atau Akun anda tidak aktif'),
          backgroundColor: AppTheme.accentDanger,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  void _showServerSettingsModal() {
    final urlController = TextEditingController(text: ApiConstants.baseUrl);
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
      ),
      builder: (ctx) {
        return Padding(
          padding: EdgeInsets.only(
            left: 24,
            right: 24,
            top: 24,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Pengaturan Alamat Server API',
                style: TextStyle(
                    fontSize: 16, fontWeight: FontWeight.w800, color: AuthColors.ink),
              ),
              const SizedBox(height: 8),
              const Text(
                'Gunakan http://127.0.0.1:8000/api untuk USB debugging via adb reverse, atau gunakan IP laptop jika terhubung via WiFi.',
                style: TextStyle(fontSize: 12, height: 1.4, color: AuthColors.muted),
              ),
              const SizedBox(height: 16),
              AuthField(
                label: 'Base URL API',
                hint: 'http://127.0.0.1:8000/api',
                icon: Icons.dns_outlined,
                controller: urlController,
                keyboardType: TextInputType.url,
              ),
              const SizedBox(height: 18),
              Row(
                children: [
                  Expanded(
                    child: SizedBox(
                      height: 50,
                      child: OutlinedButton(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AuthColors.ink,
                          side: const BorderSide(color: Color(0xFFD5DDD7), width: 1.5),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(25)),
                        ),
                        onPressed: () async {
                          await ApiConstants.resetToDefault();
                          if (ctx.mounted) Navigator.pop(ctx);
                          setState(() {});
                        },
                        child: const Text('Reset Default'),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: SizedBox(
                      height: 50,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AuthColors.green,
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(25)),
                        ),
                        onPressed: () async {
                          await ApiConstants.setCustomBaseUrl(urlController.text.trim());
                          if (ctx.mounted) Navigator.pop(ctx);
                          setState(() {});
                        },
                        child: const Text('Simpan'),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final isLoading = auth.status == AuthStatus.authenticating;

    return AuthScaffold(
      title: 'Selamat datang kembali',
      subtitle: 'Masuk untuk melaporkan dan memantau sampah di sekitarmu.',
      actions: [
        RoundIconButton(
          icon: Icons.settings_outlined,
          tooltip: 'Konfigurasi Server API',
          onPressed: _showServerSettingsModal,
        ),
      ],
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            AuthField(
              label: 'Email',
              hint: 'nama@email.com',
              icon: Icons.mail_outline_rounded,
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              textInputAction: TextInputAction.next,
              validator: (v) {
                if (v == null || v.trim().isEmpty) return 'Email wajib diisi';
                if (!v.contains('@')) return 'Format email tidak valid';
                return null;
              },
            ),
            const SizedBox(height: 16),
            AuthField(
              label: 'Kata sandi',
              hint: 'Masukkan kata sandi',
              icon: Icons.lock_outline_rounded,
              controller: _passwordController,
              obscureText: _obscurePassword,
              textInputAction: TextInputAction.done,
              onSubmitted: (_) => _handleLogin(),
              suffix: IconButton(
                icon: Icon(
                  _obscurePassword
                      ? Icons.visibility_off_outlined
                      : Icons.visibility_outlined,
                  size: 20,
                  color: AuthColors.muted,
                ),
                onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
              ),
              validator: (v) {
                if (v == null || v.isEmpty) return 'Kata sandi wajib diisi';
                return null;
              },
            ),
            Align(
            alignment: Alignment.centerRight,
            child: TextButton(
              style: TextButton.styleFrom(
                foregroundColor: AuthColors.green,
                padding: const EdgeInsets.only(top: 8),
                minimumSize: Size.zero,
                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
              ),
              onPressed: () => Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (_) => ForgotPasswordScreen(
                    initialEmail: _emailController.text.trim(),
                  ),
                ),
              ),
              child: const Text(
                'Lupa kata sandi?',
                style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700),
              ),
            ),
          ),
            const SizedBox(height: 26),
            AuthButton(label: 'Masuk', loading: isLoading, onPressed: _handleLogin),
            const SizedBox(height: 22),
            AuthSwitchLine(
              text: 'Belum punya akun?',
              action: 'Daftar',
              onTap: () => Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const RegisterScreen()),
              ),
            ),
          ],
        ),
      ),
    );
  }
}