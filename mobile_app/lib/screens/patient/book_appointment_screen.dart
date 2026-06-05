import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/appointment_controller.dart';
import '../../controllers/doctor_controller.dart';
import '../../models/appointment.dart';
import '../../models/doctor.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';

class BookAppointmentScreen extends StatefulWidget {
  const BookAppointmentScreen({super.key});

  @override
  State<BookAppointmentScreen> createState() => _BookAppointmentScreenState();
}

class _BookAppointmentScreenState extends State<BookAppointmentScreen> {
  Doctor? _doctor;
  DateTime _selectedDate = DateTime.now().add(const Duration(days: 1));
  TimeSlot? _selectedSlot;
  List<TimeSlot> _slots = [];
  bool _loadingSlots = false;
  bool _booking = false;
  final _reasonCtrl = TextEditingController();

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final args = ModalRoute.of(context)?.settings.arguments;
    if (args is Doctor && _doctor == null) {
      _doctor = args;
      _loadSlots();
    }
  }

  Future<void> _loadSlots() async {
    if (_doctor == null) return;
    setState(() {
      _loadingSlots = true;
      _selectedSlot = null;
    });
    _slots =
        await context.read<DoctorController>().fetchSlots(_doctor!.id, _selectedDate);
    setState(() => _loadingSlots = false);
  }

  Future<void> _pickDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 60)),
    );
    if (date != null) {
      setState(() => _selectedDate = date);
      _loadSlots();
    }
  }

  Future<void> _book() async {
    if (_doctor == null || _selectedSlot == null) {
      AppHelpers.snack(context, 'Please select a time slot', isError: true);
      return;
    }
    setState(() => _booking = true);
    final ok = await context.read<AppointmentController>().bookAppointment(
          doctorId: _doctor!.id,
          doctor: _doctor!,
          date: _selectedDate,
          slot: _selectedSlot!.time,
          reason: _reasonCtrl.text.trim().isEmpty ? null : _reasonCtrl.text.trim(),
        );
    setState(() => _booking = false);
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, 'Appointment booked!');
      Navigator.pushReplacementNamed(context, AppRoutes.payment, arguments: {
        'title': 'Consultation Fee',
        'amount': _doctor!.fee ?? 0,
      });
    }
  }

  @override
  void dispose() {
    _reasonCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_doctor == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Book Appointment')),
        body: const Center(child: Text('No doctor selected')),
      );
    }
    final doctor = _doctor!;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Book Appointment')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _doctorHeader(doctor),
          const SizedBox(height: 18),
          _sectionTitle('Select Date'),
          const SizedBox(height: 10),
          _datePicker(),
          const SizedBox(height: 18),
          _sectionTitle('Available Slots'),
          const SizedBox(height: 10),
          _slotsGrid(),
          const SizedBox(height: 18),
          _sectionTitle('Reason (Optional)'),
          const SizedBox(height: 10),
          TextField(
            controller: _reasonCtrl,
            maxLines: 3,
            decoration: const InputDecoration(
              hintText: 'Briefly describe your symptoms or reason for visit...',
            ),
          ),
          const SizedBox(height: 22),
          _summary(),
          const SizedBox(height: 16),
          PrimaryButton(
            label: 'Confirm & Pay',
            icon: Icons.arrow_forward_rounded,
            loading: _booking,
            onPressed: _book,
          ),
        ],
      ),
    );
  }

  Widget _doctorHeader(Doctor doctor) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Row(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.22),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Center(
              child: Text(
                doctor.name.replaceAll('Dr. ', '').isNotEmpty
                    ? doctor.name.replaceAll('Dr. ', '')[0]
                    : 'D',
                style: const TextStyle(
                    color: Colors.white,
                    fontSize: 22,
                    fontWeight: FontWeight.w800),
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
                        color: Colors.white,
                        fontSize: 17,
                        fontWeight: FontWeight.w700)),
                Text(doctor.specialization ?? '',
                    style: TextStyle(
                        color: Colors.white.withOpacity(0.85), fontSize: 13)),
                const SizedBox(height: 6),
                Row(
                  children: [
                    if (doctor.fee != null)
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.25),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(AppHelpers.currency(doctor.fee!),
                            style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.w700)),
                      ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _sectionTitle(String title) => Text(title,
      style: const TextStyle(
          fontSize: 15,
          fontWeight: FontWeight.w700,
          color: AppColors.textPrimary));

  Widget _datePicker() {
    return SizedBox(
      height: 84,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: 14,
        itemBuilder: (_, i) {
          final date = DateTime.now().add(Duration(days: i));
          final selected = date.day == _selectedDate.day &&
              date.month == _selectedDate.month;
          return GestureDetector(
            onTap: () {
              setState(() => _selectedDate = date);
              _loadSlots();
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 220),
              width: 64,
              margin: const EdgeInsets.only(right: 10),
              decoration: BoxDecoration(
                color: selected ? AppColors.primary : AppColors.surface,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                    color: selected ? AppColors.primary : AppColors.border),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']
                        [date.weekday % 7],
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: selected ? Colors.white70 : AppColors.textMuted,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${date.day}',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: selected ? Colors.white : AppColors.textPrimary,
                    ),
                  ),
                  Text(
                    [
                      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ][date.month - 1],
                    style: TextStyle(
                      fontSize: 11,
                      color: selected ? Colors.white70 : AppColors.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _slotsGrid() {
    if (_loadingSlots) {
      return const SizedBox(
        height: 100,
        child: Center(child: CircularProgressIndicator()),
      );
    }
    if (_slots.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border),
        ),
        child: const Text('No slots available for this day',
            textAlign: TextAlign.center,
            style: TextStyle(color: AppColors.textSecondary)),
      );
    }
    return Wrap(
      spacing: 10,
      runSpacing: 10,
      children: _slots.map((slot) {
        final selected = _selectedSlot?.time == slot.time;
        final disabled = !slot.isAvailable;
        return GestureDetector(
          onTap: disabled ? null : () => setState(() => _selectedSlot = slot),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 180),
            padding:
                const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              color: disabled
                  ? AppColors.surfaceAlt
                  : selected
                      ? AppColors.primary
                      : AppColors.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: disabled
                    ? AppColors.border
                    : selected
                        ? AppColors.primary
                        : AppColors.border,
              ),
            ),
            child: Text(
              slot.time,
              style: TextStyle(
                color: disabled
                    ? AppColors.textMuted
                    : selected
                        ? Colors.white
                        : AppColors.textPrimary,
                fontWeight: FontWeight.w600,
                fontSize: 13,
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _summary() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          _row('Date', AppHelpers.formatDate(_selectedDate)),
          const SizedBox(height: 6),
          _row('Time', _selectedSlot?.time ?? '—'),
          const SizedBox(height: 6),
          const Divider(height: 16),
          _row('Consultation Fee',
              AppHelpers.currency(_doctor?.fee ?? 0), bold: true),
        ],
      ),
    );
  }

  Widget _row(String label, String value, {bool bold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label,
            style: const TextStyle(color: AppColors.textSecondary, fontSize: 13)),
        Text(value,
            style: TextStyle(
                color: AppColors.textPrimary,
                fontSize: bold ? 15 : 13,
                fontWeight: bold ? FontWeight.w800 : FontWeight.w600)),
      ],
    );
  }
}
