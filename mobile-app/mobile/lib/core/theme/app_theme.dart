import 'package:flutter/material.dart';

class AppTheme {
  // Brand Colors from ui-context.md
  static const Color emphasis = Color(0xFF5B7E3C);       // Primary green
  static const Color accentSecondary = Color(0xFFFFD65A); // Warm golden yellow
  static const Color accentWarning = Color(0xFFFF9D23);   // Energetic orange
  static const Color accentDanger = Color(0xFFEA5252);    // Alert red

  // Ergonomic aliases
  static const Color primaryColor = emphasis;
  static const Color secondaryColor = accentSecondary;
  static const Color danger = accentDanger;
  static const Color warning = accentWarning;

  // Neutral tones
  static const Color base = Color(0xFFFAFAF5);           // Soft off-white
  static const Color surface = Color(0xFFFFFFFF);        // Pure white card
  static const Color textPrimary = Color(0xFF1C1C1C);    // Deep dark
  static const Color textDark = textPrimary;
  static const Color textMuted = Color(0x991C1C1C);      // 60% dark
  static const Color borderDefault = Color(0x14000000);  // 8% black
  static const Color borderAccent = Color(0x335B7E3C);   // 20% green

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: base,
      colorScheme: ColorScheme.fromSeed(
        seedColor: emphasis,
        primary: emphasis,
        secondary: accentSecondary,
        surface: surface,
        error: accentDanger,
        onPrimary: Colors.white,
        onSecondary: textPrimary,
        onSurface: textPrimary,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: surface,
        elevation: 0,
        centerTitle: false,
        scrolledUnderElevation: 0.5,
        iconTheme: IconThemeData(color: textPrimary),
        titleTextStyle: TextStyle(
          color: textPrimary,
          fontSize: 18,
          fontWeight: FontWeight.bold,
          letterSpacing: -0.2,
        ),
      ),
      cardTheme: CardThemeData(
        color: surface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: const BorderSide(color: borderDefault, width: 1),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: emphasis,
          foregroundColor: Colors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          textStyle: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surface,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: borderDefault),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: borderDefault),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: emphasis, width: 1.5),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: accentDanger),
        ),
        labelStyle: const TextStyle(color: textMuted, fontSize: 14),
        hintStyle: const TextStyle(color: textMuted, fontSize: 13),
      ),
    );
  }
}
