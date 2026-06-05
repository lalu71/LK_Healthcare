import 'package:flutter/foundation.dart';

import '../models/lab.dart';

class LabController extends ChangeNotifier {
  final List<LabTest> _tests = [];
  final List<LabBooking> _bookings = [];
  bool _loading = false;
  String _query = '';

  bool get loading => _loading;
  List<LabBooking> get bookings => _bookings;

  List<LabTest> get tests => _query.isEmpty
      ? _tests
      : _tests
          .where((t) =>
              t.name.toLowerCase().contains(_query.toLowerCase()) ||
              (t.category ?? '').toLowerCase().contains(_query.toLowerCase()))
          .toList();

  void setQuery(String q) {
    _query = q;
    notifyListeners();
  }

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration: GET /api/v1/lab/tests, /api/v1/my/lab-bookings ===
    await Future.delayed(const Duration(milliseconds: 500));
    if (_tests.isEmpty) _tests.addAll(_mockTests());
    if (_bookings.isEmpty) _bookings.addAll(_mockBookings());
    _loading = false;
    notifyListeners();
  }

  Future<bool> book(LabTest test, DateTime date) async {
    // === Backend integration: POST /api/v1/lab/book ===
    await Future.delayed(const Duration(milliseconds: 500));
    _bookings.insert(
      0,
      LabBooking(
        id: DateTime.now().millisecondsSinceEpoch,
        labTestId: test.id,
        patientId: 1,
        bookingDate: date,
        status: 'pending',
        labTest: test,
      ),
    );
    notifyListeners();
    return true;
  }

  List<LabTest> _mockTests() => [
        LabTest(id: 1, name: 'Complete Blood Count (CBC)', category: 'Hematology', price: 350, durationHours: 6, description: 'A complete picture of red blood cells, white blood cells, and platelets.'),
        LabTest(id: 2, name: 'Lipid Profile', category: 'Biochemistry', price: 700, durationHours: 24, description: 'Cholesterol, triglycerides, HDL, LDL.'),
        LabTest(id: 3, name: 'Thyroid Profile (T3, T4, TSH)', category: 'Endocrinology', price: 600, durationHours: 24),
        LabTest(id: 4, name: 'Liver Function Test (LFT)', category: 'Biochemistry', price: 800, durationHours: 24),
        LabTest(id: 5, name: 'Kidney Function Test (KFT)', category: 'Biochemistry', price: 750, durationHours: 24),
        LabTest(id: 6, name: 'HbA1c (Diabetes)', category: 'Diabetes', price: 500, durationHours: 12),
        LabTest(id: 7, name: 'Vitamin D', category: 'Vitamins', price: 1200, durationHours: 48),
        LabTest(id: 8, name: 'Vitamin B12', category: 'Vitamins', price: 900, durationHours: 48),
        LabTest(id: 9, name: 'Urine Routine', category: 'Pathology', price: 200, durationHours: 6),
        LabTest(id: 10, name: 'COVID-19 RT-PCR', category: 'Pathology', price: 500, durationHours: 12),
      ];

  List<LabBooking> _mockBookings() => [
        LabBooking(
          id: 301,
          labTestId: 1,
          patientId: 1,
          bookingDate: DateTime.now().add(const Duration(days: 1)),
          status: 'confirmed',
          paymentStatus: 'paid',
          labTest: LabTest(id: 1, name: 'Complete Blood Count (CBC)', price: 350),
        ),
        LabBooking(
          id: 302,
          labTestId: 2,
          patientId: 1,
          bookingDate: DateTime.now().subtract(const Duration(days: 7)),
          status: 'completed',
          paymentStatus: 'paid',
          resultFile: 'report_2026_05_03.pdf',
          labTest: LabTest(id: 2, name: 'Lipid Profile', price: 700),
        ),
      ];
}
