class AppUser {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? avatar;
  final bool isActive;

  AppUser({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.avatar,
    this.isActive = true,
  });

  factory AppUser.fromJson(Map<String, dynamic> json) => AppUser(
        id: json['id'] as int,
        name: json['name'] as String? ?? '',
        email: json['email'] as String? ?? '',
        phone: json['phone'] as String?,
        role: (json['role'] ?? json['roles']?[0] ?? 'patient').toString(),
        avatar: json['avatar'] as String?,
        isActive: (json['is_active'] as bool?) ?? true,
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'email': email,
        'phone': phone,
        'role': role,
        'avatar': avatar,
        'is_active': isActive,
      };
}
