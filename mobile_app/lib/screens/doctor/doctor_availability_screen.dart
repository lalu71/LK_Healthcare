import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';

class DoctorAvailabilityScreen extends StatefulWidget {
  const DoctorAvailabilityScreen({super.key});

  @override
  State<DoctorAvailabilityScreen> createState() => _DoctorAvailabilityScreenState();
}

class _DoctorAvailabilityScreenState extends State<DoctorAvailabilityScreen> {
  final Set<String> _days = {'Mon', 'Tue', 'Wed', 'Thu', 'Fri'};
  TimeOfDay _start = const TimeOfDay(hour: 9, minute: 0);
  TimeOfDay _end = const TimeOfDay(hour: 18, minute: 0);
  int _slotMinutes = 30;
  bool _saving = false;

  final List<_Slot> _customSlots = [];

  Future<void> _pickTime({required bool start}) async {
    final picked = await showTimePicker(
      context: context,
      initialTime: start ? _start : _end,
    );
    if (picked != null) {
      setState(() {
        if (start) {
          _start = picked;
        } else {
          _end = picked;
        }
      });
    }
  }

  String _fmt(TimeOfDay t) {
    final h = t.hourOfPeriod == 0 ? 12 : t.hourOfPeriod;
    final m = t.minute.toString().padLeft(2, '0');
    final p = t.period == DayPeriod.am ? 'AM' : 'PM';
    return '$h:$m $p';
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    // === Backend integration: POST /api/v1/doctor/availability ===
    await Future.delayed(const Duration(milliseconds: 700));
    setState(() => _saving = false);
    if (!mounted) return;
    AppHelpers.snack(context, 'Availability saved');
  }

  @override
  Widget build(BuildContext context) {
    return RoleScaffold(
      title: 'Availability',
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          _sectionTitle('Working Days'),
          const SizedBox(height: 10),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map((d) {
              final selected = _days.contains(d);
              return GestureDetector(
                onTap: () => setState(() {
                  if (selected) {
                    _days.remove(d);
                  } else {
                    _days.add(d);
                  }
                }),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  width: 50,
                  height: 50,
                  decoration: BoxDecoration(
                    color: selected ? AppColors.primary : AppColors.surface,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(
                        color: selected ? AppColors.primary : AppColors.border),
                  ),
                  child: Center(
                    child: Text(d,
                        style: TextStyle(
                            color: selected ? Colors.white : AppColors.textPrimary,
                            fontWeight: FontWeight.w700)),
                  ),
                ),
              );
            }).toList(),
          ),
          const SizedBox(height: 22),
          _sectionTitle('Hours'),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _timeCard('From', _fmt(_start), () => _pickTime(start: true)),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _timeCard('To', _fmt(_end), () => _pickTime(start: false)),
              ),
            ],
          ),
          const SizedBox(height: 22),
          _sectionTitle('Slot Duration'),
          const SizedBox(height: 10),
          Row(
            children: [15, 30, 45, 60].map((m) {
              final selected = _slotMinutes == m;
              return Expanded(
                child: GestureDetector(
                  onTap: () => setState(() => _slotMinutes = m),
                  child: Container(
                    margin: EdgeInsets.only(right: m == 60 ? 0 : 8),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    decoration: BoxDecoration(
                      color: selected
                          ? AppColors.accent.withOpacity(0.12)
                          : AppColors.surface,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                          color: selected ? AppColors.accent : AppColors.border),
                    ),
                    child: Center(
                      child: Text('$m min',
                          style: TextStyle(
                              fontWeight: FontWeight.w700,
                              color: selected ? AppColors.accent : AppColors.textPrimary,
                              fontSize: 13)),
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
          const SizedBox(height: 22),
          _sectionTitle('Specific Date Overrides'),
          const SizedBox(height: 8),
          const Text(
            'Add unavailable dates (vacations, conferences, etc.)',
            style: TextStyle(color: AppColors.textMuted, fontSize: 12.5),
          ),
          const SizedBox(height: 10),
          ..._customSlots.map((s) => Container(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.event_busy_rounded,
                        color: AppColors.danger, size: 18),
                    const SizedBox(width: 8),
                    Expanded(child: Text(s.label)),
                    IconButton(
                      onPressed: () =>
                          setState(() => _customSlots.remove(s)),
                      icon: const Icon(Icons.close_rounded,
                          color: AppColors.textMuted, size: 18),
                    ),
                  ],
                ),
              )),
          OutlinedButton.icon(
            onPressed: () async {
              final d = await showDatePicker(
                context: context,
                initialDate: DateTime.now(),
                firstDate: DateTime.now(),
                lastDate: DateTime.now().add(const Duration(days: 365)),
              );
              if (d != null) {
                setState(() => _customSlots.add(
                    _Slot(label: AppHelpers.formatDate(d), date: d)));
              }
            },
            icon: const Icon(Icons.add_rounded),
            label: const Text('Add Unavailable Date'),
            style: OutlinedButton.styleFrom(
                minimumSize: const Size.fromHeight(48)),
          ),
          const SizedBox(height: 28),
          PrimaryButton(
            label: 'Save Availability',
            loading: _saving,
            icon: Icons.check_rounded,
            onPressed: _save,
          ),
        ],
      ),
    );
  }

  Widget _sectionTitle(String t) => Text(t,
      style: const TextStyle(
          fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPrimary));

  Widget _timeCard(String label, String time, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label,
                style: const TextStyle(
                    color: AppColors.textMuted, fontSize: 11.5)),
            const SizedBox(height: 6),
            Row(
              children: [
                const Icon(Icons.access_time_rounded,
                    color: AppColors.primary, size: 20),
                const SizedBox(width: 6),
                Text(time,
                    style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary)),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _Slot {
  final String label;
  final DateTime date;
  _Slot({required this.label, required this.date});
}
