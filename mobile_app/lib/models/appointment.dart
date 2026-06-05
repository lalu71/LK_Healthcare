import 'doctor.dart';
import 'patient.dart';

class Appointment {
  final int id;
  final int doctorId;
  final int patientId;
  final DateTime appointmentDate;
  final String? slot;
  final String status;
  final String? reason;
  final String paymentStatus;
  final num? fee;
  final Doctor? doctor;
  final Patient? patient;

  Appointment({
    required this.id,
    required this.doctorId,
    required this.patientId,
    required this.appointmentDate,
    this.slot,
    required this.status,
    this.reason,
    this.paymentStatus = 'unpaid',
    this.fee,
    this.doctor,
    this.patient,
  });

  factory Appointment.fromJson(Map<String, dynamic> json) => Appointment(
        id: json['id'] as int,
        doctorId: json['doctor_id'] as int,
        patientId: json['patient_id'] as int,
        appointmentDate: DateTime.parse(json['appointment_date'] as String),
        slot: json['slot'] as String?,
        status: json['status'] as String? ?? 'pending',
        reason: json['reason'] as String?,
        paymentStatus: json['payment_status'] as String? ?? 'unpaid',
        fee: json['fee'] as num?,
        doctor: json['doctor'] != null ? Doctor.fromJson(json['doctor']) : null,
        patient: json['patient'] != null ? Patient.fromJson(json['patient']) : null,
      );
}

class TimeSlot {
  final String time;
  final bool isAvailable;

  TimeSlot({required this.time, this.isAvailable = true});
}
