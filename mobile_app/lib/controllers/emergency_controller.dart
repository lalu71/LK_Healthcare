import 'package:flutter/foundation.dart';

import '../models/emergency.dart';

class EmergencyController extends ChangeNotifier {
  final List<EmergencyRequest> _requests = [];
  bool _loading = false;

  bool get loading => _loading;
  List<EmergencyRequest> get requests => _requests;

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration: GET /api/v1/my/emergency-requests ===
    await Future.delayed(const Duration(milliseconds: 400));
    if (_requests.isEmpty) _requests.addAll(_mock());
    _loading = false;
    notifyListeners();
  }

  Future<bool> raise({
    required String contactName,
    required String phone,
    String? location,
    double? latitude,
    double? longitude,
    String? notes,
  }) async {
    // === Backend integration: POST /api/v1/emergency ===
    await Future.delayed(const Duration(milliseconds: 600));
    _requests.insert(
      0,
      EmergencyRequest(
        id: DateTime.now().millisecondsSinceEpoch,
        contactName: contactName,
        phone: phone,
        location: location,
        latitude: latitude,
        longitude: longitude,
        notes: notes,
        status: 'pending',
        createdAt: DateTime.now(),
      ),
    );
    notifyListeners();
    return true;
  }

  List<EmergencyRequest> _mock() => [
        EmergencyRequest(
          id: 1,
          contactName: 'Demo User',
          phone: '+91 98765 43210',
          location: 'MG Road, Bangalore',
          status: 'resolved',
          createdAt: DateTime.now().subtract(const Duration(days: 12)),
          notes: 'Severe chest pain — ambulance dispatched',
        ),
      ];
}
