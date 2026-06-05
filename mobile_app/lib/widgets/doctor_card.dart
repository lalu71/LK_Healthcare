import 'package:flutter/material.dart';

import '../config/app_colors.dart';
import '../models/doctor.dart';
import '../utils/helpers.dart';

class DoctorCard extends StatelessWidget {
  final Doctor doctor;
  final VoidCallback? onTap;
  final bool showBookButton;
  final VoidCallback? onBook;

  const DoctorCard({
    super.key,
    required this.doctor,
    this.onTap,
    this.showBookButton = true,
    this.onBook,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        borderRadius: BorderRadius.circular(18),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 64,
                height: 64,
                decoration: BoxDecoration(
                  gradient: AppColors.primaryGradient,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Center(
                  child: Text(
                    doctor.name.replaceAll('Dr. ', '').isNotEmpty
                        ? doctor.name.replaceAll('Dr. ', '')[0]
                        : 'D',
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w700,
                      fontSize: 22,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(doctor.name,
                        style: const TextStyle(
                            fontWeight: FontWeight.w700,
                            fontSize: 15,
                            color: AppColors.textPrimary)),
                    const SizedBox(height: 2),
                    Text(doctor.specialization ?? 'General',
                        style: const TextStyle(
                            color: AppColors.primary,
                            fontSize: 12.5,
                            fontWeight: FontWeight.w600)),
                    if (doctor.qualification != null) ...[
                      const SizedBox(height: 4),
                      Text(doctor.qualification!,
                          style: const TextStyle(
                              color: AppColors.textMuted, fontSize: 11.5)),
                    ],
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        if (doctor.rating != null) ...[
                          const Icon(Icons.star_rounded,
                              size: 14, color: AppColors.warning),
                          const SizedBox(width: 2),
                          Text(doctor.rating!.toStringAsFixed(1),
                              style: const TextStyle(
                                  fontSize: 12, fontWeight: FontWeight.w600)),
                          const SizedBox(width: 10),
                        ],
                        if (doctor.experienceYears != null) ...[
                          const Icon(Icons.work_outline_rounded,
                              size: 13, color: AppColors.textMuted),
                          const SizedBox(width: 2),
                          Text('${doctor.experienceYears}y',
                              style: const TextStyle(
                                  fontSize: 12, color: AppColors.textSecondary)),
                          const SizedBox(width: 10),
                        ],
                        if (doctor.fee != null)
                          Text(AppHelpers.currency(doctor.fee!),
                              style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: AppColors.accent)),
                      ],
                    ),
                  ],
                ),
              ),
              if (showBookButton)
                Padding(
                  padding: const EdgeInsets.only(left: 8),
                  child: InkWell(
                    onTap: onBook ?? onTap,
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding:
                          const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        gradient: AppColors.primaryGradient,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Text('Book',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.w700)),
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
