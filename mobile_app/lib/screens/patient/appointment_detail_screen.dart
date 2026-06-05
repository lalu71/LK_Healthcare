import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../models/appointment.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/status_badge.dart';

class AppointmentDetailScreen extends StatefulWidget {
  const AppointmentDetailScreen({super.key});

  @override
  State<AppointmentDetailScreen> createState() => _AppointmentDetailScreenState();
}

class _AppointmentDetailScreenState extends State<AppointmentDetailScreen> {
  @override
  Widget build(BuildContext context) {
    final appt = ModalRoute.of(context)?.settings.arguments as Appointment?;
    if (appt == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Appointment')),
        body: const Center(child: Text('No appointment provided')),
      );
    }
    final doctor = appt.doctor;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Appointment Details')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    Container(
                      width: 64,
                      height: 64,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.22),
                        borderRadius: BorderRadius.circular(18),
                      ),
                      child: Center(
                        child: Text(
                          (doctor?.name ?? 'D').replaceAll('Dr. ', '').isNotEmpty
                              ? (doctor!.name).replaceAll('Dr. ', '')[0]
                              : 'D',
                          style: const TextStyle(
                              color: Colors.white,
                              fontSize: 26,
                              fontWeight: FontWeight.w800),
                        ),
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(doctor?.name ?? 'Doctor',
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 18,
                                  fontWeight: FontWeight.w800)),
                          Text(doctor?.specialization ?? '',
                              style: TextStyle(
                                  color: Colors.white.withOpacity(0.85),
                                  fontSize: 13)),
                        ],
                      ),
                    ),
                    StatusBadge(status: appt.status),
                  ],
                ),
                const SizedBox(height: 18),
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Row(
                    children: [
                      _info(Icons.calendar_today_rounded, 'Date',
                          AppHelpers.formatDate(appt.appointmentDate)),
                      Container(width: 1, height: 30, color: Colors.white24),
                      _info(Icons.access_time_rounded, 'Time', appt.slot ?? '—'),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          _detailCard('Reason for Visit', appt.reason ?? '—',
              Icons.medical_information_rounded),
          _detailCard('Consultation Fee', AppHelpers.currency(appt.fee ?? 0),
              Icons.currency_rupee_rounded),
          _detailCard('Payment', appt.paymentStatus.toUpperCase(),
              Icons.payments_rounded,
              color: appt.paymentStatus == 'paid'
                  ? AppColors.success
                  : AppColors.danger),
          if (doctor?.clinicAddress != null)
            _detailCard('Clinic Address', doctor!.clinicAddress!,
                Icons.location_on_rounded),
          const SizedBox(height: 20),
          if (appt.status != 'cancelled' && appt.status != 'completed') ...[
            if (appt.paymentStatus != 'paid')
              PrimaryButton(
                label: 'Pay Now',
                icon: Icons.payments_rounded,
                onPressed: () => Navigator.pushNamed(context, AppRoutes.payment,
                    arguments: {
                      'title': 'Consultation Fee',
                      'amount': appt.fee ?? 0
                    }),
              ),
            const SizedBox(height: 10),
            OutlinedButton.icon(
              onPressed: () {},
              icon: const Icon(Icons.chat_outlined),
              label: const Text('Message Doctor'),
              style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
            ),
          ],
          if (appt.status == 'completed')
            PrimaryButton(
              label: 'View Prescription',
              icon: Icons.medication_rounded,
              onPressed: () =>
                  Navigator.pushNamed(context, AppRoutes.prescriptions),
            ),
        ],
      ),
    );
  }

  Widget _info(IconData icon, String label, String value) {
    return Expanded(
      child: Column(
        children: [
          Icon(icon, color: Colors.white, size: 22),
          const SizedBox(height: 4),
          Text(label,
              style:
                  TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 11)),
          const SizedBox(height: 2),
          Text(value,
              style: const TextStyle(
                  color: Colors.white,
                  fontSize: 13,
                  fontWeight: FontWeight.w700)),
        ],
      ),
    );
  }

  Widget _detailCard(String label, String value, IconData icon, {Color? color}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: (color ?? AppColors.primary).withOpacity(0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color ?? AppColors.primary, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label,
                    style: const TextStyle(
                        color: AppColors.textMuted, fontSize: 11.5)),
                const SizedBox(height: 2),
                Text(value,
                    style: TextStyle(
                        color: color ?? AppColors.textPrimary,
                        fontSize: 14,
                        fontWeight: FontWeight.w600)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
