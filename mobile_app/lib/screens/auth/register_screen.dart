import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/validators.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  String _role = UserRole.patient;
  bool _acceptTerms = false;

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _phone.dispose();
    _password.dispose();
    _confirm.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (!_acceptTerms) {
      AppHelpers.snack(context, 'Please accept the Terms & Privacy Policy',
          isError: true);
      return;
    }
    final auth = context.read<AuthController>();
    final ok = await auth.register(
      name: _name.text.trim(),
      email: _email.text.trim(),
      phone: _phone.text.trim(),
      password: _password.text,
      role: _role,
    );
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, 'Account created successfully!');
      Navigator.pushNamedAndRemoveUntil(
          context, _dashboardRouteFor(_role), (_) => false);
    } else {
      AppHelpers.snack(context, auth.error ?? 'Registration failed', isError: true);
    }
  }

  String _dashboardRouteFor(String role) {
    return role == UserRole.doctor
        ? AppRoutes.doctorDashboard
        : AppRoutes.patientDashboard;
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthController>().loading;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text(''),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Create Account',
                    style: TextStyle(
                        fontSize: 26,
                        fontWeight: FontWeight.w800,
                        color: AppColors.textPrimary)),
                const SizedBox(height: 4),
                const Text('Join LK Healthcare and take charge of your wellness',
                    style: TextStyle(fontSize: 14, color: AppColors.textSecondary)),
                const SizedBox(height: 24),
                _rolePicker(),
                const SizedBox(height: 20),
                CustomTextField(
                  label: 'Full Name',
                  hint: 'John Doe',
                  controller: _name,
                  prefixIcon: Icons.person_outline,
                  validator: (v) => Validators.required(v, 'Name'),
                ),
                const SizedBox(height: 14),
                CustomTextField(
                  label: 'Email',
                  hint: 'you@example.com',
                  controller: _email,
                  prefixIcon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                  validator: Validators.email,
                ),
                const SizedBox(height: 14),
                CustomTextField(
                  label: 'Phone',
                  hint: '+91 9876543210',
                  controller: _phone,
                  prefixIcon: Icons.phone_outlined,
                  keyboardType: TextInputType.phone,
                  validator: Validators.phone,
                ),
                const SizedBox(height: 14),
                CustomTextField(
                  label: 'Password',
                  hint: 'Min 6 characters',
                  controller: _password,
                  prefixIcon: Icons.lock_outline_rounded,
                  obscure: true,
                  validator: Validators.password,
                ),
                const SizedBox(height: 14),
                CustomTextField(
                  label: 'Confirm Password',
                  hint: 'Re-enter password',
                  controller: _confirm,
                  prefixIcon: Icons.lock_outline_rounded,
                  obscure: true,
                  validator: (v) => Validators.confirmPassword(v, _password.text),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Checkbox(
                      value: _acceptTerms,
                      onChanged: (v) => setState(() => _acceptTerms = v ?? false),
                      activeColor: AppColors.primary,
                    ),
                    const Expanded(
                      child: Text(
                        'I agree to the Terms of Service and Privacy Policy',
                        style: TextStyle(fontSize: 12.5, color: AppColors.textSecondary),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                PrimaryButton(
                  label: 'Create Account',
                  loading: loading,
                  icon: Icons.person_add_alt_1_rounded,
                  onPressed: _submit,
                ),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text('Already have an account? ',
                        style: TextStyle(color: AppColors.textSecondary)),
                    GestureDetector(
                      onTap: () => Navigator.pushReplacementNamed(context, AppRoutes.login),
                      child: const Text('Login',
                          style: TextStyle(
                              color: AppColors.primary, fontWeight: FontWeight.w700)),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _rolePicker() {
    final roles = [
      ('Patient', UserRole.patient, Icons.person_rounded, AppColors.patientColor),
      ('Doctor', UserRole.doctor, Icons.medical_services_rounded, AppColors.doctorColor),
    ];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 8),
          child: Text('I am a',
              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13.5)),
        ),
        Row(
          children: roles.map((r) {
            final selected = _role == r.$2;
            return Expanded(
              child: GestureDetector(
                onTap: () => setState(() => _role = r.$2),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  margin: EdgeInsets.only(right: r == roles.last ? 0 : 10),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  decoration: BoxDecoration(
                    color: selected ? r.$4.withOpacity(0.10) : AppColors.surface,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                        color: selected ? r.$4 : AppColors.border,
                        width: selected ? 1.6 : 1),
                  ),
                  child: Column(
                    children: [
                      Icon(r.$3, color: r.$4, size: 28),
                      const SizedBox(height: 6),
                      Text(r.$1,
                          style: TextStyle(
                              fontWeight: FontWeight.w600,
                              fontSize: 13,
                              color: selected ? r.$4 : AppColors.textSecondary)),
                    ],
                  ),
                ),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }
}
