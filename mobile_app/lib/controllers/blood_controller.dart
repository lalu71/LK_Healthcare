import 'package:flutter/foundation.dart';

import '../models/blood.dart';

class BloodController extends ChangeNotifier {
  final List<BloodInventory> _inventory = [];
  final List<BloodDonor> _donors = [];
  final List<BloodRequest> _requests = [];
  bool _loading = false;

  bool get loading => _loading;
  List<BloodInventory> get inventory => _inventory;
  List<BloodDonor> get donors => _donors;
  List<BloodRequest> get requests => _requests;

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration ===
    await Future.delayed(const Duration(milliseconds: 500));
    if (_inventory.isEmpty) _inventory.addAll(_mockInventory());
    if (_donors.isEmpty) _donors.addAll(_mockDonors());
    if (_requests.isEmpty) _requests.addAll(_mockRequests());
    _loading = false;
    notifyListeners();
  }

  Future<bool> request({
    required String patientName,
    required String bloodGroup,
    required int units,
    DateTime? neededBy,
    String? hospital,
    String? contact,
  }) async {
    // === Backend integration ===
    await Future.delayed(const Duration(milliseconds: 500));
    _requests.insert(
      0,
      BloodRequest(
        id: DateTime.now().millisecondsSinceEpoch,
        patientName: patientName,
        bloodGroup: bloodGroup,
        units: units,
        neededBy: neededBy,
        status: 'pending',
        hospital: hospital,
        contact: contact,
      ),
    );
    notifyListeners();
    return true;
  }

  Future<bool> registerDonor({
    required String name,
    required String bloodGroup,
    required String phone,
    String? city,
  }) async {
    // === Backend integration ===
    await Future.delayed(const Duration(milliseconds: 500));
    _donors.insert(
      0,
      BloodDonor(
        id: DateTime.now().millisecondsSinceEpoch,
        name: name,
        bloodGroup: bloodGroup,
        phone: phone,
        isAvailable: true,
        city: city,
      ),
    );
    notifyListeners();
    return true;
  }

  List<BloodInventory> _mockInventory() => [
        BloodInventory(id: 1, bloodGroup: 'A+', units: 18),
        BloodInventory(id: 2, bloodGroup: 'A-', units: 6),
        BloodInventory(id: 3, bloodGroup: 'B+', units: 22),
        BloodInventory(id: 4, bloodGroup: 'B-', units: 4),
        BloodInventory(id: 5, bloodGroup: 'AB+', units: 9),
        BloodInventory(id: 6, bloodGroup: 'AB-', units: 2),
        BloodInventory(id: 7, bloodGroup: 'O+', units: 27),
        BloodInventory(id: 8, bloodGroup: 'O-', units: 11),
      ];

  List<BloodDonor> _mockDonors() => [
        BloodDonor(id: 1, name: 'Rahul Verma', bloodGroup: 'O+', phone: '+91 98765 43210', city: 'Mumbai'),
        BloodDonor(id: 2, name: 'Priya Singh', bloodGroup: 'A+', phone: '+91 98765 11122', city: 'Delhi'),
        BloodDonor(id: 3, name: 'Anil Kumar', bloodGroup: 'B+', phone: '+91 99887 76655', city: 'Bangalore'),
        BloodDonor(id: 4, name: 'Sneha Roy', bloodGroup: 'AB+', phone: '+91 97777 88899', city: 'Pune'),
      ];

  List<BloodRequest> _mockRequests() => [
        BloodRequest(
          id: 1,
          patientName: 'Mr. Sharma',
          bloodGroup: 'O+',
          units: 2,
          neededBy: DateTime.now().add(const Duration(days: 2)),
          status: 'pending',
          hospital: 'Apollo Hospital',
          contact: '+91 98765 00011',
        ),
      ];
}
