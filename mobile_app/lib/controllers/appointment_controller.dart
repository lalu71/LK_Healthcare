import 'package:flutter/foundation.dart';

import '../models/appointment.dart';
import '../models/doctor.dart';

class AppointmentController extends ChangeNotifier {
  final List<Appointment> _appointments = [];
  bool _loading = false;
  String? _error;

  List<Appointment> get appointments => _appointments;
  bool get loading => _loading;
  String? get error => _error;

  List<Appointment> get upcoming => _appointments
      .where((a) =>
          a.appointmentDate.isAfter(DateTime.now()) &&
          a.status != 'cancelled' &&
          a.status != 'completed')
      .toList();

  List<Appointment> get history => _appointments
      .where((a) =>
          a.appointmentDate.isBefore(DateTime.now()) ||
          a.status == 'cancelled' ||
          a.status == 'completed')
      .toList();

  Future<void> fetchAppointments() async {
    _loading = true;
    notifyListeners();
    try {
      // === Backend integration point: GET /api/v1/my/appointments ===
      await Future.delayed(const Duration(milliseconds: 500));
      if (_appointments.isEmpty) {
        _appointments.addAll(_mockData());
      }
    } catch (e) {
      _error = e.toString();
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<bool> bookAppointment({
    required int doctorId,
    required Doctor doctor,
    required DateTime date,
    required String slot,
    String? reason,
  }) async {
    try {
      // === Backend integration point: POST /api/v1/appointments ===
      await Future.delayed(const Duration(milliseconds: 600));
      _appointments.add(Appointment(
        id: DateTime.now().millisecondsSinceEpoch,
        doctorId: doctorId,
        patientId: 1,
        appointmentDate: date,
        slot: slot,
        status: 'pending',
        reason: reason,
        fee: doctor.fee,
        doctor: doctor,
      ));
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      return false;
    }
  }

  Future<bool> cancelAppointment(int id) async {
    // === Backend integration point ===
    final idx = _appointments.indexWhere((a) => a.id == id);
    if (idx == -1) return false;
    final old = _appointments[idx];
    _appointments[idx] = Appointment(
      id: old.id,
      doctorId: old.doctorId,
      patientId: old.patientId,
      appointmentDate: old.appointmentDate,
      slot: old.slot,
      status: 'cancelled',
      reason: old.reason,
      paymentStatus: old.paymentStatus,
      fee: old.fee,
      doctor: old.doctor,
      patient: old.patient,
    );
    notifyListeners();
    return true;
  }

  List<Appointment> _mockData() => [
        Appointment(
          id: 101,
          doctorId: 1,
          patientId: 1,
          appointmentDate: DateTime.now().add(const Duration(days: 2)),
          slot: '10:30 AM',
          status: 'confirmed',
          reason: 'Routine heart checkup',
          paymentStatus: 'paid',
          fee: 600,
          doctor: Doctor(
            id: 1,
            name: 'Dr. Aarav Mehta',
            specialization: 'Cardiology',
            fee: 600,
            rating: 4.8,
          ),
        ),
        Appointment(
          id: 102,
          doctorId: 2,
          patientId: 1,
          appointmentDate: DateTime.now().add(const Duration(days: 5)),
          slot: '03:00 PM',
          status: 'pending',
          reason: 'Skin rash follow-up',
          paymentStatus: 'unpaid',
          fee: 500,
          doctor: Doctor(
            id: 2,
            name: 'Dr. Priya Sharma',
            specialization: 'Dermatology',
            fee: 500,
            rating: 4.7,
          ),
        ),
        Appointment(
          id: 103,
          doctorId: 4,
          patientId: 1,
          appointmentDate: DateTime.now().subtract(const Duration(days: 7)),
          slot: '09:00 AM',
          status: 'completed',
          reason: 'Child vaccination',
          paymentStatus: 'paid',
          fee: 450,
          doctor: Doctor(
            id: 4,
            name: 'Dr. Sneha Kapoor',
            specialization: 'Pediatrics',
            fee: 450,
            rating: 4.8,
          ),
        ),
      ];
}
