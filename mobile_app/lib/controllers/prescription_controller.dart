import 'package:flutter/foundation.dart';

import '../models/prescription.dart';
import '../models/doctor.dart';
import '../models/patient.dart';

class PrescriptionController extends ChangeNotifier {
  final List<Prescription> _prescriptions = [];
  bool _loading = false;

  List<Prescription> get prescriptions => _prescriptions;
  bool get loading => _loading;

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration: GET /api/v1/my/prescriptions ===
    await Future.delayed(const Duration(milliseconds: 500));
    if (_prescriptions.isEmpty) _prescriptions.addAll(_mock());
    _loading = false;
    notifyListeners();
  }

  Future<bool> create({
    required int patientId,
    required int? appointmentId,
    required String diagnosis,
    required String advice,
    required List<PrescriptionItem> items,
    String? followUpDate,
  }) async {
    // === Backend integration: POST /api/v1/doctor/prescriptions ===
    await Future.delayed(const Duration(milliseconds: 600));
    _prescriptions.insert(
      0,
      Prescription(
        id: DateTime.now().millisecondsSinceEpoch,
        doctorId: 1,
        patientId: patientId,
        appointmentId: appointmentId,
        diagnosis: diagnosis,
        advice: advice,
        followUpDate: followUpDate,
        createdAt: DateTime.now(),
        items: items,
      ),
    );
    notifyListeners();
    return true;
  }

  List<Prescription> _mock() => [
        Prescription(
          id: 201,
          doctorId: 1,
          patientId: 1,
          diagnosis: 'Hypertension (Stage 1)',
          advice: 'Reduce salt intake, regular exercise, follow-up in 2 weeks.',
          followUpDate: DateTime.now().add(const Duration(days: 14)).toIso8601String(),
          createdAt: DateTime.now().subtract(const Duration(days: 3)),
          status: 'issued',
          paymentStatus: 'unpaid',
          doctor: Doctor(id: 1, name: 'Dr. Aarav Mehta', specialization: 'Cardiology'),
          patient: Patient(id: 1, userId: 1, name: 'Demo Patient'),
          items: [
            PrescriptionItem(
              id: 1,
              medicineName: 'Amlodipine 5mg',
              dosage: '1 tablet',
              frequency: 'Once daily',
              duration: '30 days',
              instructions: 'Take in the morning after breakfast',
            ),
            PrescriptionItem(
              id: 2,
              medicineName: 'Atorvastatin 10mg',
              dosage: '1 tablet',
              frequency: 'Once at night',
              duration: '30 days',
              instructions: 'After dinner',
            ),
          ],
        ),
        Prescription(
          id: 202,
          doctorId: 4,
          patientId: 1,
          diagnosis: 'Common cold and mild fever',
          advice: 'Rest, plenty of fluids, paracetamol if fever > 100°F.',
          createdAt: DateTime.now().subtract(const Duration(days: 14)),
          status: 'completed',
          paymentStatus: 'paid',
          doctor: Doctor(id: 4, name: 'Dr. Sneha Kapoor', specialization: 'Pediatrics'),
          patient: Patient(id: 1, userId: 1, name: 'Demo Patient'),
          items: [
            PrescriptionItem(
              id: 3,
              medicineName: 'Paracetamol 500mg',
              dosage: '1 tablet',
              frequency: 'Every 6 hours',
              duration: '3 days',
              instructions: 'Only if fever',
            ),
            PrescriptionItem(
              id: 4,
              medicineName: 'Cetirizine 10mg',
              dosage: '1 tablet',
              frequency: 'At bedtime',
              duration: '5 days',
            ),
          ],
        ),
      ];
}
