import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../utils/constants.dart';

class AboutScreen extends StatefulWidget {
  const AboutScreen({super.key});

  @override
  State<AboutScreen> createState() => _AboutScreenState();
}

class _AboutScreenState extends State<AboutScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('About Us')),
      body: SingleChildScrollView(
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(24, 28, 24, 32),
              decoration: const BoxDecoration(gradient: AppColors.primaryGradient),
              child: Column(
                children: [
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Icon(Icons.medical_services_rounded,
                        color: Colors.white, size: 44),
                  ),
                  const SizedBox(height: 14),
                  const Text(AppStrings.appName,
                      style: TextStyle(
                          color: Colors.white,
                          fontSize: 22,
                          fontWeight: FontWeight.w800)),
                  const SizedBox(height: 4),
                  Text(AppStrings.tagline,
                      style: TextStyle(
                          color: Colors.white.withOpacity(0.85), fontSize: 13)),
                ],
              ),
            ),
            const SizedBox(height: 20),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _section(
                    'Our Mission',
                    'To make quality healthcare accessible and affordable for every Indian. We connect patients with verified doctors, pharmacies, and labs through a single, secure platform.',
                    Icons.flag_rounded,
                    AppColors.primary,
                  ),
                  _section(
                    'Our Vision',
                    'A future where healthcare is a few taps away — from booking appointments to receiving medicines, accessing lab reports, and saving lives through fast emergency response.',
                    Icons.remove_red_eye_rounded,
                    AppColors.accent,
                  ),
                  _section(
                    'What We Offer',
                    'Doctor consultations, lab bookings, pharmacy delivery, blood bank network, medical records storage, and 24/7 emergency response — all in one app.',
                    Icons.local_hospital_rounded,
                    AppColors.warning,
                  ),
                  const SizedBox(height: 20),
                  _stats(),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _section(String title, String body, IconData icon, Color color) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  color: color.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, color: color, size: 20),
              ),
              const SizedBox(width: 10),
              Text(title,
                  style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary)),
            ],
          ),
          const SizedBox(height: 10),
          Text(body,
              style: const TextStyle(
                  color: AppColors.textSecondary, fontSize: 13.5, height: 1.6)),
        ],
      ),
    );
  }

  Widget _stats() {
    final data = [
      ('100+', 'Doctors'),
      ('50K+', 'Patients'),
      ('8', 'Cities'),
      ('24/7', 'Support'),
    ];
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        children: data
            .map((e) => Expanded(
                  child: Column(
                    children: [
                      Text(e.$1,
                          style: const TextStyle(
                              color: Colors.white,
                              fontSize: 22,
                              fontWeight: FontWeight.w800)),
                      const SizedBox(height: 2),
                      Text(e.$2,
                          style: TextStyle(
                              color: Colors.white.withOpacity(0.85),
                              fontSize: 11.5)),
                    ],
                  ),
                ))
            .toList(),
      ),
    );
  }
}
