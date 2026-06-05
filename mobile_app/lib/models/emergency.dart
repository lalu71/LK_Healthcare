class EmergencyRequest {
  final int id;
  final int? userId;
  final String contactName;
  final String? phone;
  final String? location;
  final double? latitude;
  final double? longitude;
  final String status;
  final String? notes;
  final DateTime createdAt;

  EmergencyRequest({
    required this.id,
    this.userId,
    required this.contactName,
    this.phone,
    this.location,
    this.latitude,
    this.longitude,
    this.status = 'pending',
    this.notes,
    required this.createdAt,
  });

  factory EmergencyRequest.fromJson(Map<String, dynamic> json) => EmergencyRequest(
        id: json['id'] as int? ?? 0,
        userId: json['user_id'] as int?,
        contactName: json['contact_name'] as String? ?? '',
        phone: json['phone'] as String?,
        location: json['location'] as String?,
        latitude: (json['latitude'] as num?)?.toDouble(),
        longitude: (json['longitude'] as num?)?.toDouble(),
        status: json['status'] as String? ?? 'pending',
        notes: json['notes'] as String?,
        createdAt: DateTime.parse(
            json['created_at'] as String? ?? DateTime.now().toIso8601String()),
      );
}
