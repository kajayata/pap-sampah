import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../auth/auth_widgets.dart';
import '../auth/forgot_password_screen.dart'; // Pastikan import halaman lupa sandimu benar
import '../auth/onboarding_screen.dart';

const _pageBg = Color(0xFFF4F6F2);
const _cardBorder = Color(0xFFE4EAE4);
const _danger = Color(0xFFDC2626);
const _greenPrimary = Color(0xFF1F6B45);

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().currentUser;
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    final name = (user?.name ?? '').trim().isEmpty ? 'Warga Masyarakat' : user!.name;
    final village = user?.villageName;
    final area = village != null
        ? 'Kelurahan $village, Sumbersari'
        : 'Kecamatan Sumbersari';
    final phone = (user?.phone ?? '').toString().trim();
    final isPetugas = user?.isPetugas ?? false;

    return Scaffold(
      backgroundColor: _pageBg,
      body: SingleChildScrollView(
        child: Stack(
          children: [
            Positioned(
              top: 0,
              left: 0,
              right: 0,
              height: topPad + 300,
              child: const CustomPaint(painter: HeroPainter(hillBase: 0.8)),
            ),
            Column(
              children: [
                Padding(
                  padding: EdgeInsets.fromLTRB(16, topPad + 12, 16, 0),
                  child: Row(
                    children: [
                      RoundIconButton(
                        icon: Icons.arrow_back_ios_new_rounded,
                        onPressed: () => Navigator.maybePop(context),
                      ),
                      const Expanded(
                        child: Text(
                          'Profil',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
                        ),
                      ),
                      const SizedBox(width: 40),
                    ],
                  ),
                ),
                const SizedBox(height: 10),
                _buildAvatar(name),
                const SizedBox(height: 12),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  child: Text(
                    name,
                    textAlign: TextAlign.center,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      letterSpacing: -0.5,
                    ),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  user?.email ?? '',
                  style: TextStyle(
                    fontSize: 13,
                    color: Colors.white.withValues(alpha: 0.78),
                  ),
                ),
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                  decoration: BoxDecoration(
                    color: AuthColors.yellow,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    _capitalize(user?.role ?? 'masyarakat'),
                    style: const TextStyle(
                      fontSize: 11.5,
                      fontWeight: FontWeight.w800,
                      color: AuthColors.greenDark,
                    ),
                  ),
                ),
                const SizedBox(height: 26),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Informasi Akun
                      _card(
                        shadow: true,
                        child: Column(
                          children: [
                            _infoRow(Icons.mail_outline_rounded, 'Email', user?.email ?? '-'),
                            _divider(),
                            _infoRow(Icons.phone_outlined, 'Nomor telepon',
                                phone.isEmpty ? '-' : phone),
                            _divider(),
                            _infoRow(
                              Icons.location_city_rounded,
                              isPetugas ? 'Wilayah penugasan' : 'Domisili',
                              area,
                            ),
                          ],
                        ),
                      ),
                      
                      const SizedBox(height: 22),

                      // Tombol Ubah Kata Sandi (Menggunakan halaman ForgotPasswordScreen yang sudah ada)
                      SizedBox(
                        height: 54,
                        child: OutlinedButton.icon(
                          style: OutlinedButton.styleFrom(
                            foregroundColor: _greenPrimary,
                            backgroundColor: Colors.white,
                            side: const BorderSide(color: _greenPrimary, width: 1.5),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(27),
                            ),
                          ),
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => ForgotPasswordScreen(
                                  initialEmail: user?.email ?? '',
                                ),
                              ),
                            );
                          },
                          icon: const Icon(Icons.lock_reset_rounded, size: 20),
                          label: const Text(
                            'Ubah Kata Sandi',
                            style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                          ),
                        ),
                      ),

                      const SizedBox(height: 14),
                      
                      // Tombol Keluar Akun
                      SizedBox(
                        height: 54,
                        child: OutlinedButton.icon(
                          style: OutlinedButton.styleFrom(
                            foregroundColor: _danger,
                            backgroundColor: Colors.white,
                            side: const BorderSide(color: _danger, width: 1.5),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(27),
                            ),
                          ),
                          onPressed: () => _handleLogout(context),
                          icon: const Icon(Icons.logout_rounded, size: 19),
                          label: const Text(
                            'Keluar Akun',
                            style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                          ),
                        ),
                      ),

                      const SizedBox(height: 20),
                      const Center(
                        child: Text(
                          'PapSampah · Kecamatan Sumbersari, Jember',
                          style: TextStyle(fontSize: 11.5, color: AuthColors.muted),
                        ),
                      ),
                      SizedBox(height: 24 + bottomPad),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAvatar(String name) {
    return Container(
      width: 88,
      height: 88,
      decoration: BoxDecoration(
        color: AuthColors.yellow,
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white.withValues(alpha: 0.9), width: 4),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.18),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      alignment: Alignment.center,
      child: Text(
        name.trim().isEmpty ? 'W' : name.trim()[0].toUpperCase(),
        style: const TextStyle(
          fontSize: 36,
          fontWeight: FontWeight.w800,
          color: AuthColors.greenDark,
        ),
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: AuthColors.field,
              borderRadius: BorderRadius.circular(13),
            ),
            child: Icon(icon, size: 21, color: AuthColors.green),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(fontSize: 11.5, color: AuthColors.muted),
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AuthColors.ink,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _divider() => const Divider(height: 1, color: _cardBorder);

  Widget _card({required Widget child, bool shadow = false}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
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

  String _capitalize(String s) =>
      s.isEmpty ? s : s[0].toUpperCase() + s.substring(1);

  Future<void> _handleLogout(BuildContext context) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: const Text(
          'Keluar akun?',
          style: TextStyle(fontWeight: FontWeight.w800, color: AuthColors.ink),
        ),
        content: const Text(
          'Kamu perlu masuk lagi untuk melapor dan memantau laporanmu.',
          style: TextStyle(fontSize: 13.5, height: 1.4, color: AuthColors.muted),
        ),
        actionsPadding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
        actions: [
          TextButton(
            style: TextButton.styleFrom(foregroundColor: AuthColors.muted),
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Batal', style: TextStyle(fontWeight: FontWeight.w700)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: _danger,
              foregroundColor: Colors.white,
              elevation: 0,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            ),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Keluar', style: TextStyle(fontWeight: FontWeight.w800)),
          ),
        ],
      ),
    );

    if (confirm == true && context.mounted) {
      await context.read<AuthProvider>().logout();
      if (context.mounted) {
        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (_) => const OnboardingScreen()),
          (route) => false,
        );
      }
    }
  }
}