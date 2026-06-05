import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../config/app_colors.dart';
import '../config/routes.dart';
import '../controllers/notification_controller.dart';
import 'app_drawer.dart';

/// Common scaffold used across authenticated screens.
/// Wraps body with AppBar (with notification + drawer), drawer, and proper bg.
class RoleScaffold extends StatelessWidget {
  final String title;
  final Widget body;
  final List<Widget>? actions;
  final Widget? floatingActionButton;
  final Widget? bottomNavigationBar;
  final bool showAppBar;
  final bool showDrawer;
  final bool showBack;

  const RoleScaffold({
    super.key,
    required this.title,
    required this.body,
    this.actions,
    this.floatingActionButton,
    this.bottomNavigationBar,
    this.showAppBar = true,
    this.showDrawer = true,
    this.showBack = true,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      drawer: showDrawer ? const AppDrawer() : null,
      appBar: showAppBar
          ? AppBar(
              title: Text(title),
              automaticallyImplyLeading: showBack || !showDrawer,
              actions: [
                Consumer<NotificationController>(
                  builder: (context, ctrl, _) {
                    final count = ctrl.unreadCount;
                    return Stack(
                      alignment: Alignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.notifications_none_rounded),
                          onPressed: () => Navigator.pushNamed(
                              context, AppRoutes.notifications),
                        ),
                        if (count > 0)
                          Positioned(
                            right: 8,
                            top: 8,
                            child: Container(
                              padding: const EdgeInsets.all(4),
                              constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                              decoration: const BoxDecoration(
                                color: AppColors.danger,
                                shape: BoxShape.circle,
                              ),
                              child: Text(
                                count > 9 ? '9+' : '$count',
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 9,
                                    fontWeight: FontWeight.bold),
                              ),
                            ),
                          ),
                      ],
                    );
                  },
                ),
                ...?actions,
                const SizedBox(width: 4),
              ],
            )
          : null,
      body: body,
      floatingActionButton: floatingActionButton,
      bottomNavigationBar: bottomNavigationBar,
    );
  }
}
