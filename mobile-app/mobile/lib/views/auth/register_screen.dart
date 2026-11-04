import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import 'auth_widgets.dart';
import '../citizen/citizen_main_screen.dart';
import 'auth_hero.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    final auth = context.read<AuthProvider>();
    final success = await auth.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phone: _phoneController.text.trim().isNotEmpty ? _phoneController.text.trim() : null,
      password: _passwordController.text,
      passwordConfirmation: _confirmPasswordController.text,
    );

    if (!mounted) return;

    if (success) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const CitizenMainScreen()),
        (route) => false,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(auth.errorMessage ?? 'Pendaftaran gagal. Periksa input Anda.'),
          backgroundColor: AppTheme.accentDanger,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  Widget _eyeButton() => IconButton(
        icon: Icon(
          _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
          size: 20,
          color: AuthColors.muted,
        ),
        onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
      );

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final isLoading = auth.status == AuthStatus.authenticating;
    final mq = MediaQuery.of(context);
    final heroH = (mq.size.height * 0.30).clamp(200.0, 280.0);
    const overlap = 32.0;

    return Scaffold(
      backgroundColor: const Color(0xFFE9FAF6),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 480),
          child: Container(
            color: Colors.white,
            child: Stack(
              children: [
                // Hero
                Positioned(
                  top: 0,
                  left: 0,
                  right: 0,
                  height: heroH,
                  child: const AuthHero(),
                ),

                // Konten scroll
                SingleChildScrollView(
                  keyboardDismissBehavior:
                      ScrollViewKeyboardDismissBehavior.onDrag,
                  child: Column(
                    children: [
                      SizedBox(height: heroH - overlap),
                      Container(
                        width: double.infinity,
                        constraints: BoxConstraints(
                          minHeight: mq.size.height - heroH + overlap,
                        ),
                        padding: const EdgeInsets.fromLTRB(24, 30, 24, 28),
                        decoration: const BoxDecoration(
                          color: Colors.white,
                          borderRadius:
                              BorderRadius.vertical(top: Radius.circular(36)),
                        ),
                        child: Form(
                          key: _formKey,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              const Text(
                                'Buat akun',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.w800,
                                  color: AuthColors.ink,
                                  letterSpacing: -0.6,
                                ),
                              ),
                              const SizedBox(height: 6),
                              const Text(
                                'Isi data berikut untuk mulai melaporkan sampah di lingkunganmu.',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 13.5,
                                  height: 1.45,
                                  color: AuthColors.muted,
                                ),
                              ),
                              const SizedBox(height: 24),
                              AuthField(
                                label: 'Nama lengkap',
                                hint: 'Nama sesuai KTP',
                                icon: Icons.person_outline_rounded,
                                controller: _nameController,
                                textInputAction: TextInputAction.next,
                                validator: (v) {
                                  if (v == null || v.trim().isEmpty) {
                                    return 'Nama lengkap wajib diisi';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 14),
                              AuthField(
                                label: 'Email',
                                hint: 'nama@email.com',
                                icon: Icons.mail_outline_rounded,
                                controller: _emailController,
                                keyboardType: TextInputType.emailAddress,
                                textInputAction: TextInputAction.next,
                                validator: (v) {
                                  if (v == null || v.trim().isEmpty) {
                                    return 'Email wajib diisi';
                                  }
                                  if (!v.contains('@')) {
                                    return 'Format email tidak valid';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 14),
                              AuthField(
                                label: 'Nomor telepon (opsional)',
                                hint: '081234567890',
                                icon: Icons.phone_outlined,
                                controller: _phoneController,
                                keyboardType: TextInputType.phone,
                                textInputAction: TextInputAction.next,
                              ),
                              const SizedBox(height: 14),
                              AuthField(
                                label: 'Kata sandi',
                                hint: 'Minimal 8 karakter',
                                icon: Icons.lock_outline_rounded,
                                controller: _passwordController,
                                obscureText: _obscurePassword,
                                textInputAction: TextInputAction.next,
                                suffix: _eyeButton(),
                                validator: (v) {
                                  if (v == null || v.isEmpty) {
                                    return 'Kata sandi wajib diisi';
                                  }
                                  if (v.length < 8) {
                                    return 'Kata sandi minimal 8 karakter';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 14),
                              AuthField(
                                label: 'Konfirmasi kata sandi',
                                hint: 'Ulangi kata sandi',
                                icon: Icons.lock_reset_rounded,
                                controller: _confirmPasswordController,
                                obscureText: _obscurePassword,
                                textInputAction: TextInputAction.done,
                                onSubmitted: (_) => _handleRegister(),
                                validator: (v) {
                                  if (v != _passwordController.text) {
                                    return 'Konfirmasi kata sandi tidak cocok';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 26),
                              AuthButton(
                                  label: 'Daftar',
                                  loading: isLoading,
                                  onPressed: _handleRegister),
                              const SizedBox(height: 22),
                              AuthSwitchLine(
                                text: 'Sudah punya akun?',
                                action: 'Masuk',
                                onTap: () => Navigator.pop(context),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                // Tombol kembali di atas (Persis seperti LoginScreen)
                SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        RoundIconButton(
                          icon: Icons.arrow_back_rounded,
                          tooltip: 'Kembali',
                          onPressed: () => Navigator.of(context).maybePop(),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}