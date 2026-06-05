import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/appointment_controller.dart';
import '../../widgets/appointment_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/role_scaffold.dart';

class MyAppointmentsScreen extends StatefulWidget {
  const MyAppointmentsScreen({super.key});

  @override
  State<MyAppointmentsScreen> createState() => _MyAppointmentsScreenState();
}

class _MyAppointmentsScreenState extends State<MyAppointmentsScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AppointmentController>().fetchAppointments();
    });
  }

  @override
  void dispose() {
    _tabs.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<AppointmentController>();
    return RoleScaffold(
      title: 'My Appointments',
      body: Column(
        children: [
          Container(
            color: AppColors.surface,
            child: TabBar(
              controller: _tabs,
              labelColor: AppColors.primary,
              unselectedLabelColor: AppColors.textSecondary,
              indicatorColor: AppColors.primary,
              indicatorWeight: 3,
              labelStyle: const TextStyle(fontWeight: FontWeight.w700),
              tabs: const [
                Tab(text: 'Upcoming'),
                Tab(text: 'History'),
              ],
            ),
          ),
          Expanded(
            child: ctrl.loading
                ? const LoadingView()
                : TabBarView(
                    controller: _tabs,
                    children: [
                      _list(ctrl.upcoming, true),
                      _list(ctrl.history, false),
                    ],
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.pushNamed(context, AppRoutes.doctorsList),
        icon: const Icon(Icons.add_rounded),
        label: const Text('Book New'),
      ),
    );
  }

  Widget _list(List items, bool upcoming) {
    if (items.isEmpty) {
      return EmptyState(
        icon: upcoming ? Icons.event_busy_rounded : Icons.history_rounded,
        title: upcoming ? 'No upcoming appointments' : 'No past appointments',
        message: upcoming ? 'Book your next consultation' : 'Your visit history will appear here',
        actionLabel: upcoming ? 'Book Now' : null,
        onAction: upcoming
            ? () => Navigator.pushNamed(context, AppRoutes.doctorsList)
            : null,
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: items.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (_, i) {
        final a = items[i];
        return AppointmentCard(
          appointment: a,
          onTap: () => Navigator.pushNamed(
              context, AppRoutes.appointmentDetail,
              arguments: a),
          onCancel: upcoming
              ? () => _confirmCancel(a.id)
              : null,
        );
      },
    );
  }

  void _confirmCancel(int id) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cancel Appointment?'),
        content: const Text(
            'Are you sure you want to cancel? You can rebook anytime.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Keep')),
          TextButton(
            onPressed: () {
              context.read<AppointmentController>().cancelAppointment(id);
              Navigator.pop(context);
            },
            style: TextButton.styleFrom(foregroundColor: AppColors.danger),
            child: const Text('Cancel Appointment'),
          ),
        ],
      ),
    );
  }
}
