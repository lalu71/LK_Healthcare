import 'package:flutter/material.dart';

/// Premium healthcare-themed color palette.
class AppColors {
  AppColors._();

  // Brand
  static const Color primary = Color(0xFF1A73E8); // Trustworthy blue
  static const Color primaryDark = Color(0xFF0B5BCB);
  static const Color primaryLight = Color(0xFF5EA0F2);
  static const Color accent = Color(0xFF00BFA6); // Healing teal
  static const Color accentDark = Color(0xFF00897B);

  // Status
  static const Color success = Color(0xFF22C55E);
  static const Color warning = Color(0xFFF59E0B);
  static const Color danger = Color(0xFFEF4444);
  static const Color info = Color(0xFF3B82F6);

  // Neutral
  static const Color bg = Color(0xFFF6F8FB);
  static const Color surface = Colors.white;
  static const Color surfaceAlt = Color(0xFFF1F5F9);
  static const Color border = Color(0xFFE2E8F0);
  static const Color divider = Color(0xFFEDF2F7);

  // Text
  static const Color textPrimary = Color(0xFF0F172A);
  static const Color textSecondary = Color(0xFF475569);
  static const Color textMuted = Color(0xFF94A3B8);
  static const Color textOnPrimary = Colors.white;

  // Gradients
  static const LinearGradient primaryGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF1A73E8), Color(0xFF00BFA6)],
  );

  static const LinearGradient cardGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF1A73E8), Color(0xFF5EA0F2)],
  );

  static const LinearGradient dangerGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFFEF4444), Color(0xFFF87171)],
  );

  static const LinearGradient successGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF22C55E), Color(0xFF4ADE80)],
  );

  // Role colors
  static const Color patientColor = Color(0xFF1A73E8);
  static const Color doctorColor = Color(0xFF00BFA6);
  static const Color pharmacistColor = Color(0xFFF59E0B);
  static const Color adminColor = Color(0xFF8B5CF6);
}
