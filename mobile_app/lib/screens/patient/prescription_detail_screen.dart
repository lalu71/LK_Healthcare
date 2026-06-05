import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../models/prescription.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/status_badge.dart';

class PrescriptionDetailScreen extends StatefulWidget {
  const PrescriptionDetailScreen({super.key});

  @override
  State<PrescriptionDetailScreen> createState() => _PrescriptionDetailScreenState();
}

class _PrescriptionDetailScreenState extends State<PrescriptionDetailScreen> {
  @override
  Widget build(BuildContext context) {
    final p = ModalRoute.of(context)?.settings.arguments as Prescription?;
    if (p == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Prescription')),
        body: const Center(child: Text('No prescription provided')),
      );
    }
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Prescription'),
        actions: [
          IconButton(
            onPressed: () => AppHelpers.snack(context, 'Downloading PDF...'),
            icon: const Icon(Icons.download_rounded),
          ),
          IconButton(
            onPressed: () => AppHelpers.snack(context, 'Share link copied'),
            icon: const Icon(Icons.share_rounded),
          ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(18),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.local_hospital_rounded,
                        color: Colors.white, size: 28),
                    const SizedBox(width: 8),
                    const Text('LK Healthcare',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.w800)),
                    const Spacer(),
                    StatusBadge(status: p.status),
                  ],
                ),
                const SizedBox(height: 14),
                Text(p.doctor?.name ?? 'Doctor',
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.w700)),
                Text(p.doctor?.specialization ?? '',
                    style: TextStyle(
                        color: Colors.white.withOpacity(0.85),
                        fontSize: 13)),
                const SizedBox(height: 12),
                Row(
                  children: [
                    const Icon(Icons.calendar_today_rounded,
                        size: 14, color: Colors.white70),
                    const SizedBox(width: 6),
                    Text(AppHelpers.formatDate(p.createdAt),
                        style: const TextStyle(
                            color: Colors.white70, fontSize: 12.5)),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          if (p.diagnosis != null) ...[
            _section('Diagnosis', p.diagnosis!, Icons.diagnosis_rounded),
            const SizedBox(height: 10),
          ],
          if (p.items.isNotEmpty) ...[
            const _SectionTitle(title: 'Prescribed Medicines'),
            const SizedBox(height: 10),
            ...p.items.map((i) => _MedicineTile(item: i)),
            const SizedBox(height: 14),
          ],
          if (p.advice != null && p.advice!.isNotEmpty)
            _section('Advice', p.advice!, Icons.tips_and_updates_rounded),
          if (p.followUpDate != null && p.followUpDate!.isNotEmpty) ...[
            const SizedBox(height: 10),
            _section('Follow-up', p.followUpDate!, Icons.event_rounded),
          ],
          const SizedBox(height: 24),
          PrimaryButton(
            label: 'Order Medicines',
            icon: Icons.local_pharmacy_rounded,
            onPressed: () => Navigator.pushNamed(context, AppRoutes.pharmacy),
          ),
        ],
      ),
    );
  }

  Widget _section(String title, String body, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 18, color: AppColors.primary),
              const SizedBox(width: 8),
              Text(title,
                  style: const TextStyle(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary)),
            ],
          ),
          const SizedBox(height: 8),
          Text(body,
              style: const TextStyle(
                  fontSize: 13.5,
                  color: AppColors.textSecondary,
                  height: 1.55)),
        ],
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String title;
  const _SectionTitle({required this.title});

  @override
  Widget build(BuildContext context) {
    return Text(title,
        style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary));
  }
}

class _MedicineTile extends StatelessWidget {
  final PrescriptionItem item;
  const _MedicineTile({required this.item});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.accent.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.medication_rounded,
                    color: AppColors.accent, size: 18),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(item.medicineName,
                    style: const TextStyle(
                        fontWeight: FontWeight.w700,
                        fontSize: 14.5,
                        color: AppColors.textPrimary)),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Wrap(
            spacing: 6,
            runSpacing: 6,
            children: [
              if (item.dosage != null) _chip(Icons.medication_liquid_rounded, item.dosage!),
              if (item.frequency != null) _chip(Icons.repeat_rounded, item.frequency!),
              if (item.duration != null) _chip(Icons.schedule_rounded, item.duration!),
            ],
          ),
          if (item.instructions != null && item.instructions!.isNotEmpty) ...[
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              decoration: BoxDecoration(
                color: AppColors.surfaceAlt,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  const Icon(Icons.info_outline_rounded,
                      size: 14, color: AppColors.textMuted),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(item.instructions!,
                        style: const TextStyle(
                            fontSize: 12, color: AppColors.textSecondary)),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _chip(IconData icon, String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: AppColors.surfaceAlt,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: AppColors.textSecondary),
          const SizedBox(width: 4),
          Text(text,
              style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textPrimary,
                  fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
