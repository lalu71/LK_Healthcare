import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';

class PatientProfileScreen extends StatefulWidget {
  const PatientProfileScreen({super.key});

  @override
  State<PatientProfileScreen> createState() => _PatientProfileScreenState();
}

class _PatientProfileScreenState extends State<PatientProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _dob = TextEditingController();
  final _address = TextEditingController();
  final _aadhaar = TextEditingController();
  String _gender = 'Male';
  String _bloodGroup = 'O+';
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthController>().user;
    _name.text = user?.name ?? '';
    _email.text = user?.email ?? '';
    _phone.text = user?.phone ?? '';
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _phone.dispose();
    _dob.dispose();
    _address.dispose();
    _aadhaar.dispose();
    super.dispose();
  }

  Future<void> _pickDob() async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime(2000),
      firstDate: DateTime(1920),
      lastDate: DateTime.now(),
    );
    if (date != null) {
      _dob.text = AppHelpers.formatDate(date);
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    // === Backend integration: PUT /api/v1/patient/me ===
    await Future.delayed(const Duration(milliseconds: 700));
    setState(() => _saving = false);
    if (!mounted) return;
    AppHelpers.snack(context, 'Profile saved successfully');
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
              _sectionTitle('Personal Information'),
              CustomTextField(
                label: 'Full Name',
                controller: _name,
                prefixIcon: Icons.person_outline,
              ),
              const SizedBox(height: 14),
              CustomTextField(
                label: 'Email',
                controller: _email,
                prefixIcon: Icons.email_outlined,
                readOnly: true,
              ),
              const SizedBox(height: 14),
              CustomTextField(
                label: 'Phone',
                controller: _phone,
                prefixIcon: Icons.phone_outlined,
                keyboardType: TextInputType.phone,
              ),
              const SizedBox(height: 14),
              CustomTextField(
                label: 'Date of Birth',
                controller: _dob,
                prefixIcon: Icons.cake_outlined,
                readOnly: true,
                suffixIcon: Icons.calendar_today_rounded,
                onTap: _pickDob,
              ),
              const SizedBox(height: 14),
              _genderPicker(),
              const SizedBox(height: 14),
              _bloodGroupPicker(),
              const SizedBox(height: 22),
              _sectionTitle('Address & Identity'),
              CustomTextField(
                label: 'Address',
                controller: _address,
                prefixIcon: Icons.location_on_outlined,
                maxLines: 2,
              ),
              const SizedBox(height: 14),
              CustomTextField(
                label: 'Aadhaar Number',
                controller: _aadhaar,
                prefixIcon: Icons.badge_outlined,
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 28),
              PrimaryButton(
                label: 'Save Changes',
                loading: _saving,
                icon: Icons.check_rounded,
                onPressed: _save,
              ),
              const SizedBox(height: 12),
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
          Stack(
            children: [
              Container(
                width: 100,
                height: 100,
                decoration: BoxDecoration(
                  gradient: AppColors.primaryGradient,
                  borderRadius: BorderRadius.circular(28),
                ),
                child: const Icon(Icons.person_rounded,
                    color: Colors.white, size: 56),
              ),
              Positioned(
                bottom: 0,
                right: 0,
                child: Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
                  ),
                  child: const Icon(Icons.camera_alt_rounded,
                      color: Colors.white, size: 16),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(_name.text.isEmpty ? 'Your Name' : _name.text,
              style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary)),
          Text(_email.text,
              style: const TextStyle(
                  fontSize: 12.5, color: AppColors.textSecondary)),
        ],
      ),
    );
  }

  Widget _sectionTitle(String title) => Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: Text(title,
            style: const TextStyle(
                fontSize: 14.5,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary)),
      );

  Widget _genderPicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 6),
          child: Text('Gender',
              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ),
        Row(
          children: ['Male', 'Female', 'Other'].map((g) {
            final selected = _gender == g;
            return Expanded(
              child: GestureDetector(
                onTap: () => setState(() => _gender = g),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  margin: EdgeInsets.only(right: g != 'Other' ? 8 : 0),
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  decoration: BoxDecoration(
                    color: selected
                        ? AppColors.primary.withOpacity(0.12)
                        : AppColors.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                        color: selected ? AppColors.primary : AppColors.border),
                  ),
                  child: Center(
                    child: Text(g,
                        style: TextStyle(
                          color: selected
                              ? AppColors.primary
                              : AppColors.textSecondary,
                          fontWeight: FontWeight.w600,
                          fontSize: 13,
                        )),
                  ),
                ),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }

  Widget _bloodGroupPicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 6),
          child: Text('Blood Group',
              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: BloodGroup.all.map((g) {
            final selected = _bloodGroup == g;
            return GestureDetector(
              onTap: () => setState(() => _bloodGroup = g),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: selected ? AppColors.danger : AppColors.surface,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                      color: selected ? AppColors.danger : AppColors.border),
                ),
                child: Text(g,
                    style: TextStyle(
                        color:
                            selected ? Colors.white : AppColors.textPrimary,
                        fontWeight: FontWeight.w600,
                        fontSize: 13)),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }
}
