class MedicalRecord {
  final int id;
  final int patientId;
  final String filePath;
  final String? recordType;
  final String? title;
  final String? notes;
  final DateTime uploadedAt;

  MedicalRecord({
    required this.id,
    required this.patientId,
    required this.filePath,
    this.recordType,
    this.title,
    this.notes,
    required this.uploadedAt,
  });

  factory MedicalRecord.fromJson(Map<String, dynamic> json) => MedicalRecord(
        id: json['id'] as int,
        patientId: json['patient_id'] as int,
        filePath: json['file_path'] as String? ?? '',
        recordType: json['record_type'] as String?,
        title: json['title'] as String?,
        notes: json['notes'] as String?,
        uploadedAt: DateTime.parse(json['uploaded_at'] as String? ??
            json['created_at'] as String? ??
            DateTime.now().toIso8601String()),
      );
}
