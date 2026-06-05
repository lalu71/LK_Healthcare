import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../utils/helpers.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';

class DoctorProfileScreen extends StatefulWidget {
  const DoctorProfileScreen({super.key});

  @override
  State<DoctorProfileScreen> createState() => _DoctorProfileScreenState();
}

class _DoctorProfileScreenState extends State<DoctorProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _qualification = TextEditingController();
  final _experience = TextEditingController();
  final _fee = TextEditingController();
  final _bio = TextEditingController();
  final _clinic = TextEditingController();
  String _specialization = 'Cardiology';
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthController>().user;
    _name.text = user?.name ?? '';
    _email.text = user?.email ?? '';
    _phone.text = user?.phone ?? '';
    _qualification.text = 'MBBS, MD';
    _experience.text = '10';
    _fee.text = '600';
    _bio.text = 'Experienced specialist focused on patient care.';
    _clinic.text = 'LK Healthcare Clinic';
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _phone.dispose();
    _qualification.dispose();
    _experience.dispose();
    _fee.dispose();
    _bio.dispose();
    _clinic.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    // === Backend integration: PUT /api/v1/doctor/me ===
    await Future.delayed(const Duration(milliseconds: 700));
    setState(() => _saving = false);
    if (!mounted) return;
    AppHelpers.snack(context, 'Profile updated');
  }

  @override
  Widget build(BuildContext context) {
    return RoleScaffold(
      title: 'My Profile',
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _avatar(),
              const SizedBox(height: 24),
              _sectionTitle('Professional Info'),
              CustomTextField(
                  label: 'Full Name',
                  controller: _name,
                  prefixIcon: Icons.person_outline),
              const SizedBox(height: 14),
              _specPicker(),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Qualification',
                  controller: _qualification,
                  prefixIcon: Icons.school_outlined),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Experience (years)',
                  controller: _experience,
                  prefixIcon: Icons.work_outline,
                  keyboardType: TextInputType.number),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Consultation Fee (₹)',
                  controller: _fee,
                  prefixIcon: Icons.currency_rupee_rounded,
                  keyboardType: TextInputType.number),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Bio',
                  controller: _bio,
                  maxLines: 3,
                  prefixIcon: Icons.description_outlined),
              const SizedBox(height: 22),
              _sectionTitle('Contact & Clinic'),
              CustomTextField(
                  label: 'Email',
                  controller: _email,
                  readOnly: true,
                  prefixIcon: Icons.email_outlined),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Phone',
                  controller: _phone,
                  keyboardType: TextInputType.phone,
                  prefixIcon: Icons.phone_outlined),
              const SizedBox(height: 14),
              CustomTextField(
                  label: 'Clinic Address',
                  controller: _clinic,
                  maxLines: 2,
                  prefixIcon: Icons.location_on_outlined),
              const SizedBox(height: 28),
              PrimaryButton(
                  label: 'Save Changes',
                  loading: _saving,
                  icon: Icons.check_rounded,
                  onPressed: _save),
              const SizedBox(height: 10),
              OutlinedButton.icon(
                onPressed: () async {
                  await context.read<AuthController>().logout();
                  if (context.mounted) {
                    Navigator.pushNamedAndRemoveUntil(
                        context, AppRoutes.login, (_) => false);
                  }
                },
                icon: const Icon(Icons.logout_rounded, color: AppColors.danger),
                label: const Text('Logout',
                    style: TextStyle(color: AppColors.danger)),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppColors.danger, width: 1.4),
                  minimumSize: const Size.fromHeight(52),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _avatar() {
    return Center(
      child: Column(
        children: [
          Container(
            width: 100,
            height: 100,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [AppColors.accent, AppColors.accentDark],
              ),
              borderRadius: BorderRadius.circular(28),
            ),
            child: const Icon(Icons.medical_services_rounded,
                color: Colors.white, size: 52),
          ),
          const SizedBox(height: 10),
          Text('Dr. ${_name.text}',
              style: const TextStyle(
                  fontSize: 18, fontWeight: FontWeight.w700)),
          Text(_specialization,
              style: const TextStyle(
                  color: AppColors.accent, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  Widget _sectionTitle(String t) => Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: Text(t,
            style: const TextStyle(fontSize: 14.5, fontWeight: FontWeight.w700)),
      );

  Widget _specPicker() {
    final specs = ['Cardiology', 'Dermatology', 'Neurology', 'Orthopedics', 'Pediatrics', 'General'];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 6),
          child: Text('Specialization',
              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: specs.map((s) {
            final selected = _specialization == s;
            return GestureDetector(
              onTap: () => setState(() => _specialization = s),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: selected ? AppColors.accent : AppColors.surface,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                      color: selected ? AppColors.accent : AppColors.border),
                ),
                child: Text(s,
                    style: TextStyle(
                        color: selected ? Colors.white : AppColors.textPrimary,
                        fontWeight: FontWeight.w600,
                        fontSize: 12.5)),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }
}
