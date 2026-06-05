class Patient {
  final int id;
  final int userId;
  final String name;
  final String? dob;
  final String? gender;
  final String? bloodGroup;
  final String? address;
  final String? aadhaar;
  final String? phone;
  final String? email;
  final String? avatar;

  Patient({
    required this.id,
    required this.userId,
    required this.name,
    this.dob,
    this.gender,
    this.bloodGroup,
    this.address,
    this.aadhaar,
    this.phone,
    this.email,
    this.avatar,
  });

  factory Patient.fromJson(Map<String, dynamic> json) => Patient(
        id: json['id'] as int,
        userId: json['user_id'] as int? ?? 0,
        name: json['name'] as String? ?? json['user']?['name'] ?? '',
        dob: json['dob'] as String?,
        gender: json['gender'] as String?,
        bloodGroup: json['blood_group'] as String?,
        address: json['address'] as String?,
        aadhaar: json['aadhaar'] as String?,
        phone: json['phone'] as String?,
        email: json['email'] as String?,
        avatar: json['avatar'] as String?,
      );
}
