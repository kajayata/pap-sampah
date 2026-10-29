import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/password_reset_service.dart';
import 'auth_widgets.dart';

class ForgotPasswordScreen extends StatefulWidget {
  final String initialEmail;
  const ForgotPasswordScreen({super.key, this.initialEmail = ''});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _emailFormKey = GlobalKey<FormState>();
  final _resetFormKey = GlobalKey<FormState>();
  late final TextEditingController _emailController;
  final _codeController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmController = TextEditingController();

  int _step = 0; // 0 = isi email, 1 = isi kode + kata sandi baru
  bool _loading = false;
  bool _obscure = true;
  int _cooldown = 0;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _emailController = TextEditingController(text: widget.initialEmail);
  }

  @override
  void dispose() {
    _timer?.cancel();
    _emailController.dispose();
    _codeController.dispose();
    _passwordController.dispose();
    _confirmController.dispose();
    super.dispose();
  }

  void _startCooldown() {
    _timer?.cancel();
    setState(() => _cooldown = 60);
    _timer = Timer.periodic(const Duration(seconds: 1), (t) {
      if (!mounted) return t.cancel();
      setState(() => _cooldown--);
      if (_cooldown <= 0) t.cancel();
    });
  }

  void _toast(String msg, {bool error = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: error ? const Color(0xFFDC2626) : AuthColors.green,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  Future<void> _sendCode({bool resend = false}) async {
    if (!resend && !_emailFormKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final err = await PasswordResetService.requestCode(_emailController.text.trim());
    if (!mounted) return;
    setState(() => _loading = false);

    if (err != null) {
      _toast(err, error: true);
      return;
    }
    _startCooldown();
    if (resend) {
      _toast('Kode baru telah dikirim.');
    } else {
      setState(() => _step = 1);
    }
  }

  Future<void> _resetPassword() async {
    if (!_resetFormKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final err = await PasswordResetService.reset(
      email: _emailController.text.trim(),
      code: _codeController.text.trim(),
      password: _passwordController.text,
      passwordConfirmation: _confirmController.text,
    );
    if (!mounted) return;
    setState(() => _loading = false);

    if (err != null) {
      _toast(err, error: true);
      return;
    }
    _toast('Kata sandi berhasil diubah. Silakan masuk.');
    Navigator.of(context).pop();
  }

  Widget _eye() => IconButton(
        icon: Icon(
          _obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
          size: 20,
          color: AuthColors.muted,
        ),
        onPressed: () => setState(() => _obscure = !_obscure),
      );

  @override
  Widget build(BuildContext context) {
    return AuthScaffold(
      title: _step == 0 ? 'Lupa kata sandi?' : 'Atur ulang kata sandi',
      subtitle: _step == 0
          ? 'Masukkan email akunmu, kami kirim kode verifikasi.'
          : 'Masukkan kode yang dikirim ke ${_emailController.text.trim()}.',
      showLogo: false,
      heroHeight: 180,
      child: _step == 0 ? _buildEmailStep() : _buildResetStep(),
    );
  }

  Widget _buildEmailStep() {
    return Form(
      key: _emailFormKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          AuthField(
            label: 'Email',
            hint: 'nama@email.com',
            icon: Icons.mail_outline_rounded,
            controller: _emailController,
            keyboardType: TextInputType.emailAddress,
            textInputAction: TextInputAction.done,
            onSubmitted: (_) => _sendCode(),
            validator: (v) {
              if (v == null || v.trim().isEmpty) return 'Email wajib diisi';
              if (!v.contains('@')) return 'Format email tidak valid';
              return null;
            },
          ),
          const SizedBox(height: 26),
          AuthButton(label: 'Kirim kode', loading: _loading, onPressed: _sendCode),
          const SizedBox(height: 22),
          AuthSwitchLine(
            text: 'Ingat kata sandimu?',
            action: 'Masuk',
            onTap: () => Navigator.of(context).maybePop(),
          ),
        ],
      ),
    );
  }

  Widget _buildResetStep() {
    return Form(
      key: _resetFormKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          AuthField(
            label: 'Kode verifikasi',
            hint: '6 digit angka',
            icon: Icons.pin_outlined,
            controller: _codeController,
            keyboardType: TextInputType.number,
            textInputAction: TextInputAction.next,
            validator: (v) {
              if (v == null || !RegExp(r'^\d{6}$').hasMatch(v.trim())) {
                return 'Kode harus 6 digit angka';
              }
              return null;
            },
          ),
          const SizedBox(height: 14),
          AuthField(
            label: 'Kata sandi baru',
            hint: 'Minimal 8 karakter',
            icon: Icons.lock_outline_rounded,
            controller: _passwordController,
            obscureText: _obscure,
            textInputAction: TextInputAction.next,
            suffix: _eye(),
            validator: (v) {
              if (v == null || v.isEmpty) return 'Kata sandi wajib diisi';
              if (v.length < 8) return 'Kata sandi minimal 8 karakter';
              return null;
            },
          ),
          const SizedBox(height: 14),
          AuthField(
            label: 'Konfirmasi kata sandi',
            hint: 'Ulangi kata sandi baru',
            icon: Icons.lock_reset_rounded,
            controller: _confirmController,
            obscureText: _obscure,
            textInputAction: TextInputAction.done,
            onSubmitted: (_) => _resetPassword(),
            validator: (v) {
              if (v != _passwordController.text) {
                return 'Konfirmasi kata sandi tidak cocok';
              }
              return null;
            },
          ),
          const SizedBox(height: 26),
          AuthButton(
            label: 'Ubah kata sandi',
            loading: _loading,
            onPressed: _resetPassword,
          ),
          const SizedBox(height: 18),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Text('Kode tidak masuk? ',
                  style: TextStyle(fontSize: 13, color: AuthColors.muted)),
              GestureDetector(
                onTap: (_cooldown > 0 || _loading) ? null : () => _sendCode(resend: true),
                child: Text(
                  _cooldown > 0 ? 'Kirim ulang ($_cooldown dtk)' : 'Kirim ulang',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: _cooldown > 0 ? AuthColors.muted : AuthColors.green,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Center(
            child: GestureDetector(
              onTap: () => setState(() => _step = 0),
              child: const Text(
                'Ganti email',
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: AuthColors.green,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}