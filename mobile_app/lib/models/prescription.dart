import 'doctor.dart';
import 'patient.dart';

class Prescription {
  final int id;
  final int doctorId;
  final int patientId;
  final int? appointmentId;
  final String? diagnosis;
  final String? advice;
  final String? followUpDate;
  final DateTime createdAt;
  final String status;
  final String paymentStatus;
  final List<PrescriptionItem> items;
  final Doctor? doctor;
  final Patient? patient;

  Prescription({
    required this.id,
    required this.doctorId,
    required this.patientId,
    this.appointmentId,
    this.diagnosis,
    this.advice,
    this.followUpDate,
    required this.createdAt,
    this.status = 'issued',
    this.paymentStatus = 'unpaid',
    this.items = const [],
    this.doctor,
    this.patient,
  });

  factory Prescription.fromJson(Map<String, dynamic> json) => Prescription(
        id: json['id'] as int,
        doctorId: json['doctor_id'] as int,
        patientId: json['patient_id'] as int,
        appointmentId: json['appointment_id'] as int?,
        diagnosis: json['diagnosis'] as String?,
        advice: json['advice'] as String?,
        followUpDate: json['follow_up_date'] as String?,
        createdAt: DateTime.parse(json['created_at'] as String),
        status: json['status'] as String? ?? 'issued',
        paymentStatus: json['payment_status'] as String? ?? 'unpaid',
        items: (json['items'] as List?)
                ?.map((e) => PrescriptionItem.fromJson(e))
                .toList() ??
            const [],
        doctor: json['doctor'] != null ? Doctor.fromJson(json['doctor']) : null,
        patient: json['patient'] != null ? Patient.fromJson(json['patient']) : null,
      );
}

class PrescriptionItem {
  final int id;
  final String medicineName;
  final String? dosage;
  final String? frequency;
  final String? duration;
  final String? instructions;

  PrescriptionItem({
    required this.id,
    required this.medicineName,
    this.dosage,
    this.frequency,
    this.duration,
    this.instructions,
  });

  factory PrescriptionItem.fromJson(Map<String, dynamic> json) => PrescriptionItem(
        id: json['id'] as int? ?? 0,
        medicineName: json['medicine_name'] as String? ?? '',
        dosage: json['dosage'] as String?,
        frequency: json['frequency'] as String?,
        duration: json['duration'] as String?,
        instructions: json['instructions'] as String?,
      );
}
