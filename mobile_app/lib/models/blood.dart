class BloodInventory {
  final int id;
  final String bloodGroup;
  final int units;

  BloodInventory({required this.id, required this.bloodGroup, required this.units});

  factory BloodInventory.fromJson(Map<String, dynamic> json) => BloodInventory(
        id: json['id'] as int? ?? 0,
        bloodGroup: json['blood_group'] as String? ?? '',
        units: json['units'] as int? ?? 0,
      );
}

class BloodRequest {
  final int id;
  final int? userId;
  final String patientName;
  final String bloodGroup;
  final int units;
  final DateTime? neededBy;
  final String status;
  final String? hospital;
  final String? contact;

  BloodRequest({
    required this.id,
    this.userId,
    required this.patientName,
    required this.bloodGroup,
    required this.units,
    this.neededBy,
    this.status = 'pending',
    this.hospital,
    this.contact,
  });

  factory BloodRequest.fromJson(Map<String, dynamic> json) => BloodRequest(
        id: json['id'] as int? ?? 0,
        userId: json['user_id'] as int?,
        patientName: json['patient_name'] as String? ?? '',
        bloodGroup: json['blood_group'] as String? ?? '',
        units: json['units'] as int? ?? 1,
        neededBy: json['needed_by'] != null
            ? DateTime.tryParse(json['needed_by'] as String)
            : null,
        status: json['status'] as String? ?? 'pending',
        hospital: json['hospital'] as String?,
        contact: json['contact'] as String?,
      );
}

class BloodDonor {
  final int id;
  final String name;
  final String bloodGroup;
  final String phone;
  final DateTime? lastDonated;
  final bool isAvailable;
  final String? city;

  BloodDonor({
    required this.id,
    required this.name,
    required this.bloodGroup,
    required this.phone,
    this.lastDonated,
    this.isAvailable = true,
    this.city,
  });

  factory BloodDonor.fromJson(Map<String, dynamic> json) => BloodDonor(
        id: json['id'] as int? ?? 0,
        name: json['name'] as String? ?? '',
        bloodGroup: json['blood_group'] as String? ?? '',
        phone: json['phone'] as String? ?? '',
        lastDonated: json['last_donated_at'] != null
            ? DateTime.tryParse(json['last_donated_at'] as String)
            : null,
        isAvailable: (json['is_available'] as bool?) ?? true,
        city: json['city'] as String?,
      );
}
