import 'package:flutter/material.dart';

import '../config/app_colors.dart';

/// Premium gradient header used on dashboards.
class GradientHeader extends StatelessWidget {
  final String greeting;
  final String name;
  final String? subtitle;
  final Widget? trailing;
  final IconData? avatarIcon;
  final List<Widget> stats;
  final Gradient? gradient;

  const GradientHeader({
    super.key,
    required this.greeting,
    required this.name,
    this.subtitle,
    this.trailing,
    this.avatarIcon,
    this.stats = const [],
    this.gradient,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 24, 20, 24),
      decoration: BoxDecoration(
        gradient: gradient ?? AppColors.primaryGradient,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(28),
          bottomRight: Radius.circular(28),
        ),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.2),
            offset: const Offset(0, 8),
            blurRadius: 20,
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.white.withOpacity(0.3)),
                ),
                child: Icon(avatarIcon ?? Icons.person_rounded,
                    color: Colors.white, size: 26),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(greeting,
                        style: TextStyle(
                            color: Colors.white.withOpacity(0.85),
                            fontSize: 13,
                            fontWeight: FontWeight.w500)),
                    const SizedBox(height: 2),
                    Text(name,
                        style: const TextStyle(
                            color: Colors.white,
                            fontSize: 19,
                            fontWeight: FontWeight.w700)),
                  ],
                ),
              ),
              if (trailing != null) trailing!,
            ],
          ),
          if (subtitle != null) ...[
            const SizedBox(height: 12),
            Text(subtitle!,
                style: TextStyle(
                    color: Colors.white.withOpacity(0.9),
                    fontSize: 13,
                    height: 1.4)),
          ],
          if (stats.isNotEmpty) ...[
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.16),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.white.withOpacity(0.2)),
              ),
              child: Row(
                children: [
                  for (int i = 0; i < stats.length; i++) ...[
                    Expanded(child: stats[i]),
                    if (i < stats.length - 1)
                      Container(
                          width: 1,
                          height: 32,
                          color: Colors.white.withOpacity(0.25)),
                  ],
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class HeaderStat extends StatelessWidget {
  final String label;
  final String value;
  final IconData? icon;

  const HeaderStat({
    super.key,
    required this.label,
    required this.value,
    this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        if (icon != null) Icon(icon, color: Colors.white, size: 18),
        if (icon != null) const SizedBox(height: 4),
        Text(value,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.w700)),
        const SizedBox(height: 2),
        Text(label,
            style: TextStyle(
                color: Colors.white.withOpacity(0.8), fontSize: 11)),
      ],
    );
  }
}
