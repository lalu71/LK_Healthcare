import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:fluttertoast/fluttertoast.dart';

import '../config/app_colors.dart';

class AppHelpers {
  AppHelpers._();

  static String formatDate(DateTime date) =>
      DateFormat('dd MMM yyyy').format(date);

  static String formatDateTime(DateTime date) =>
      DateFormat('dd MMM yyyy, hh:mm a').format(date);

  static String formatTime(DateTime date) =>
      DateFormat('hh:mm a').format(date);

  static String currency(num amount) =>
      NumberFormat.currency(locale: 'en_IN', symbol: '₹', decimalDigits: 0)
          .format(amount);

  static String relative(DateTime date) {
    final now = DateTime.now();
    final diff = now.difference(date);
    if (diff.inSeconds < 60) return 'just now';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
    if (diff.inHours < 24) return '${diff.inHours}h ago';
    if (diff.inDays < 7) return '${diff.inDays}d ago';
    return formatDate(date);
  }

  static void toast(String message, {bool isError = false}) {
    Fluttertoast.showToast(
      msg: message,
      backgroundColor: isError ? AppColors.danger : AppColors.textPrimary,
      textColor: Colors.white,
      gravity: ToastGravity.BOTTOM,
    );
  }

  static void snack(BuildContext context, String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? AppColors.danger : AppColors.textPrimary,
      ),
    );
  }

  static Color statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return AppColors.warning;
      case 'confirmed':
      case 'paid':
      case 'completed':
      case 'delivered':
        return AppColors.success;
      case 'cancelled':
      case 'rejected':
      case 'unpaid':
        return AppColors.danger;
      default:
        return AppColors.info;
    }
  }
}
