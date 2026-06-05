import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import 'app.dart';
import 'controllers/appointment_controller.dart';
import 'controllers/auth_controller.dart';
import 'controllers/blood_controller.dart';
import 'controllers/doctor_controller.dart';
import 'controllers/emergency_controller.dart';
import 'controllers/lab_controller.dart';
import 'controllers/notification_controller.dart';
import 'controllers/pharmacy_controller.dart';
import 'controllers/prescription_controller.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthController()),
        ChangeNotifierProvider(create: (_) => DoctorController()),
        ChangeNotifierProvider(create: (_) => AppointmentController()),
        ChangeNotifierProvider(create: (_) => PrescriptionController()),
        ChangeNotifierProvider(create: (_) => LabController()),
        ChangeNotifierProvider(create: (_) => PharmacyController()),
        ChangeNotifierProvider(create: (_) => BloodController()),
        ChangeNotifierProvider(create: (_) => EmergencyController()),
        ChangeNotifierProvider(create: (_) => NotificationController()),
      ],
      child: const LKHealthcareApp(),
    ),
  );
}
