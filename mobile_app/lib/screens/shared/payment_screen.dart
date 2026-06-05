import 'package:flutter/material.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../utils/helpers.dart';
import '../../widgets/primary_button.dart';

class PaymentScreen extends StatefulWidget {
  const PaymentScreen({super.key});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  String _method = 'upi';
  bool _processing = false;

  Future<void> _pay(num amount, String title) async {
    setState(() => _processing = true);
    // === Backend integration: Razorpay/UPI checkout ===
    await Future.delayed(const Duration(milliseconds: 1200));
    setState(() => _processing = false);
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, AppRoutes.receipt, arguments: {
      'title': title,
      'amount': amount,
      'method': _method,
      'txnId': 'TXN${DateTime.now().millisecondsSinceEpoch}',
    });
  }

  @override
  Widget build(BuildContext context) {
    final args =
        ModalRoute.of(context)?.settings.arguments as Map? ?? {};
    final title = args['title'] as String? ?? 'Payment';
    final amount = args['amount'] as num? ?? 0;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Payment')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: AppColors.primaryGradient,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                      style: const TextStyle(
                          color: Colors.white70, fontSize: 13)),
                  const SizedBox(height: 6),
                  Text(AppHelpers.currency(amount),
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 36,
                          fontWeight: FontWeight.w800)),
                  const SizedBox(height: 14),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.lock_rounded,
                            color: Colors.white, size: 14),
                        SizedBox(width: 4),
                        Text('Secured by Razorpay',
                            style: TextStyle(
                                color: Colors.white,
                                fontSize: 11,
                                fontWeight: FontWeight.w600)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            const Text('Choose Payment Method',
                style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            _methodTile('upi', 'UPI', 'Pay via UPI app',
                Icons.account_balance_rounded, AppColors.primary),
            _methodTile('card', 'Credit / Debit Card', 'Visa, Mastercard, Rupay',
                Icons.credit_card_rounded, AppColors.accent),
            _methodTile('netbanking', 'Net Banking',
                'All major banks supported', Icons.account_balance_outlined,
                AppColors.warning),
            _methodTile('wallet', 'Wallet', 'Paytm, PhonePe, GPay',
                Icons.account_balance_wallet_rounded, AppColors.adminColor),
            const SizedBox(height: 24),
            PrimaryButton(
              label: 'Pay ${AppHelpers.currency(amount)}',
              icon: Icons.lock_rounded,
              loading: _processing,
              onPressed: () => _pay(amount, title),
            ),
            const SizedBox(height: 12),
            const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.shield_rounded,
                    size: 14, color: AppColors.textMuted),
                SizedBox(width: 4),
                Text('100% safe and secure payment',
                    style: TextStyle(
                        fontSize: 12, color: AppColors.textMuted)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _methodTile(
      String code, String title, String subtitle, IconData icon, Color color) {
    final selected = _method == code;
    return GestureDetector(
      onTap: () => setState(() => _method = code),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: selected ? color.withOpacity(0.06) : AppColors.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: selected ? color : AppColors.border,
              width: selected ? 1.6 : 1),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: color.withOpacity(0.12),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                      style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 14)),
                  Text(subtitle,
                      style: const TextStyle(
                          fontSize: 12, color: AppColors.textMuted)),
                ],
              ),
            ),
            Icon(
                selected
                    ? Icons.radio_button_checked_rounded
                    : Icons.radio_button_unchecked_rounded,
                color: selected ? color : AppColors.textMuted),
          ],
        ),
      ),
    );
  }
}
