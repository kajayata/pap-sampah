import 'package:flutter/material.dart';

class AuthHero extends StatelessWidget {
  final String asset;
  const AuthHero({super.key, this.asset = 'assets/images/auth_hero.png'});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Color(0xFFB4F0E3), Color(0xFFE9FAF6)],
        ),
      ),
      child: Image.asset(
        asset,
        fit: BoxFit.cover,
        alignment: Alignment.bottomCenter,
        width: double.infinity,
        height: double.infinity,
        errorBuilder: (_, __, ___) => const Center(
          child: Icon(Icons.eco_rounded, size: 96, color: Color(0x332E6B47)),
        ),
      ),
    );
  }
}