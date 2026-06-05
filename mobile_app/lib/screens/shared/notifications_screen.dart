import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../controllers/notification_controller.dart';
import '../../models/notification.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<NotificationController>().fetch();
    });
  }

  IconData _iconFor(String? type) {
    switch (type) {
      case 'appointment':
        return Icons.event_rounded;
      case 'prescription':
        return Icons.medication_rounded;
      case 'lab':
        return Icons.science_rounded;
      case 'pharmacy':
        return Icons.local_pharmacy_rounded;
      case 'payment':
        return Icons.payments_rounded;
      case 'emergency':
        return Icons.local_hospital_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  Color _colorFor(String? type) {
    switch (type) {
      case 'appointment':
        return AppColors.primary;
      case 'prescription':
        return AppColors.accent;
      case 'lab':
        return AppColors.warning;
      case 'pharmacy':
        return AppColors.accent;
      case 'payment':
        return AppColors.success;
      case 'emergency':
        return AppColors.danger;
      default:
        return AppColors.info;
    }
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<NotificationController>();
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Notifications'),
        actions: [
          if (ctrl.unreadCount > 0)
            TextButton(
              onPressed: () => ctrl.markAllRead(),
              child: const Text('Mark all read'),
            ),
        ],
      ),
      body: ctrl.loading
          ? const LoadingView()
          : ctrl.items.isEmpty
              ? const EmptyState(
                  icon: Icons.notifications_off_rounded,
                  title: 'No notifications',
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: ctrl.items.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 8),
                  itemBuilder: (_, i) => _Tile(
                    notification: ctrl.items[i],
                    icon: _iconFor(ctrl.items[i].type),
                    color: _colorFor(ctrl.items[i].type),
                    onTap: () => ctrl.markRead(ctrl.items[i].id),
                  ),
                ),
    );
  }
}

class _Tile extends StatelessWidget {
  final AppNotification notification;
  final IconData icon;
  final Color color;
  final VoidCallback? onTap;

  const _Tile({
    required this.notification,
    required this.icon,
    required this.color,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: notification.isRead
          ? AppColors.surface
          : AppColors.primary.withOpacity(0.05),
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: notification.isRead
                  ? AppColors.border
                  : AppColors.primary.withOpacity(0.3),
            ),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Stack(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.12),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(icon, color: color, size: 22),
                  ),
                  if (!notification.isRead)
                    Positioned(
                      right: 0,
                      top: 0,
                      child: Container(
                        width: 10,
                        height: 10,
                        decoration: const BoxDecoration(
                          color: AppColors.danger,
                          shape: BoxShape.circle,
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(notification.title,
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: notification.isRead
                              ? FontWeight.w600
                              : FontWeight.w800,
                          color: AppColors.textPrimary,
                        )),
                    const SizedBox(height: 2),
                    Text(notification.message,
                        style: const TextStyle(
                            fontSize: 12.5,
                            color: AppColors.textSecondary,
                            height: 1.4)),
                    const SizedBox(height: 6),
                    Text(AppHelpers.relative(notification.createdAt),
                        style: const TextStyle(
                            fontSize: 11, color: AppColors.textMuted)),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
