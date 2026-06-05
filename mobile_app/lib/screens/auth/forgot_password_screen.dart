import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../controllers/auth_controller.dart';
import '../../utils/helpers.dart';
import '../../utils/validators.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  bool _sent = false;

  @override
  void dispose() {
    _email.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    final ok = await context.read<AuthController>().forgotPassword(_email.text.trim());
    if (!mounted) return;
    if (ok) {
      setState(() => _sent = true);
    } else {
      AppHelpers.snack(context, 'Could not send reset link', isError: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthController>().loading;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: _sent
              ? _success()
              : Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: 78,
                        height: 78,
                        decoration: BoxDecoration(
                          gradient: AppColors.primaryGradient,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Icon(Icons.lock_reset_rounded,
                            color: Colors.white, size: 40),
                      ),
                      const SizedBox(height: 20),
                      const Text('Forgot Password?',
                          style: TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.w800,
                              color: AppColors.textPrimary)),
                      const SizedBox(height: 8),
                      const Text(
                        "No worries! Enter your registered email below and we'll send you a link to reset your password.",
                        style: TextStyle(
                            fontSize: 14,
                            color: AppColors.textSecondary,
                            height: 1.5),
                      ),
                      const SizedBox(height: 28),
                      CustomTextField(
                        label: 'Email Address',
                        hint: 'you@example.com',
                        controller: _email,
                        prefixIcon: Icons.email_outlined,
                        keyboardType: TextInputType.emailAddress,
                        validator: Validators.email,
                      ),
                      const SizedBox(height: 20),
                      PrimaryButton(
                        label: 'Send Reset Link',
                        loading: loading,
                        icon: Icons.send_rounded,
                        onPressed: _submit,
                      ),
                      const SizedBox(height: 16),
                      Center(
                        child: TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Back to Login'),
                        ),
                      ),
                    ],
                  ),
                ),
        ),
      ),
    );
  }

  Widget _success() {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 96,
            height: 96,
            decoration: const BoxDecoration(
              gradient: AppColors.successGradient,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.check_rounded, color: Colors.white, size: 56),
          ),
          const SizedBox(height: 24),
          const Text('Check Your Email!',
              style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: AppColors.textPrimary)),
          const SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            child: Text(
              "We've sent a password reset link to ${_email.text.trim()}",
              style: const TextStyle(
                  color: AppColors.textSecondary, fontSize: 14, height: 1.5),
              textAlign: TextAlign.center,
            ),
          ),
          const SizedBox(height: 32),
          SizedBox(
            width: 200,
            child: PrimaryButton(
              label: 'Back to Login',
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ],
      ),
    );
  }
}
