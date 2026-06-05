class AppStrings {
  AppStrings._();

  static const String appName = 'LK Healthcare';
  static const String tagline = 'Your trusted healthcare partner';
  static const String emergencyHelpline = '+91-100';
}

class UserRole {
  UserRole._();

  static const String patient = 'patient';
  static const String doctor = 'doctor';
}

class AppointmentStatus {
  AppointmentStatus._();

  static const String pending = 'pending';
  static const String confirmed = 'confirmed';
  static const String completed = 'completed';
  static const String cancelled = 'cancelled';
}

class PaymentStatus {
  PaymentStatus._();

  static const String unpaid = 'unpaid';
  static const String paid = 'paid';
  static const String pending = 'pending';
  static const String refunded = 'refunded';
}

class BloodGroup {
  BloodGroup._();

  static const List<String> all = [
    'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-',
  ];
}
