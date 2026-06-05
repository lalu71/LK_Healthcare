import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../utils/helpers.dart';
import '../../utils/validators.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/primary_button.dart';

class ContactScreen extends StatefulWidget {
  const ContactScreen({super.key});

  @override
  State<ContactScreen> createState() => _ContactScreenState();
}

class _ContactScreenState extends State<ContactScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _subject = TextEditingController();
  final _message = TextEditingController();
  bool _loading = false;

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _subject.dispose();
    _message.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    // === Backend integration point ===
    await Future.delayed(const Duration(milliseconds: 700));
    setState(() => _loading = false);
    if (!mounted) return;
    AppHelpers.snack(context, 'Message sent! We will get back soon.');
    _name.clear();
    _email.clear();
    _subject.clear();
    _message.clear();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Contact Us')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _contactCards(),
            const SizedBox(height: 24),
            const Text('Send us a message',
                style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary)),
            const SizedBox(height: 14),
            Form(
              key: _formKey,
              child: Column(
                children: [
                  CustomTextField(
                    label: 'Name',
                    hint: 'Your full name',
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
                    label: 'Subject',
                    hint: 'How can we help?',
                    controller: _subject,
                    prefixIcon: Icons.subject_rounded,
                    validator: (v) => Validators.required(v, 'Subject'),
                  ),
                  const SizedBox(height: 14),
                  CustomTextField(
                    label: 'Message',
                    hint: 'Type your message...',
                    controller: _message,
                    maxLines: 5,
                    validator: (v) => Validators.required(v, 'Message'),
                  ),
                  const SizedBox(height: 22),
                  PrimaryButton(
                    label: 'Send Message',
                    loading: _loading,
                    icon: Icons.send_rounded,
                    onPressed: _submit,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _contactCards() {
    final items = [
      ('Phone', '+91 1800-123-4567', Icons.phone_rounded, AppColors.primary),
      ('Email', 'support@lkhealthcare.in', Icons.email_rounded, AppColors.accent),
      ('Address', 'New Delhi, India', Icons.location_on_rounded, AppColors.danger),
    ];
    return Column(
      children: items
          .map((e) => Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: e.$4.withOpacity(0.12),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(e.$3, color: e.$4, size: 22),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(e.$1,
                              style: const TextStyle(
                                  fontSize: 11.5,
                                  color: AppColors.textMuted,
                                  fontWeight: FontWeight.w500)),
                          const SizedBox(height: 2),
                          Text(e.$2,
                              style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textPrimary)),
                        ],
                      ),
                    ),
                  ],
                ),
              ))
          .toList(),
    );
  }
}
