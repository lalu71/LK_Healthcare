import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../config/app_colors.dart';
import '../config/routes.dart';
import '../controllers/auth_controller.dart';
import '../utils/constants.dart';

class AppDrawer extends StatelessWidget {
  const AppDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthController>();
    final user = auth.user;
    final isDoctor = auth.role == UserRole.doctor;

    return Drawer(
      backgroundColor: AppColors.surface,
      width: MediaQuery.of(context).size.width * 0.78,
      child: SafeArea(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: isDoctor
                    ? const LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [AppColors.accent, AppColors.accentDark],
                      )
                    : AppColors.primaryGradient,
              ),
              width: double.infinity,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 60,
                    height: 60,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Icon(
                        isDoctor
                            ? Icons.medical_services_rounded
                            : Icons.person_rounded,
                        color: Colors.white,
                        size: 32),
                  ),
                  const SizedBox(height: 12),
                  Text(
                      isDoctor && (user?.name ?? '').isNotEmpty
                          ? 'Dr. ${user!.name}'
                          : user?.name ?? 'Guest',
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.w700)),
                  Text(user?.email ?? '',
                      style: TextStyle(
                          color: Colors.white.withOpacity(0.85), fontSize: 12)),
                  const SizedBox(height: 8),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      (user?.role ?? 'patient').toUpperCase(),
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10.5,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.8),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(vertical: 8),
                children: [
                  _item(
                      context,
                      Icons.dashboard_rounded,
                      'Dashboard',
                      isDoctor
                          ? AppRoutes.doctorDashboard
                          : AppRoutes.patientDashboard),
                  _item(
                      context,
                      Icons.person_outline_rounded,
                      'My Profile',
                      isDoctor
                          ? AppRoutes.doctorProfile
                          : AppRoutes.patientProfile),
                  const Divider(height: 1),
                  _section('Health'),
                  if (!isDoctor)
                    _item(context, Icons.medical_services_outlined,
                        'Find Doctors', AppRoutes.doctorsList),
                  if (!isDoctor) ...[
                    _item(context, Icons.event_outlined, 'My Appointments',
                        AppRoutes.myAppointments),
                    _item(context, Icons.medication_outlined, 'Prescriptions',
                        AppRoutes.prescriptions),
                    _item(context, Icons.science_outlined, 'Lab Tests',
                        AppRoutes.labTests),
                    _item(context, Icons.local_pharmacy_outlined, 'Pharmacy',
                        AppRoutes.pharmacy),
                    _item(context, Icons.folder_outlined, 'Medical Records',
                        AppRoutes.medicalRecords),
                  ],
                  if (isDoctor) ...[
                    _item(context, Icons.event_outlined, 'Appointments',
                        AppRoutes.doctorAppointments),
                    _item(context, Icons.schedule_outlined, 'Availability',
                        AppRoutes.doctorAvailability),
                    _item(context, Icons.medication_outlined, 'Prescriptions',
                        AppRoutes.doctorPrescriptions),
                    _item(context, Icons.add_circle_outline_rounded,
                        'Create Prescription',
                        AppRoutes.createPrescription),
                  ],
                  const Divider(height: 1),
                  _section('Services'),
                  _item(context, Icons.bloodtype_outlined, 'Blood Bank',
                      AppRoutes.bloodBank),
                  _item(context, Icons.local_hospital_rounded, 'Emergency SOS',
                      AppRoutes.emergency,
                      color: AppColors.danger),
                  _item(context, Icons.notifications_outlined, 'Notifications',
                      AppRoutes.notifications),
                  const Divider(height: 1),
                  _section('Info'),
                  _item(context, Icons.info_outline_rounded, 'About Us',
                      AppRoutes.about),
                  _item(context, Icons.support_agent_rounded, 'Contact',
                      AppRoutes.contact),
                ],
              ),
            ),
            const Divider(height: 1),
            ListTile(
              leading: const Icon(Icons.logout_rounded, color: AppColors.danger),
              title: const Text('Logout',
                  style: TextStyle(
                      color: AppColors.danger, fontWeight: FontWeight.w600)),
              onTap: () async {
                Navigator.pop(context);
                await context.read<AuthController>().logout();
                if (context.mounted) {
                  Navigator.pushNamedAndRemoveUntil(
                      context, AppRoutes.login, (_) => false);
                }
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }

  Widget _section(String title) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 12, 20, 4),
        child: Text(title.toUpperCase(),
            style: const TextStyle(
                color: AppColors.textMuted,
                fontSize: 11,
                fontWeight: FontWeight.w700,
                letterSpacing: 1.0)),
      );

  Widget _item(BuildContext context, IconData icon, String label, String route,
      {Color? color}) {
    return ListTile(
      leading: Icon(icon, size: 22, color: color ?? AppColors.textSecondary),
      title: Text(label,
          style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
              color: color ?? AppColors.textPrimary)),
      onTap: () {
        Navigator.pop(context);
        Navigator.pushNamed(context, route);
      },
    );
  }
}
