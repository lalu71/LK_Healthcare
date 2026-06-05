import 'package:flutter/material.dart';

import '../utils/helpers.dart';

class StatusBadge extends StatelessWidget {
  final String status;
  final IconData? icon;

  const StatusBadge({super.key, required this.status, this.icon});

  @override
  Widget build(BuildContext context) {
    final color = AppHelpers.statusColor(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.25)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 12, color: color),
            const SizedBox(width: 4),
          ] else ...[
            Container(
              width: 6,
              height: 6,
              decoration: BoxDecoration(color: color, shape: BoxShape.circle),
            ),
            const SizedBox(width: 6),
          ],
          Text(
            status[0].toUpperCase() + status.substring(1).toLowerCase(),
            style: TextStyle(
              color: color,
              fontWeight: FontWeight.w600,
              fontSize: 11.5,
              letterSpacing: 0.2,
            ),
          ),
        ],
      ),
    );
  }
}
