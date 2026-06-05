import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../utils/constants.dart';
import '../../widgets/feature_card.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/section_header.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _hero(),
              const SizedBox(height: 24),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SectionHeader(title: 'Our Services'),
                    const SizedBox(height: 12),
                    GridView.count(
                      crossAxisCount: 2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      childAspectRatio: 1.05,
                      children: [
                        FeatureCard(
                            icon: Icons.medical_services_rounded,
                            title: 'Find Doctors',
                            subtitle: '100+ specialists',
                            color: AppColors.primary,
                            onTap: () => Navigator.pushNamed(context, AppRoutes.doctorsList)),
                        FeatureCard(
                            icon: Icons.science_rounded,
                            title: 'Lab Tests',
                            subtitle: 'Book in 1 tap',
                            color: AppColors.warning,
                            onTap: () => Navigator.pushNamed(context, AppRoutes.login)),
                        FeatureCard(
                            icon: Icons.local_pharmacy_rounded,
                            title: 'Pharmacy',
                            subtitle: 'Home delivery',
                            color: AppColors.accent,
                            onTap: () => Navigator.pushNamed(context, AppRoutes.login)),
                        FeatureCard(
                            icon: Icons.bloodtype_rounded,
                            title: 'Blood Bank',
                            subtitle: 'Find donors',
                            color: AppColors.danger,
                            onTap: () => Navigator.pushNamed(context, AppRoutes.bloodBank)),
                      ],
                    ),
                    const SizedBox(height: 24),
                    _whyUs(),
                    const SizedBox(height: 24),
                    _emergencyCard(),
                    const SizedBox(height: 24),
                    _footer(),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _hero() {
    return Container(
      decoration: const BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
      ),
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 32),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.medical_services_rounded, color: Colors.white),
              ),
              const SizedBox(width: 10),
              const Text(AppStrings.appName,
                  style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w800)),
              const Spacer(),
              IconButton(
                onPressed: () => Navigator.pushNamed(context, AppRoutes.login),
                icon: const Icon(Icons.login_rounded, color: Colors.white),
              ),
            ],
          ),
          const SizedBox(height: 20),
          const Text(
            'Your Health,\nOur Priority',
            style: TextStyle(
              color: Colors.white,
              fontSize: 30,
              fontWeight: FontWeight.w800,
              height: 1.2,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Book appointments, order medicines, find blood donors,\nand get instant emergency support.',
            style: TextStyle(
              color: Colors.white.withOpacity(0.9),
              fontSize: 13.5,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              Expanded(
                child: PrimaryButton(
                  label: 'Login',
                  icon: Icons.login_rounded,
                  gradient: const LinearGradient(
                    colors: [Colors.white, Colors.white],
                  ),
                  onPressed: () => Navigator.pushNamed(context, AppRoutes.login),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: OutlinedButton(
                  onPressed: () => Navigator.pushNamed(context, AppRoutes.register),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Colors.white, width: 1.5),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child: const Text('Sign Up'),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _whyUs() {
    final items = [
      ('Verified Doctors', Icons.verified_rounded, AppColors.success),
      ('24/7 Support', Icons.headset_mic_rounded, AppColors.primary),
      ('Secure Payments', Icons.lock_rounded, AppColors.accent),
      ('Privacy Protected', Icons.shield_rounded, AppColors.warning),
    ];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SectionHeader(title: 'Why Choose Us'),
        const SizedBox(height: 12),
        Wrap(
          spacing: 12,
          runSpacing: 12,
          children: items
              .map((e) => SizedBox(
                    width: (MediaQuery.of(context).size.width - 52) / 2,
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: Row(
                        children: [
                          Icon(e.$2, color: e.$3, size: 22),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Text(e.$1,
                                style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w600,
                                    color: AppColors.textPrimary)),
                          ),
                        ],
                      ),
                    ),
                  ))
              .toList(),
        ),
      ],
    );
  }

  Widget _emergencyCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: AppColors.dangerGradient,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: AppColors.danger.withOpacity(0.3),
            offset: const Offset(0, 8),
            blurRadius: 20,
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.25),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(Icons.local_hospital_rounded,
                color: Colors.white, size: 32),
          ),
          const SizedBox(width: 14),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Emergency?',
                    style: TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.w800)),
                SizedBox(height: 2),
                Text('One tap to alert and get help nearby',
                    style: TextStyle(color: Colors.white70, fontSize: 12)),
              ],
            ),
          ),
          GestureDetector(
            onTap: () => Navigator.pushNamed(context, AppRoutes.emergency),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Text('SOS',
                  style: TextStyle(
                      color: AppColors.danger,
                      fontWeight: FontWeight.w800,
                      fontSize: 14)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _footer() {
    return Wrap(
      spacing: 16,
      runSpacing: 8,
      children: [
        TextButton(
            onPressed: () => Navigator.pushNamed(context, AppRoutes.about),
            child: const Text('About')),
        TextButton(
            onPressed: () => Navigator.pushNamed(context, AppRoutes.services),
            child: const Text('Services')),
        TextButton(
            onPressed: () => Navigator.pushNamed(context, AppRoutes.contact),
            child: const Text('Contact')),
      ],
    );
  }
}
