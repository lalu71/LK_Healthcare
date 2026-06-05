import 'package:flutter/material.dart';

import '../screens/splash_screen.dart';
import '../screens/onboarding_screen.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/auth/forgot_password_screen.dart';

import '../screens/public/home_screen.dart';
import '../screens/public/about_screen.dart';
import '../screens/public/services_screen.dart';
import '../screens/public/doctors_list_screen.dart';
import '../screens/public/contact_screen.dart';

import '../screens/patient/patient_dashboard_screen.dart';
import '../screens/patient/patient_profile_screen.dart';
import '../screens/patient/book_appointment_screen.dart';
import '../screens/patient/my_appointments_screen.dart';
import '../screens/patient/appointment_detail_screen.dart';
import '../screens/patient/prescriptions_screen.dart';
import '../screens/patient/prescription_detail_screen.dart';
import '../screens/patient/medical_records_screen.dart';
import '../screens/patient/lab_tests_screen.dart';
import '../screens/patient/pharmacy_screen.dart';

import '../screens/doctor/doctor_dashboard_screen.dart';
import '../screens/doctor/doctor_profile_screen.dart';
import '../screens/doctor/doctor_appointments_screen.dart';
import '../screens/doctor/doctor_availability_screen.dart';
import '../screens/doctor/doctor_prescriptions_screen.dart';
import '../screens/doctor/create_prescription_screen.dart';

import '../screens/shared/blood_bank_screen.dart';
import '../screens/shared/emergency_screen.dart';
import '../screens/shared/notifications_screen.dart';
import '../screens/shared/payment_screen.dart';
import '../screens/shared/receipt_screen.dart';

class AppRoutes {
  AppRoutes._();

  // Splash / Onboarding
  static const String splash = '/';
  static const String onboarding = '/onboarding';

  // Auth
  static const String login = '/login';
  static const String register = '/register';
  static const String forgotPassword = '/forgot-password';

  // Public
  static const String home = '/home';
  static const String about = '/about';
  static const String services = '/services';
  static const String doctorsList = '/doctors';
  static const String contact = '/contact';

  // Patient
  static const String patientDashboard = '/patient/dashboard';
  static const String patientProfile = '/patient/profile';
  static const String bookAppointment = '/patient/book-appointment';
  static const String myAppointments = '/patient/appointments';
  static const String appointmentDetail = '/patient/appointment-detail';
  static const String prescriptions = '/patient/prescriptions';
  static const String prescriptionDetail = '/patient/prescription-detail';
  static const String medicalRecords = '/patient/medical-records';
  static const String labTests = '/patient/lab';
  static const String pharmacy = '/patient/pharmacy';

  // Doctor
  static const String doctorDashboard = '/doctor/dashboard';
  static const String doctorProfile = '/doctor/profile';
  static const String doctorAppointments = '/doctor/appointments';
  static const String doctorAvailability = '/doctor/availability';
  static const String doctorPrescriptions = '/doctor/prescriptions';
  static const String createPrescription = '/doctor/create-prescription';

  // Shared
  static const String bloodBank = '/blood-bank';
  static const String emergency = '/emergency';
  static const String notifications = '/notifications';
  static const String payment = '/payment';
  static const String receipt = '/receipt';

  static Map<String, WidgetBuilder> routes = {
    splash: (_) => const SplashScreen(),
    onboarding: (_) => const OnboardingScreen(),

    login: (_) => const LoginScreen(),
    register: (_) => const RegisterScreen(),
    forgotPassword: (_) => const ForgotPasswordScreen(),

    home: (_) => const HomeScreen(),
    about: (_) => const AboutScreen(),
    services: (_) => const ServicesScreen(),
    doctorsList: (_) => const DoctorsListScreen(),
    contact: (_) => const ContactScreen(),

    patientDashboard: (_) => const PatientDashboardScreen(),
    patientProfile: (_) => const PatientProfileScreen(),
    bookAppointment: (_) => const BookAppointmentScreen(),
    myAppointments: (_) => const MyAppointmentsScreen(),
    appointmentDetail: (_) => const AppointmentDetailScreen(),
    prescriptions: (_) => const PrescriptionsScreen(),
    prescriptionDetail: (_) => const PrescriptionDetailScreen(),
    medicalRecords: (_) => const MedicalRecordsScreen(),
    labTests: (_) => const LabTestsScreen(),
    pharmacy: (_) => const PharmacyScreen(),

    doctorDashboard: (_) => const DoctorDashboardScreen(),
    doctorProfile: (_) => const DoctorProfileScreen(),
    doctorAppointments: (_) => const DoctorAppointmentsScreen(),
    doctorAvailability: (_) => const DoctorAvailabilityScreen(),
    doctorPrescriptions: (_) => const DoctorPrescriptionsScreen(),
    createPrescription: (_) => const CreatePrescriptionScreen(),

    bloodBank: (_) => const BloodBankScreen(),
    emergency: (_) => const EmergencyScreen(),
    notifications: (_) => const NotificationsScreen(),
    payment: (_) => const PaymentScreen(),
    receipt: (_) => const ReceiptScreen(),
  };
}
