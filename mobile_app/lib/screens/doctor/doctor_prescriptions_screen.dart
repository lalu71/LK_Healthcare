import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/prescription_controller.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/status_badge.dart';

class DoctorPrescriptionsScreen extends StatefulWidget {
  const DoctorPrescriptionsScreen({super.key});

  @override
  State<DoctorPrescriptionsScreen> createState() => _DoctorPrescriptionsScreenState();
}

class _DoctorPrescriptionsScreenState extends State<DoctorPrescriptionsScreen> {
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
      title: 'Prescriptions',
      body: ctrl.loading
          ? const LoadingView()
          : ctrl.prescriptions.isEmpty
              ? EmptyState(
                  icon: Icons.medication_outlined,
                  title: 'No prescriptions',
                  message: 'Create your first prescription',
                  actionLabel: 'Create Prescription',
                  onAction: () => Navigator.pushNamed(
                      context, AppRoutes.createPrescription),
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: ctrl.prescriptions.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (_, i) {
                    final p = ctrl.prescriptions[i];
                    return Material(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(16),
                      child: InkWell(
                        borderRadius: BorderRadius.circular(16),
                        onTap: () => Navigator.pushNamed(
                            context, AppRoutes.prescriptionDetail,
                            arguments: p),
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
                                      color: AppColors.primary.withOpacity(0.12),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: const Icon(Icons.person_rounded,
                                        color: AppColors.primary),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(p.patient?.name ?? 'Patient',
                                            style: const TextStyle(
                                                fontSize: 14.5,
                                                fontWeight: FontWeight.w700)),
                                        Text(p.diagnosis ?? '',
                                            style: const TextStyle(
                                                fontSize: 12,
                                                color: AppColors.textSecondary),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis),
                                      ],
                                    ),
                                  ),
                                  StatusBadge(status: p.status),
                                ],
                              ),
                              const SizedBox(height: 10),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text('${p.items.length} medicines',
                                      style: const TextStyle(
                                          fontSize: 12,
                                          color: AppColors.textSecondary,
                                          fontWeight: FontWeight.w600)),
                                  Text(AppHelpers.formatDate(p.createdAt),
                                      style: const TextStyle(
                                          fontSize: 12,
                                          color: AppColors.textMuted)),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () =>
            Navigator.pushNamed(context, AppRoutes.createPrescription),
        icon: const Icon(Icons.add_rounded),
        label: const Text('New Rx'),
      ),
    );
  }
}
