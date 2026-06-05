import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/appointment_controller.dart';
import '../../controllers/auth_controller.dart';
import '../../controllers/doctor_controller.dart';
import '../../controllers/notification_controller.dart';
import '../../widgets/appointment_card.dart';
import '../../widgets/doctor_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/feature_card.dart';
import '../../widgets/gradient_header.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/section_header.dart';

class PatientDashboardScreen extends StatefulWidget {
  const PatientDashboardScreen({super.key});

  @override
  State<PatientDashboardScreen> createState() => _PatientDashboardScreenState();
}

class _PatientDashboardScreenState extends State<PatientDashboardScreen> {
  int _bottomIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DoctorController>().fetchDoctors();
      context.read<AppointmentController>().fetchAppointments();
      context.read<NotificationController>().fetch();
    });
  }

  void _onBottomTap(int i) {
    setState(() => _bottomIndex = i);
    switch (i) {
      case 1:
        Navigator.pushNamed(context, AppRoutes.myAppointments);
        break;
      case 2:
        Navigator.pushNamed(context, AppRoutes.prescriptions);
        break;
      case 3:
        Navigator.pushNamed(context, AppRoutes.patientProfile);
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthController>().user;
    final appts = context.watch<AppointmentController>().upcoming;
    final doctors = context.watch<DoctorController>().doctors;

    return RoleScaffold(
      title: 'Home',
      showBack: false,
      body: RefreshIndicator(
        onRefresh: () async {
          await context.read<DoctorController>().fetchDoctors();
          await context.read<AppointmentController>().fetchAppointments();
        },
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            GradientHeader(
              greeting: 'Hello,',
              name: user?.name ?? 'Patient',
              subtitle: 'How are you feeling today? Stay healthy and book in advance.',
              avatarIcon: Icons.person_rounded,
              stats: [
                HeaderStat(label: 'Upcoming', value: '${appts.length}', icon: Icons.event_outlined),
                const HeaderStat(label: 'Records', value: '0', icon: Icons.folder_outlined),
                const HeaderStat(label: 'Reports', value: '2', icon: Icons.description_outlined),
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
                        icon: Icons.calendar_today_rounded,
                        title: 'Book',
                        color: AppColors.primary,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.doctorsList),
                      ),
                      FeatureCard(
                        icon: Icons.science_rounded,
                        title: 'Lab',
                        color: AppColors.warning,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.labTests),
                      ),
                      FeatureCard(
                        icon: Icons.local_pharmacy_rounded,
                        title: 'Medicines',
                        color: AppColors.accent,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.pharmacy),
                      ),
                      FeatureCard(
                        icon: Icons.local_hospital_rounded,
                        title: 'SOS',
                        color: AppColors.danger,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.emergency),
                      ),
                      FeatureCard(
                        icon: Icons.bloodtype_rounded,
                        title: 'Blood',
                        color: AppColors.danger,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.bloodBank),
                      ),
                      FeatureCard(
                        icon: Icons.medication_rounded,
                        title: 'Rx',
                        color: AppColors.primary,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.prescriptions),
                      ),
                      FeatureCard(
                        icon: Icons.folder_special_rounded,
                        title: 'Records',
                        color: AppColors.adminColor,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.medicalRecords),
                      ),
                      FeatureCard(
                        icon: Icons.support_agent_rounded,
                        title: 'Help',
                        color: AppColors.info,
                        onTap: () => Navigator.pushNamed(context, AppRoutes.contact),
                      ),
                    ],
                  ),
                  const SizedBox(height: 22),
                  SectionHeader(
                    title: 'Upcoming Appointments',
                    actionLabel: 'View All',
                    onActionTap: () =>
                        Navigator.pushNamed(context, AppRoutes.myAppointments),
                  ),
                  const SizedBox(height: 10),
                  if (appts.isEmpty)
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: const EmptyState(
                        icon: Icons.event_busy_rounded,
                        title: 'No upcoming appointments',
                        message: 'Book your next consultation now',
                      ),
                    )
                  else
                    ...appts.take(2).map((a) => Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: AppointmentCard(
                            appointment: a,
                            onTap: () => Navigator.pushNamed(
                                context, AppRoutes.appointmentDetail,
                                arguments: a),
                          ),
                        )),
                  const SizedBox(height: 18),
                  SectionHeader(
                    title: 'Top Doctors',
                    actionLabel: 'See All',
                    onActionTap: () =>
                        Navigator.pushNamed(context, AppRoutes.doctorsList),
                  ),
                  const SizedBox(height: 10),
                  ...doctors.take(3).map((d) => Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: DoctorCard(
                          doctor: d,
                          onTap: () => Navigator.pushNamed(
                              context, AppRoutes.bookAppointment,
                              arguments: d),
                        ),
                      )),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
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
