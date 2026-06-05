import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/prescription_controller.dart';
import '../../models/prescription.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/status_badge.dart';

class PrescriptionsScreen extends StatefulWidget {
  const PrescriptionsScreen({super.key});

  @override
  State<PrescriptionsScreen> createState() => _PrescriptionsScreenState();
}

class _PrescriptionsScreenState extends State<PrescriptionsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PrescriptionController>().fetch();
    });
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<PrescriptionController>();
    return RoleScaffold(
      title: 'My Prescriptions',
      body: ctrl.loading
          ? const LoadingView()
          : ctrl.prescriptions.isEmpty
              ? const EmptyState(
                  icon: Icons.medication_outlined,
                  title: 'No prescriptions yet',
                  message: 'Your doctor-issued prescriptions appear here',
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: ctrl.prescriptions.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (_, i) => _PrescriptionTile(
                    prescription: ctrl.prescriptions[i],
                    onTap: () => Navigator.pushNamed(
                        context, AppRoutes.prescriptionDetail,
                        arguments: ctrl.prescriptions[i]),
                  ),
                ),
    );
  }
}

class _PrescriptionTile extends StatelessWidget {
  final Prescription prescription;
  final VoidCallback? onTap;

  const _PrescriptionTile({required this.prescription, this.onTap});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: AppColors.accent.withOpacity(0.12),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.medication_rounded,
                        color: AppColors.accent),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(prescription.doctor?.name ?? 'Doctor',
                            style: const TextStyle(
                                fontSize: 14.5,
                                fontWeight: FontWeight.w700,
                                color: AppColors.textPrimary)),
                        Text(prescription.doctor?.specialization ?? '',
                            style: const TextStyle(
                                fontSize: 12,
                                color: AppColors.textSecondary)),
                      ],
                    ),
                  ),
                  StatusBadge(status: prescription.status),
                ],
              ),
              const SizedBox(height: 12),
              if (prescription.diagnosis != null)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceAlt,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(prescription.diagnosis!,
                      style: const TextStyle(
                          fontSize: 12.5, color: AppColors.textPrimary)),
                ),
              const SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${prescription.items.length} medicines',
                    style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                        fontWeight: FontWeight.w600),
                  ),
                  Text(
                    AppHelpers.formatDate(prescription.createdAt),
                    style: const TextStyle(
                        fontSize: 12, color: AppColors.textMuted),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
