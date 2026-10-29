import 'package:flutter/material.dart';

class AuthColors {
  static const green = Color(0xFF1F6B45);
  static const greenMid = Color(0xFF2E8556);
  static const greenDark = Color(0xFF14492F);
  static const yellow = Color(0xFFF4C542);
  static const ink = Color(0xFF14231B);
  static const muted = Color(0xFF6B7A70);
  static const field = Color(0xFFF3F5F1);
}

/// Latar hijau berlapis (lingkaran lembut + bukit) untuk area hero.
class HeroPainter extends CustomPainter {
  final double hillBase;
  const HeroPainter({this.hillBase = 0.72});

  @override
  void paint(Canvas canvas, Size size) {
    final w = size.width;
    final h = size.height;

    canvas.drawRect(
      Offset.zero & size,
      Paint()
        ..shader = const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AuthColors.greenMid, AuthColors.greenDark],
        ).createShader(Offset.zero & size),
    );

    final soft = Paint()..color = Colors.white.withOpacity(0.06);
    canvas.drawCircle(Offset(w * 0.88, h * 0.12), w * 0.38, soft);
    canvas.drawCircle(Offset(w * 0.02, h * 0.5), w * 0.26, soft);

    final back = Path()
      ..moveTo(0, h * (hillBase + 0.04))
      ..quadraticBezierTo(w * 0.28, h * (hillBase - 0.1), w * 0.58, h * (hillBase + 0.02))
      ..quadraticBezierTo(w * 0.85, h * (hillBase + 0.12), w, h * (hillBase - 0.04))
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(back, Paint()..color = Colors.white.withOpacity(0.07));

    final front = Path()
      ..moveTo(0, h * (hillBase + 0.14))
      ..quadraticBezierTo(w * 0.35, h * (hillBase + 0.02), w * 0.7, h * (hillBase + 0.14))
      ..quadraticBezierTo(w * 0.9, h * (hillBase + 0.2), w, h * (hillBase + 0.1))
      ..lineTo(w, h)
      ..lineTo(0, h)
      ..close();
    canvas.drawPath(front, Paint()..color = Colors.black.withOpacity(0.08));
  }

  @override
  bool shouldRepaint(covariant HeroPainter old) => old.hillBase != hillBase;
}

class BrandMark extends StatelessWidget {
  final double size;
  const BrandMark({super.key, this.size = 48});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: AuthColors.yellow,
        borderRadius: BorderRadius.circular(size * 0.3),
      ),
      child: Icon(Icons.recycling_rounded, size: size * 0.58, color: AuthColors.greenDark),
    );
  }
}

class RoundIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback? onPressed;
  final String? tooltip;
  const RoundIconButton({super.key, required this.icon, this.onPressed, this.tooltip});

  @override
  Widget build(BuildContext context) {
    return Tooltip(
      message: tooltip ?? '',
      child: InkWell(
        onTap: onPressed,
        customBorder: const CircleBorder(),
        child: Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.16),
            shape: BoxShape.circle,
          ),
          child: Icon(icon, size: 18, color: Colors.white),
        ),
      ),
    );
  }
}

/// Kerangka halaman auth: hero hijau di atas + sheet putih berisi form.
class AuthScaffold extends StatelessWidget {
  final String title;
  final String subtitle;
  final Widget child;
  final List<Widget> actions;
  final bool showLogo;
  final double heroHeight;

  const AuthScaffold({
    super.key,
    required this.title,
    required this.subtitle,
    required this.child,
    this.actions = const [],
    this.showLogo = true,
    this.heroHeight = 230,
  });

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);
    final keyboardOpen = mq.viewInsets.bottom > 0;
    final heroH = mq.padding.top + (keyboardOpen ? 120 : heroHeight);
    final bottomPad = mq.viewInsets.bottom > mq.padding.bottom
        ? mq.viewInsets.bottom
        : mq.padding.bottom;

    return Scaffold(
      resizeToAvoidBottomInset: false,
      backgroundColor: AuthColors.greenDark,
      body: Stack(
        children: [
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 250),
              curve: Curves.easeOut,
              height: heroH,
              child: const CustomPaint(size: Size.infinite, painter: HeroPainter()),
            ),
          ),
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: SafeArea(
              bottom: false,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        RoundIconButton(
                          icon: Icons.arrow_back_ios_new_rounded,
                          onPressed: () => Navigator.maybePop(context),
                        ),
                        const Spacer(),
                        ...actions,
                      ],
                    ),
                    Padding(
                      padding: const EdgeInsets.fromLTRB(8, 14, 8, 0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (showLogo && !keyboardOpen) ...[
                            const BrandMark(size: 48),
                            const SizedBox(height: 14),
                          ],
                          Text(
                            title,
                            style: const TextStyle(
                              fontSize: 26,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                              letterSpacing: -0.6,
                              height: 1.1,
                            ),
                          ),
                          if (!keyboardOpen) ...[
                            const SizedBox(height: 6),
                            Text(
                              subtitle,
                              style: TextStyle(
                                fontSize: 13,
                                height: 1.4,
                                color: Colors.white.withOpacity(0.78),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          AnimatedPositioned(
            duration: const Duration(milliseconds: 250),
            curve: Curves.easeOut,
            top: heroH - 28,
            left: 0,
            right: 0,
            bottom: 0,
            child: Container(
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
              ),
              child: SingleChildScrollView(
                padding: EdgeInsets.fromLTRB(24, 28, 24, 24 + bottomPad),
                child: child,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class AuthField extends StatelessWidget {
  final String label;
  final String? hint;
  final IconData icon;
  final TextEditingController controller;
  final String? Function(String?)? validator;
  final TextInputType? keyboardType;
  final TextInputAction? textInputAction;
  final bool obscureText;
  final Widget? suffix;
  final ValueChanged<String>? onSubmitted;

  const AuthField({
    super.key,
    required this.label,
    required this.icon,
    required this.controller,
    this.hint,
    this.validator,
    this.keyboardType,
    this.textInputAction,
    this.obscureText = false,
    this.suffix,
    this.onSubmitted,
  });

  OutlineInputBorder _border(Color color, [double width = 1]) => OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: color == Colors.transparent
            ? BorderSide.none
            : BorderSide(color: color, width: width),
      );

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 12.5,
            fontWeight: FontWeight.w600,
            color: AuthColors.ink,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          obscureText: obscureText,
          keyboardType: keyboardType,
          textInputAction: textInputAction,
          onFieldSubmitted: onSubmitted,
          validator: validator,
          style: const TextStyle(fontSize: 14, color: AuthColors.ink),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: const TextStyle(fontSize: 14, color: Color(0xFF9AA6A0)),
            filled: true,
            fillColor: AuthColors.field,
            prefixIcon: Icon(icon, size: 20, color: AuthColors.muted),
            suffixIcon: suffix,
            contentPadding: const EdgeInsets.symmetric(vertical: 15, horizontal: 14),
            border: _border(Colors.transparent),
            enabledBorder: _border(Colors.transparent),
            focusedBorder: _border(AuthColors.green, 1.5),
            errorBorder: _border(const Color(0xFFDC2626)),
            focusedErrorBorder: _border(const Color(0xFFDC2626), 1.5),
            errorStyle: const TextStyle(fontSize: 11.5),
          ),
        ),
      ],
    );
  }
}

class AuthButton extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final bool loading;
  const AuthButton({super.key, required this.label, this.onPressed, this.loading = false});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 54,
      child: ElevatedButton(
        style: ElevatedButton.styleFrom(
          backgroundColor: AuthColors.green,
          foregroundColor: Colors.white,
          disabledBackgroundColor: AuthColors.green.withOpacity(0.6),
          elevation: 0,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(27)),
        ),
        onPressed: loading ? null : onPressed,
        child: loading
            ? const SizedBox(
                width: 20,
                height: 20,
                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
              )
            : Text(label, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
      ),
    );
  }
}

class AuthSwitchLine extends StatelessWidget {
  final String text;
  final String action;
  final VoidCallback onTap;
  const AuthSwitchLine({super.key, required this.text, required this.action, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text('$text ', style: const TextStyle(fontSize: 13, color: AuthColors.muted)),
        GestureDetector(
          onTap: onTap,
          child: Text(
            action,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w700,
              color: AuthColors.green,
            ),
          ),
        ),
      ],
    );
  }
}