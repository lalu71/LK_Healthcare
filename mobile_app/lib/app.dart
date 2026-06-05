import 'package:flutter/material.dart';

import 'config/routes.dart';
import 'config/theme.dart';
import 'utils/constants.dart';

class LKHealthcareApp extends StatefulWidget {
  const LKHealthcareApp({super.key});

  @override
  State<LKHealthcareApp> createState() => _LKHealthcareAppState();
}

class _LKHealthcareAppState extends State<LKHealthcareApp> {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppStrings.appName,
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      initialRoute: AppRoutes.splash,
      routes: AppRoutes.routes,
      builder: (context, child) {
        // Lock text scaling so the design stays consistent across devices
        // and use LayoutBuilder to enforce a max width on tablets.
        return MediaQuery(
          data: MediaQuery.of(context).copyWith(
            textScaler: MediaQuery.of(context).textScaler.clamp(
                  minScaleFactor: 0.85,
                  maxScaleFactor: 1.15,
                ),
          ),
          child: LayoutBuilder(
            builder: (ctx, constraints) {
              if (constraints.maxWidth > 600) {
                return Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 480),
                    child: child!,
                  ),
                );
              }
              return child!;
            },
          ),
        );
      },
    );
  }
}
