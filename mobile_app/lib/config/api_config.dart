/// Backend API configuration.
///
/// For Android emulator pointing to host machine: 10.0.2.2
/// For iOS simulator: 127.0.0.1
/// For physical device: replace with your machine's LAN IP
class ApiConfig {
  ApiConfig._();

  static const String baseUrl = 'http://10.0.2.2:8000';
  static const String apiPrefix = '/api/v1';

  static String api(String path) => '$baseUrl$apiPrefix$path';

  // Auth
  static String get login => api('/login');
  static String get register => api('/register');
  static String get logout => api('/logout');
  static String get me => api('/user');

  // Patient
  static String get patientProfile => api('/patient/me');

  // Doctor
  static String get doctors => api('/doctors');
  static String doctorDetail(int id) => api('/doctors/$id');
  static String doctorSlots(int id) => api('/doctors/$id/slots');
  static String get specializations => api('/specializations');
  static String get doctorProfile => api('/doctor/me');
  static String get doctorAvailability => api('/doctor/availability');
  static String get doctorAppointments => api('/doctor/appointments');

  // Appointments
  static String get myAppointments => api('/my/appointments');
  static String get appointments => api('/appointments');

  // Prescriptions
  static String get myPrescriptions => api('/my/prescriptions');
  static String get doctorPrescriptions => api('/doctor/prescriptions');

  // Lab
  static String get labTests => api('/lab/tests');
  static String get myLabBookings => api('/my/lab-bookings');
  static String get labBook => api('/lab/book');

  // Pharmacy
  static String get medicines => api('/pharmacy/medicines');
  static String get myPharmacyOrders => api('/my/pharmacy-orders');

  // Blood
  static String get bloodInventory => api('/blood/inventory');
  static String get bloodDonors => api('/blood/donors');
  static String get bloodRequests => api('/blood/requests');

  // Emergency
  static String get emergency => api('/emergency');
  static String get myEmergencyRequests => api('/my/emergency-requests');

  // Notifications & Records
  static String get notifications => api('/notifications');
  static String get medicalRecords => api('/medical-records');
}
