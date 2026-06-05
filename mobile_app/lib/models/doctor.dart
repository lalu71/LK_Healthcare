class Doctor {
  final int id;
  final int? userId;
  final String name;
  final String? specialization;
  final int? specializationId;
  final String? qualification;
  final String? bio;
  final int? experienceYears;
  final num? fee;
  final String? clinicAddress;
  final String? phone;
  final String? email;
  final String? avatar;
  final double? rating;

  Doctor({
    required this.id,
    this.userId,
    required this.name,
    this.specialization,
    this.specializationId,
    this.qualification,
    this.bio,
    this.experienceYears,
    this.fee,
    this.clinicAddress,
    this.phone,
    this.email,
    this.avatar,
    this.rating,
  });

  factory Doctor.fromJson(Map<String, dynamic> json) => Doctor(
        id: json['id'] as int,
        userId: json['user_id'] as int?,
        name: json['name'] as String? ?? json['user']?['name'] ?? '',
        specialization:
            json['specialization']?['name'] as String? ?? json['specialization'] as String?,
        specializationId: json['specialization_id'] as int?,
        qualification: json['qualification'] as String?,
        bio: json['bio'] as String?,
        experienceYears: json['experience_years'] as int?,
        fee: json['fee'] as num?,
        clinicAddress: json['clinic_address'] as String?,
        phone: json['phone'] as String?,
        email: json['email'] as String?,
        avatar: json['avatar'] as String?,
        rating: (json['rating'] as num?)?.toDouble(),
      );
}

class Specialization {
  final int id;
  final String name;
  final String? icon;

  Specialization({required this.id, required this.name, this.icon});

  factory Specialization.fromJson(Map<String, dynamic> json) => Specialization(
        id: json['id'] as int,
        name: json['name'] as String,
        icon: json['icon'] as String?,
      );
}
