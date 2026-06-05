import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../config/app_colors.dart';
import '../../controllers/emergency_controller.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/validators.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';

class EmergencyScreen extends StatefulWidget {
  const EmergencyScreen({super.key});

  @override
  State<EmergencyScreen> createState() => _EmergencyScreenState();
}

class _EmergencyScreenState extends State<EmergencyScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _phone = TextEditingController();
  final _location = TextEditingController();
  final _notes = TextEditingController();
  bool _submitting = false;

  late final AnimationController _pulse;

  @override
  void initState() {
    super.initState();
    _pulse = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _name.dispose();
    _phone.dispose();
    _location.dispose();
    _notes.dispose();
    _pulse.dispose();
    super.dispose();
  }

  Future<void> _raise() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _submitting = true);
    final ok = await context.read<EmergencyController>().raise(
          contactName: _name.text,
          phone: _phone.text,
          location: _location.text,
          notes: _notes.text,
        );
    setState(() => _submitting = false);
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, 'Help is on the way!');
      Navigator.pop(context);
    }
  }

  Future<void> _call() async {
    final uri = Uri(scheme: 'tel', path: AppStrings.emergencyHelpline);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Emergency SOS'),
        backgroundColor: AppColors.danger,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _sosButton(),
              const SizedBox(height: 20),
              _emergencyCallCard(),
              const SizedBox(height: 24),
              const Text('Or fill the form for help:',
                  style: TextStyle(
                      fontSize: 14, color: AppColors.textSecondary)),
              const SizedBox(height: 16),
              CustomTextField(
                label: 'Your Name',
                controller: _name,
                prefixIcon: Icons.person_outline,
                validator: (v) => Validators.required(v, 'Name'),
              ),
              const SizedBox(height: 12),
              CustomTextField(
                label: 'Phone Number',
                controller: _phone,
                prefixIcon: Icons.phone_outlined,
                keyboardType: TextInputType.phone,
                validator: Validators.phone,
              ),
              const SizedBox(height: 12),
              CustomTextField(
                label: 'Current Location',
                controller: _location,
                prefixIcon: Icons.location_on_outlined,
                suffixIcon: Icons.my_location_rounded,
                onSuffixTap: () => AppHelpers.snack(context, 'Detecting GPS...'),
              ),
              const SizedBox(height: 12),
              CustomTextField(
                label: 'Notes (Optional)',
                controller: _notes,
                prefixIcon: Icons.note_outlined,
                maxLines: 3,
                hint: 'Briefly describe the emergency...',
              ),
              const SizedBox(height: 24),
              PrimaryButton(
                label: 'Send SOS Request',
                icon: Icons.send_rounded,
                loading: _submitting,
                gradient: AppColors.dangerGradient,
                onPressed: _raise,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _sosButton() {
    return GestureDetector(
      onTap: _raise,
      child: AnimatedBuilder(
        animation: _pulse,
        builder: (_, child) {
          final size = 180.0 + (_pulse.value * 10);
          return Container(
            width: size,
            height: size,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: AppColors.dangerGradient,
              boxShadow: [
                BoxShadow(
                  color: AppColors.danger.withOpacity(0.5 - _pulse.value * 0.3),
                  blurRadius: 40 + _pulse.value * 20,
                  spreadRadius: 4 + _pulse.value * 4,
                ),
              ],
            ),
            child: const Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.local_hospital_rounded, color: Colors.white, size: 48),
                SizedBox(height: 8),
                Text('SOS',
                    style: TextStyle(
                        color: Colors.white,
                        fontSize: 32,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 4)),
                Text('Tap to alert',
                    style: TextStyle(color: Colors.white70, fontSize: 12)),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _emergencyCallCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: AppColors.success.withOpacity(0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.call_rounded, color: AppColors.success),
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Emergency Helpline',
                    style: TextStyle(
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                        color: AppColors.textPrimary)),
                Text('Available 24x7',
                    style: TextStyle(
                        fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          ElevatedButton.icon(
            onPressed: _call,
            icon: const Icon(Icons.phone_rounded, size: 16),
            label: const Text('Call'),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.success,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            ),
          ),
        ],
      ),
    );
  }
}
