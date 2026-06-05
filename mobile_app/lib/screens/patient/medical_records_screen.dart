import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/role_scaffold.dart';

class MedicalRecordsScreen extends StatefulWidget {
  const MedicalRecordsScreen({super.key});

  @override
  State<MedicalRecordsScreen> createState() => _MedicalRecordsScreenState();
}

class _MedicalRecordsScreenState extends State<MedicalRecordsScreen> {
  final List<_Record> _records = [
    _Record(
      title: 'Lipid Profile Report',
      type: 'Lab Report',
      date: DateTime.now().subtract(const Duration(days: 5)),
      size: '420 KB',
      icon: Icons.science_rounded,
      color: AppColors.warning,
    ),
    _Record(
      title: 'Cardiology Prescription',
      type: 'Prescription',
      date: DateTime.now().subtract(const Duration(days: 12)),
      size: '180 KB',
      icon: Icons.medication_rounded,
      color: AppColors.accent,
    ),
    _Record(
      title: 'X-Ray Chest PA View',
      type: 'X-Ray',
      date: DateTime.now().subtract(const Duration(days: 30)),
      size: '1.2 MB',
      icon: Icons.image_rounded,
      color: AppColors.primary,
    ),
  ];

  Future<void> _addRecord() async {
    // === Backend integration: POST /api/v1/medical-records with file ===
    AppHelpers.snack(context, 'Picker opens for upload');
  }

  @override
  Widget build(BuildContext context) {
    return RoleScaffold(
      title: 'Medical Records',
      body: _records.isEmpty
          ? EmptyState(
              icon: Icons.folder_open_rounded,
              title: 'No records yet',
              message: 'Upload your medical reports, prescriptions, and history',
              actionLabel: 'Upload Record',
              onAction: _addRecord,
            )
          : ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: _records.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (_, i) => _RecordTile(record: _records[i]),
            ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _addRecord,
        icon: const Icon(Icons.upload_file_rounded),
        label: const Text('Upload'),
      ),
    );
  }
}

class _Record {
  final String title;
  final String type;
  final DateTime date;
  final String size;
  final IconData icon;
  final Color color;

  _Record({
    required this.title,
    required this.type,
    required this.date,
    required this.size,
    required this.icon,
    required this.color,
  });
}

class _RecordTile extends StatelessWidget {
  final _Record record;
  const _RecordTile({required this.record});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: () => AppHelpers.snack(context, 'Opening ${record.title}'),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: record.color.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(record.icon, color: record.color, size: 24),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(record.title,
                        style: const TextStyle(
                            fontSize: 14.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: AppColors.surfaceAlt,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(record.type,
                              style: const TextStyle(
                                  fontSize: 10.5,
                                  color: AppColors.textSecondary,
                                  fontWeight: FontWeight.w600)),
                        ),
                        const SizedBox(width: 8),
                        Text(AppHelpers.formatDate(record.date),
                            style: const TextStyle(
                                fontSize: 11.5,
                                color: AppColors.textMuted)),
                        const Spacer(),
                        Text(record.size,
                            style: const TextStyle(
                                fontSize: 11, color: AppColors.textMuted)),
                      ],
                    ),
                  ],
                ),
              ),
              IconButton(
                onPressed: () =>
                    AppHelpers.snack(context, 'Downloading ${record.title}'),
                icon: const Icon(Icons.download_rounded,
                    color: AppColors.primary),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
