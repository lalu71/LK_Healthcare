import 'package:flutter/foundation.dart';

import '../models/doctor.dart';
import '../models/appointment.dart';

class DoctorController extends ChangeNotifier {
  List<Doctor> _doctors = [];
  List<Specialization> _specializations = [];
  bool _loading = false;
  String? _error;
  String _query = '';
  int? _specializationFilter;

  List<Doctor> get doctors {
    var list = _doctors;
    if (_query.isNotEmpty) {
      list = list
          .where((d) =>
              d.name.toLowerCase().contains(_query.toLowerCase()) ||
              (d.specialization ?? '').toLowerCase().contains(_query.toLowerCase()))
          .toList();
    }
    if (_specializationFilter != null) {
      list = list.where((d) => d.specializationId == _specializationFilter).toList();
    }
    return list;
  }

  List<Specialization> get specializations => _specializations;
  bool get loading => _loading;
  String? get error => _error;
  int? get specializationFilter => _specializationFilter;

  void setQuery(String q) {
    _query = q;
    notifyListeners();
  }

  void setSpecialization(int? id) {
    _specializationFilter = id;
    notifyListeners();
  }

  Future<void> fetchDoctors() async {
    _loading = true;
    notifyListeners();
    try {
      // === Backend integration point ===
      // final res = await ApiService.instance.get(ApiConfig.doctors);
      // _doctors = (res['data'] as List).map((e) => Doctor.fromJson(e)).toList();

      await Future.delayed(const Duration(milliseconds: 500));
      _doctors = _mockDoctors();
      _specializations = _mockSpecializations();
    } catch (e) {
      _error = e.toString();
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<List<TimeSlot>> fetchSlots(int doctorId, DateTime date) async {
    // === Backend integration point ===
    // final res = await ApiService.instance.get(ApiConfig.doctorSlots(doctorId));
    await Future.delayed(const Duration(milliseconds: 400));
    return [
      TimeSlot(time: '09:00 AM'),
      TimeSlot(time: '09:30 AM', isAvailable: false),
      TimeSlot(time: '10:00 AM'),
      TimeSlot(time: '10:30 AM'),
      TimeSlot(time: '11:00 AM', isAvailable: false),
      TimeSlot(time: '11:30 AM'),
      TimeSlot(time: '02:00 PM'),
      TimeSlot(time: '02:30 PM'),
      TimeSlot(time: '03:00 PM', isAvailable: false),
      TimeSlot(time: '03:30 PM'),
      TimeSlot(time: '04:00 PM'),
      TimeSlot(time: '04:30 PM'),
    ];
  }

  Doctor? doctorById(int id) {
    try {
      return _doctors.firstWhere((d) => d.id == id);
    } catch (_) {
      return null;
    }
  }

  // -- Mock data ---------------------------------------------------------
  List<Specialization> _mockSpecializations() => const [
        Specialization(id: 1, name: 'Cardiology'),
        Specialization(id: 2, name: 'Dermatology'),
        Specialization(id: 3, name: 'Neurology'),
        Specialization(id: 4, name: 'Orthopedics'),
        Specialization(id: 5, name: 'Pediatrics'),
        Specialization(id: 6, name: 'General Medicine'),
        Specialization(id: 7, name: 'Gynecology'),
        Specialization(id: 8, name: 'ENT'),
      ];

  List<Doctor> _mockDoctors() => [
        Doctor(
          id: 1,
          name: 'Dr. Aarav Mehta',
          specialization: 'Cardiology',
          specializationId: 1,
          qualification: 'MBBS, MD (Cardiology)',
          experienceYears: 12,
          fee: 600,
          rating: 4.8,
          bio: 'Specialist in interventional cardiology with 12+ years of experience.',
          clinicAddress: 'Apollo Clinic, Mumbai',
        ),
        Doctor(
          id: 2,
          name: 'Dr. Priya Sharma',
          specialization: 'Dermatology',
          specializationId: 2,
          qualification: 'MBBS, MD (Skin)',
          experienceYears: 8,
          fee: 500,
          rating: 4.7,
          bio: 'Cosmetic and clinical dermatology expert.',
          clinicAddress: 'Skin Care Clinic, Delhi',
        ),
        Doctor(
          id: 3,
          name: 'Dr. Rajesh Iyer',
          specialization: 'Neurology',
          specializationId: 3,
          qualification: 'MBBS, DM (Neuro)',
          experienceYears: 15,
          fee: 800,
          rating: 4.9,
          bio: 'Senior neurologist, stroke specialist.',
          clinicAddress: 'NeuroCare, Bangalore',
        ),
        Doctor(
          id: 4,
          name: 'Dr. Sneha Kapoor',
          specialization: 'Pediatrics',
          specializationId: 5,
          qualification: 'MBBS, MD (Pediatrics)',
          experienceYears: 10,
          fee: 450,
          rating: 4.8,
          bio: 'Child specialist with a gentle approach.',
          clinicAddress: 'KidsCare Hospital, Pune',
        ),
        Doctor(
          id: 5,
          name: 'Dr. Vikram Singh',
          specialization: 'Orthopedics',
          specializationId: 4,
          qualification: 'MBBS, MS (Ortho)',
          experienceYears: 14,
          fee: 700,
          rating: 4.6,
          bio: 'Joint replacement and sports injury expert.',
          clinicAddress: 'Bone & Joint Clinic, Chennai',
        ),
        Doctor(
          id: 6,
          name: 'Dr. Anjali Verma',
          specialization: 'Gynecology',
          specializationId: 7,
          qualification: 'MBBS, MD (OBGY)',
          experienceYears: 11,
          fee: 600,
          rating: 4.9,
          bio: 'Women\'s health and high-risk pregnancy specialist.',
          clinicAddress: 'Women Care, Hyderabad',
        ),
      ];
}
