import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../controllers/prescription_controller.dart';
import '../../models/prescription.dart';
import '../../utils/helpers.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';

class CreatePrescriptionScreen extends StatefulWidget {
  const CreatePrescriptionScreen({super.key});

  @override
  State<CreatePrescriptionScreen> createState() => _CreatePrescriptionScreenState();
}

class _CreatePrescriptionScreenState extends State<CreatePrescriptionScreen> {
  final _formKey = GlobalKey<FormState>();
  final _patientName = TextEditingController();
  final _diagnosis = TextEditingController();
  final _advice = TextEditingController();
  DateTime? _followUpDate;
  bool _saving = false;

  final List<_DraftItem> _items = [_DraftItem()];

  @override
  void dispose() {
    _patientName.dispose();
    _diagnosis.dispose();
    _advice.dispose();
    for (final it in _items) {
      it.dispose();
    }
    super.dispose();
  }

  Future<void> _pickFollowUp() async {
    final d = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 14)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (d != null) setState(() => _followUpDate = d);
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    if (_items.where((i) => i.name.text.isNotEmpty).isEmpty) {
      AppHelpers.snack(context, 'Add at least one medicine', isError: true);
      return;
    }
    setState(() => _saving = true);
    final items = _items
        .where((i) => i.name.text.isNotEmpty)
        .map((i) => PrescriptionItem(
              id: 0,
              medicineName: i.name.text,
              dosage: i.dosage.text,
              frequency: i.frequency.text,
              duration: i.duration.text,
              instructions: i.instructions.text,
            ))
        .toList();

    final ok = await context.read<PrescriptionController>().create(
          patientId: 1,
          appointmentId: null,
          diagnosis: _diagnosis.text.trim(),
          advice: _advice.text.trim(),
          items: items,
          followUpDate: _followUpDate?.toIso8601String(),
        );
    setState(() => _saving = false);
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, 'Prescription created');
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Create Prescription')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _section('Patient Information'),
              CustomTextField(
                label: 'Patient Name',
                hint: 'Search or enter patient name',
                controller: _patientName,
                prefixIcon: Icons.person_outline,
                validator: (v) => v == null || v.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 14),
              CustomTextField(
                label: 'Diagnosis',
                hint: 'e.g., Hypertension, Diabetes',
                controller: _diagnosis,
                prefixIcon: Icons.diagnosis_outlined,
                validator: (v) => v == null || v.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 22),
              Row(
                children: [
                  _section('Medicines'),
                  const Spacer(),
                  TextButton.icon(
                    onPressed: () => setState(() => _items.add(_DraftItem())),
                    icon: const Icon(Icons.add_rounded, size: 18),
                    label: const Text('Add Medicine'),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              ..._items.asMap().entries.map((entry) {
                final i = entry.key;
                final it = entry.value;
                return Container(
                  margin: const EdgeInsets.only(bottom: 14),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 28,
                            height: 28,
                            decoration: BoxDecoration(
                              color: AppColors.accent.withOpacity(0.12),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Center(
                              child: Text('${i + 1}',
                                  style: const TextStyle(
                                      color: AppColors.accent,
                                      fontWeight: FontWeight.w700,
                                      fontSize: 13)),
                            ),
                          ),
                          const SizedBox(width: 8),
                          const Text('Medicine',
                              style: TextStyle(
                                  fontWeight: FontWeight.w700, fontSize: 13)),
                          const Spacer(),
                          if (_items.length > 1)
                            IconButton(
                              onPressed: () =>
                                  setState(() => _items.removeAt(i)),
                              icon: const Icon(Icons.delete_outline_rounded,
                                  color: AppColors.danger, size: 20),
                            ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      CustomTextField(
                          label: 'Name',
                          hint: 'Paracetamol 500mg',
                          controller: it.name),
                      const SizedBox(height: 10),
                      Row(
                        children: [
                          Expanded(
                              child: CustomTextField(
                                  label: 'Dosage',
                                  hint: '1 tab',
                                  controller: it.dosage)),
                          const SizedBox(width: 8),
                          Expanded(
                              child: CustomTextField(
                                  label: 'Frequency',
                                  hint: '1-0-1',
                                  controller: it.frequency)),
                        ],
                      ),
                      const SizedBox(height: 10),
                      CustomTextField(
                          label: 'Duration',
                          hint: '5 days',
                          controller: it.duration),
                      const SizedBox(height: 10),
                      CustomTextField(
                          label: 'Instructions (Optional)',
                          hint: 'After food',
                          controller: it.instructions),
                    ],
                  ),
                );
              }),
              const SizedBox(height: 12),
              _section('Advice & Follow-up'),
              CustomTextField(
                label: 'Doctor\'s Advice',
                hint: 'Rest, diet, exercise notes...',
                controller: _advice,
                maxLines: 3,
                prefixIcon: Icons.tips_and_updates_outlined,
              ),
              const SizedBox(height: 14),
              InkWell(
                onTap: _pickFollowUp,
                borderRadius: BorderRadius.circular(14),
                child: Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.event_rounded,
                          color: AppColors.primary),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('Follow-up Date',
                                style: TextStyle(
                                    color: AppColors.textMuted,
                                    fontSize: 11.5)),
                            const SizedBox(height: 2),
                            Text(_followUpDate == null
                                ? 'Not set (tap to choose)'
                                : AppHelpers.formatDate(_followUpDate!),
                                style: const TextStyle(
                                    fontWeight: FontWeight.w600)),
                          ],
                        ),
                      ),
                      const Icon(Icons.chevron_right_rounded,
                          color: AppColors.textMuted),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 28),
              PrimaryButton(
                label: 'Issue Prescription',
                icon: Icons.check_rounded,
                loading: _saving,
                onPressed: _save,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _section(String t) => Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: Text(t,
            style: const TextStyle(
                fontSize: 14.5,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary)),
      );
}

class _DraftItem {
  final name = TextEditingController();
  final dosage = TextEditingController();
  final frequency = TextEditingController();
  final duration = TextEditingController();
  final instructions = TextEditingController();

  void dispose() {
    name.dispose();
    dosage.dispose();
    frequency.dispose();
    duration.dispose();
    instructions.dispose();
  }
}
