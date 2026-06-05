import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/appointment_controller.dart';
import '../../controllers/auth_controller.dart';
import '../../controllers/notification_controller.dart';
import '../../widgets/appointment_card.dart';
import '../../widgets/feature_card.dart';
import '../../widgets/gradient_header.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/section_header.dart';

class DoctorDashboardScreen extends StatefulWidget {
  const DoctorDashboardScreen({super.key});

  @override
  State<DoctorDashboardScreen> createState() => _DoctorDashboardScreenState();
}

class _DoctorDashboardScreenState extends State<DoctorDashboardScreen> {
  int _bottomIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AppointmentController>().fetchAppointments();
      context.read<NotificationController>().fetch();
    });
  }

  void _onBottomTap(int i) {
    setState(() => _bottomIndex = i);
    switch (i) {
      case 1:
        Navigator.pushNamed(context, AppRoutes.doctorAppointments);
        break;
      case 2:
        Navigator.pushNamed(context, AppRoutes.doctorPrescriptions);
        break;
      case 3:
        Navigator.pushNamed(context, AppRoutes.doctorProfile);
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthController>().user;
    final upcoming = context.watch<AppointmentController>().upcoming;

    return RoleScaffold(
      title: 'Doctor Home',
      showBack: false,
      body: ListView(
        padding: EdgeInsets.zero,
        children: [
          GradientHeader(
            greeting: 'Good morning,',
            name: 'Dr. ${user?.name ?? "Doctor"}',
            subtitle: 'You have ${upcoming.length} appointments today. Have a great day!',
            avatarIcon: Icons.medical_services_rounded,
            gradient: const LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [AppColors.accent, AppColors.accentDark],
            ),
            stats: [
              HeaderStat(label: 'Today', value: '${upcoming.length}', icon: Icons.today_rounded),
              const HeaderStat(label: 'This Week', value: '24', icon: Icons.calendar_view_week_rounded),
              const HeaderStat(label: 'Patients', value: '156', icon: Icons.people_rounded),
            ],
          ),
          const SizedBox(height: 20),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SectionHeader(title: 'Quick Actions'),
                const SizedBox(height: 10),
                GridView.count(
                  crossAxisCount: 4,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                  childAspectRatio: 0.85,
                  children: [
                    FeatureCard(
                      icon: Icons.event_outlined,
                      title: 'Appts',
                      color: AppColors.primary,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.doctorAppointments),
                    ),
                    FeatureCard(
                      icon: Icons.schedule_rounded,
                      title: 'Slots',
                      color: AppColors.warning,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.doctorAvailability),
                    ),
                    FeatureCard(
                      icon: Icons.medication_rounded,
                      title: 'Rx',
                      color: AppColors.accent,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.doctorPrescriptions),
                    ),
                    FeatureCard(
                      icon: Icons.add_circle_rounded,
                      title: 'New Rx',
                      color: AppColors.adminColor,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.createPrescription),
                    ),
                    FeatureCard(
                      icon: Icons.person_outline_rounded,
                      title: 'Profile',
                      color: AppColors.info,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.doctorProfile),
                    ),
                    FeatureCard(
                      icon: Icons.bloodtype_rounded,
                      title: 'Blood',
                      color: AppColors.danger,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.bloodBank),
                    ),
                    FeatureCard(
                      icon: Icons.notifications_outlined,
                      title: 'Alerts',
                      color: AppColors.warning,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.notifications),
                    ),
                    FeatureCard(
                      icon: Icons.support_agent_rounded,
                      title: 'Help',
                      color: AppColors.primary,
                      onTap: () => Navigator.pushNamed(context, AppRoutes.contact),
                    ),
                  ],
                ),
                const SizedBox(height: 22),
                SectionHeader(
                  title: "Today's Appointments",
                  actionLabel: 'View All',
                  onActionTap: () => Navigator.pushNamed(context, AppRoutes.doctorAppointments),
                ),
                const SizedBox(height: 10),
                if (upcoming.isEmpty)
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: const Center(
                      child: Text('No appointments today',
                          style: TextStyle(color: AppColors.textMuted)),
                    ),
                  )
                else
                  ...upcoming.take(3).map((a) => Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: AppointmentCard(appointment: a),
                      )),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _bottomIndex,
        onTap: _onBottomTap,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_rounded), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.event_outlined), label: 'Appointments'),
          BottomNavigationBarItem(icon: Icon(Icons.medication_outlined), label: 'Rx'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline_rounded), label: 'Profile'),
        ],
      ),
    );
  }
}
