import 'package:flutter/material.dart';

import '../config/app_colors.dart';
import '../models/appointment.dart';
import '../utils/helpers.dart';
import 'status_badge.dart';

class AppointmentCard extends StatelessWidget {
  final Appointment appointment;
  final VoidCallback? onTap;
  final VoidCallback? onCancel;

  const AppointmentCard({
    super.key,
    required this.appointment,
    this.onTap,
    this.onCancel,
  });

  @override
  Widget build(BuildContext context) {
    final doctor = appointment.doctor;
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      gradient: AppColors.primaryGradient,
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Center(
                      child: Text(
                        (doctor?.name ?? 'D').replaceAll('Dr. ', '').isNotEmpty
                            ? (doctor!.name).replaceAll('Dr. ', '')[0]
                            : 'D',
                        style: const TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                            fontWeight: FontWeight.w700),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          doctor?.name ?? 'Doctor',
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 2),
                        Text(
                          doctor?.specialization ?? 'General',
                          style: const TextStyle(
                            fontSize: 12.5,
                            color: AppColors.primary,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  StatusBadge(status: appointment.status),
                ],
              ),
              const SizedBox(height: 14),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  color: AppColors.surfaceAlt,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    _info(
                      Icons.calendar_today_rounded,
                      AppHelpers.formatDate(appointment.appointmentDate),
                    ),
                    Container(width: 1, height: 22, color: AppColors.border),
                    _info(Icons.access_time_rounded, appointment.slot ?? '—'),
                    if (appointment.fee != null) ...[
                      Container(width: 1, height: 22, color: AppColors.border),
                      _info(Icons.currency_rupee_rounded,
                          AppHelpers.currency(appointment.fee!).replaceAll('₹', '')),
                    ],
                  ],
                ),
              ),
              if (appointment.reason != null && appointment.reason!.isNotEmpty) ...[
                const SizedBox(height: 10),
                Text(
                  appointment.reason!,
                  style: const TextStyle(
                      color: AppColors.textSecondary, fontSize: 12.5),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
              if (onCancel != null &&
                  appointment.status != 'cancelled' &&
                  appointment.status != 'completed') ...[
                const SizedBox(height: 10),
                Align(
                  alignment: Alignment.centerRight,
                  child: TextButton.icon(
                    onPressed: onCancel,
                    icon: const Icon(Icons.close_rounded,
                        size: 16, color: AppColors.danger),
                    label: const Text('Cancel',
                        style: TextStyle(color: AppColors.danger)),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _info(IconData icon, String text) {
    return Expanded(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, size: 13, color: AppColors.textSecondary),
          const SizedBox(width: 4),
          Flexible(
            child: Text(
              text,
              style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textPrimary,
                  fontWeight: FontWeight.w600),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
