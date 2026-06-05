import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';

class ServicesScreen extends StatefulWidget {
  const ServicesScreen({super.key});

  @override
  State<ServicesScreen> createState() => _ServicesScreenState();
}

class _ServicesScreenState extends State<ServicesScreen> {
  final List<_Service> _services = const [
    _Service(
      icon: Icons.medical_services_rounded,
      color: AppColors.primary,
      title: 'Doctor Consultations',
      description:
          'Book in-person or video consultations with 100+ verified specialists across cardiology, pediatrics, dermatology and more.',
      route: AppRoutes.doctorsList,
    ),
    _Service(
      icon: Icons.science_rounded,
      color: AppColors.warning,
      title: 'Lab Tests',
      description:
          'Browse our extensive catalog of pathology and diagnostic tests. Book home sample collection and download digital reports.',
      route: AppRoutes.login,
    ),
    _Service(
      icon: Icons.local_pharmacy_rounded,
      color: AppColors.accent,
      title: 'Online Pharmacy',
      description:
          'Order prescription and OTC medicines with home delivery. Upload prescription or buy directly.',
      route: AppRoutes.login,
    ),
    _Service(
      icon: Icons.bloodtype_rounded,
      color: AppColors.danger,
      title: 'Blood Bank',
      description:
          'Find blood donors, check inventory across blood groups, and place emergency blood requests.',
      route: AppRoutes.bloodBank,
    ),
    _Service(
      icon: Icons.folder_special_rounded,
      color: AppColors.adminColor,
      title: 'Medical Records',
      description:
          'Securely store and access your medical history, prescriptions, and lab reports anytime.',
      route: AppRoutes.login,
    ),
    _Service(
      icon: Icons.local_hospital_rounded,
      color: AppColors.danger,
      title: 'Emergency SOS',
      description:
          'One-tap emergency alerts with location sharing, ambulance booking, and nearest hospital info.',
      route: AppRoutes.emergency,
    ),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Our Services')),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _services.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (_, i) {
          final s = _services[i];
          return _ServiceTile(service: s);
        },
      ),
    );
  }
}

class _Service {
  final IconData icon;
  final Color color;
  final String title;
  final String description;
  final String route;

  const _Service({
    required this.icon,
    required this.color,
    required this.title,
    required this.description,
    required this.route,
  });
}

class _ServiceTile extends StatelessWidget {
  final _Service service;
  const _ServiceTile({required this.service});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        borderRadius: BorderRadius.circular(18),
        onTap: () => Navigator.pushNamed(context, service.route),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [service.color, service.color.withOpacity(0.7)],
                  ),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(service.icon, color: Colors.white, size: 28),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(service.title,
                        style: const TextStyle(
                            fontSize: 15.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary)),
                    const SizedBox(height: 6),
                    Text(service.description,
                        style: const TextStyle(
                            fontSize: 12.5,
                            color: AppColors.textSecondary,
                            height: 1.5)),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              const Icon(Icons.chevron_right_rounded,
                  color: AppColors.textMuted),
            ],
          ),
        ),
      ),
    );
  }
}
