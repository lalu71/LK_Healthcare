class LabTest {
  final int id;
  final String name;
  final String? category;
  final num price;
  final int? durationHours;
  final String? description;
  final bool isActive;

  LabTest({
    required this.id,
    required this.name,
    this.category,
    required this.price,
    this.durationHours,
    this.description,
    this.isActive = true,
  });

  factory LabTest.fromJson(Map<String, dynamic> json) => LabTest(
        id: json['id'] as int,
        name: json['name'] as String,
        category: json['category'] as String?,
        price: json['price'] as num? ?? 0,
        durationHours: json['duration_hours'] as int?,
        description: json['description'] as String?,
        isActive: (json['is_active'] as bool?) ?? true,
      );
}

class LabBooking {
  final int id;
  final int labTestId;
  final int patientId;
  final DateTime bookingDate;
  final String status;
  final String? resultFile;
  final String paymentStatus;
  final LabTest? labTest;

  LabBooking({
    required this.id,
    required this.labTestId,
    required this.patientId,
    required this.bookingDate,
    this.status = 'pending',
    this.resultFile,
    this.paymentStatus = 'unpaid',
    this.labTest,
  });

  factory LabBooking.fromJson(Map<String, dynamic> json) => LabBooking(
        id: json['id'] as int,
        labTestId: json['lab_test_id'] as int,
        patientId: json['patient_id'] as int,
        bookingDate: DateTime.parse(json['booking_date'] as String),
        status: json['status'] as String? ?? 'pending',
        resultFile: json['result_file'] as String?,
        paymentStatus: json['payment_status'] as String? ?? 'unpaid',
        labTest: json['lab_test'] != null ? LabTest.fromJson(json['lab_test']) : null,
      );
}
