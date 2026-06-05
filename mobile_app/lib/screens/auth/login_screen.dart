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

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();
  String _role = UserRole.patient;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthController>();
    final ok = await auth.login(_email.text.trim(), _password.text, role: _role);
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, 'Welcome back!');
      Navigator.pushNamedAndRemoveUntil(
          context, _dashboardRouteFor(_role), (_) => false);
    } else {
      AppHelpers.snack(context, auth.error ?? 'Login failed', isError: true);
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
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              child: ConstrainedBox(
                constraints: BoxConstraints(minHeight: constraints.maxHeight),
                child: IntrinsicHeight(
                  child: Column(
                    children: [
                      _header(),
                      Expanded(
                        child: Container(
                          width: double.infinity,
                          padding: const EdgeInsets.fromLTRB(24, 28, 24, 24),
                          decoration: const BoxDecoration(
                            color: AppColors.bg,
                            borderRadius: BorderRadius.only(
                              topLeft: Radius.circular(32),
                              topRight: Radius.circular(32),
                            ),
                          ),
                          child: Form(
                            key: _formKey,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Welcome Back',
                                  style: TextStyle(
                                      fontSize: 24,
                                      fontWeight: FontWeight.w800,
                                      color: AppColors.textPrimary),
                                ),
                                const SizedBox(height: 4),
                                const Text(
                                  'Login to continue your healthcare journey',
                                  style: TextStyle(
                                      fontSize: 14, color: AppColors.textSecondary),
                                ),
                                const SizedBox(height: 28),
                                _rolePicker(),
                                const SizedBox(height: 18),
                                CustomTextField(
                                  label: 'Email',
                                  hint: 'you@example.com',
                                  controller: _email,
                                  prefixIcon: Icons.email_outlined,
                                  keyboardType: TextInputType.emailAddress,
                                  validator: Validators.email,
                                ),
                                const SizedBox(height: 16),
                                CustomTextField(
                                  label: 'Password',
                                  hint: 'Enter password',
                                  controller: _password,
                                  prefixIcon: Icons.lock_outline_rounded,
                                  obscure: true,
                                  validator: Validators.password,
                                ),
                                const SizedBox(height: 8),
                                Align(
                                  alignment: Alignment.centerRight,
                                  child: TextButton(
                                    onPressed: () => Navigator.pushNamed(
                                        context, AppRoutes.forgotPassword),
                                    child: const Text('Forgot password?'),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                PrimaryButton(
                                  label: 'Login',
                                  loading: loading,
                                  icon: Icons.login_rounded,
                                  onPressed: _submit,
                                ),
                                const SizedBox(height: 16),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Text("Don't have an account? ",
                                        style: TextStyle(color: AppColors.textSecondary)),
                                    GestureDetector(
                                      onTap: () => Navigator.pushNamed(
                                          context, AppRoutes.register),
                                      child: const Text('Sign Up',
                                          style: TextStyle(
                                              color: AppColors.primary,
                                              fontWeight: FontWeight.w700)),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 18),
                                Row(
                                  children: const [
                                    Expanded(child: Divider()),
                                    Padding(
                                      padding: EdgeInsets.symmetric(horizontal: 12),
                                      child: Text('Quick Access',
                                          style: TextStyle(
                                              color: AppColors.textMuted,
                                              fontSize: 12)),
                                    ),
                                    Expanded(child: Divider()),
                                  ],
                                ),
                                const SizedBox(height: 14),
                                Row(
                                  children: [
                                    Expanded(
                                      child: OutlinedButton.icon(
                                        onPressed: () => Navigator.pushNamed(
                                            context, AppRoutes.home),
                                        icon: const Icon(Icons.home_outlined, size: 18),
                                        label: const Text('Browse'),
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: OutlinedButton.icon(
                                        onPressed: () => Navigator.pushNamed(
                                            context, AppRoutes.emergency),
                                        icon: const Icon(Icons.local_hospital_rounded,
                                            size: 18, color: AppColors.danger),
                                        label: const Text('Emergency',
                                            style: TextStyle(color: AppColors.danger)),
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _header() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(24, 40, 24, 36),
      decoration: const BoxDecoration(gradient: AppColors.primaryGradient),
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.18),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Colors.white.withOpacity(0.4), width: 1.5),
            ),
            child: const Icon(Icons.medical_services_rounded,
                color: Colors.white, size: 44),
          ),
          const SizedBox(height: 12),
          const Text(AppStrings.appName,
              style: TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  letterSpacing: 1.0)),
          const SizedBox(height: 4),
          Text(AppStrings.tagline,
              style: TextStyle(
                  color: Colors.white.withOpacity(0.85), fontSize: 13)),
        ],
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
                      Icon(r.$3, color: r.$4, size: 26),
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
