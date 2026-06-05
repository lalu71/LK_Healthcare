import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/appointment_controller.dart';
import '../../widgets/appointment_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/role_scaffold.dart';

class DoctorAppointmentsScreen extends StatefulWidget {
  const DoctorAppointmentsScreen({super.key});

  @override
  State<DoctorAppointmentsScreen> createState() => _DoctorAppointmentsScreenState();
}

class _DoctorAppointmentsScreenState extends State<DoctorAppointmentsScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 3, vsync: this);
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
    final today = ctrl.appointments
        .where((a) => _isSameDay(a.appointmentDate, DateTime.now()))
        .toList();
    final upcoming = ctrl.upcoming;
    final history = ctrl.history;

    return RoleScaffold(
      title: 'Appointments',
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
              tabs: [
                Tab(text: 'Today (${today.length})'),
                Tab(text: 'Upcoming (${upcoming.length})'),
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
                      _list(today, 'today'),
                      _list(upcoming, 'upcoming'),
                      _list(history, 'past'),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  bool _isSameDay(DateTime a, DateTime b) =>
      a.year == b.year && a.month == b.month && a.day == b.day;

  Widget _list(List items, String type) {
    if (items.isEmpty) {
      return EmptyState(
        icon: Icons.event_busy_rounded,
        title: type == 'today'
            ? 'No appointments today'
            : type == 'upcoming'
                ? 'No upcoming appointments'
                : 'No past appointments',
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
        );
      },
    );
  }
}
