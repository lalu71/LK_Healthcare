import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';

class ReceiptScreen extends StatefulWidget {
  const ReceiptScreen({super.key});

  @override
  State<ReceiptScreen> createState() => _ReceiptScreenState();
}

class _ReceiptScreenState extends State<ReceiptScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;
  late final Animation<double> _scale;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 700));
    _scale = CurvedAnimation(parent: _ctrl, curve: Curves.elasticOut);
    _ctrl.forward();
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final args = ModalRoute.of(context)?.settings.arguments as Map? ?? {};
    final title = args['title'] as String? ?? 'Payment';
    final amount = args['amount'] as num? ?? 0;
    final method = args['method'] as String? ?? 'upi';
    final txnId = args['txnId'] as String? ?? 'TXN0000';

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Receipt')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            ScaleTransition(
              scale: _scale,
              child: Container(
                width: 110,
                height: 110,
                decoration: const BoxDecoration(
                  gradient: AppColors.successGradient,
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.check_rounded,
                    color: Colors.white, size: 70),
              ),
            ),
            const SizedBox(height: 20),
            const Text('Payment Successful',
                style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    color: AppColors.textPrimary)),
            const SizedBox(height: 4),
            Text(AppHelpers.currency(amount),
                style: const TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.w800,
                    color: AppColors.success)),
            const SizedBox(height: 24),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                children: [
                  _row('Transaction ID', txnId),
                  _row('Description', title),
                  _row('Method', method.toUpperCase()),
                  _row('Date', AppHelpers.formatDateTime(DateTime.now())),
                  const Divider(height: 24),
                  _row('Amount Paid', AppHelpers.currency(amount), bold: true),
                ],
              ),
            ),
            const SizedBox(height: 24),
            PrimaryButton(
              label: 'Download Receipt',
              icon: Icons.download_rounded,
              onPressed: () =>
                  AppHelpers.snack(context, 'Receipt downloaded'),
            ),
            const SizedBox(height: 10),
            OutlinedButton.icon(
              onPressed: () => Navigator.pushNamedAndRemoveUntil(
                  context, AppRoutes.patientDashboard, (_) => false),
              icon: const Icon(Icons.home_rounded),
              label: const Text('Back to Home'),
              style: OutlinedButton.styleFrom(
                  minimumSize: const Size.fromHeight(52)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(String label, String value, {bool bold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
              style: const TextStyle(
                  fontSize: 13, color: AppColors.textSecondary)),
          Text(value,
              style: TextStyle(
                  fontSize: bold ? 16 : 13,
                  fontWeight: bold ? FontWeight.w800 : FontWeight.w600,
                  color: AppColors.textPrimary)),
        ],
      ),
    );
  }
}
