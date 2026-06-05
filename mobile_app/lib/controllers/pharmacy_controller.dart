import 'package:flutter/foundation.dart';

import '../models/pharmacy.dart';

class PharmacyController extends ChangeNotifier {
  final List<Medicine> _medicines = [];
  final List<PharmacyOrder> _orders = [];
  final List<CartItem> _cart = [];
  bool _loading = false;
  String _query = '';
  String? _category;

  bool get loading => _loading;
  List<PharmacyOrder> get orders => _orders;
  List<CartItem> get cart => _cart;

  num get cartTotal => _cart.fold(0, (sum, item) => sum + item.total);
  int get cartCount => _cart.fold(0, (sum, item) => sum + item.quantity);

  List<Medicine> get medicines {
    var list = _medicines;
    if (_query.isNotEmpty) {
      list = list
          .where((m) => m.name.toLowerCase().contains(_query.toLowerCase()))
          .toList();
    }
    if (_category != null && _category != 'All') {
      list = list.where((m) => m.category == _category).toList();
    }
    return list;
  }

  List<String> get categories {
    final set = {'All', ..._medicines.map((m) => m.category ?? 'Other')};
    return set.toList();
  }

  String? get selectedCategory => _category;

  void setQuery(String q) {
    _query = q;
    notifyListeners();
  }

  void setCategory(String? cat) {
    _category = cat;
    notifyListeners();
  }

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration ===
    await Future.delayed(const Duration(milliseconds: 500));
    if (_medicines.isEmpty) _medicines.addAll(_mockMedicines());
    if (_orders.isEmpty) _orders.addAll(_mockOrders());
    _loading = false;
    notifyListeners();
  }

  void addToCart(Medicine m, [int qty = 1]) {
    final idx = _cart.indexWhere((c) => c.medicine.id == m.id);
    if (idx >= 0) {
      _cart[idx].quantity += qty;
    } else {
      _cart.add(CartItem(medicine: m, quantity: qty));
    }
    notifyListeners();
  }

  void removeFromCart(int medicineId) {
    _cart.removeWhere((c) => c.medicine.id == medicineId);
    notifyListeners();
  }

  void updateQty(int medicineId, int qty) {
    final idx = _cart.indexWhere((c) => c.medicine.id == medicineId);
    if (idx >= 0) {
      if (qty <= 0) {
        _cart.removeAt(idx);
      } else {
        _cart[idx].quantity = qty;
      }
      notifyListeners();
    }
  }

  void clearCart() {
    _cart.clear();
    notifyListeners();
  }

  Future<bool> placeOrder({String? address}) async {
    if (_cart.isEmpty) return false;
    // === Backend integration: POST /api/v1/pharmacy/order ===
    await Future.delayed(const Duration(milliseconds: 600));
    _orders.insert(
      0,
      PharmacyOrder(
        id: DateTime.now().millisecondsSinceEpoch,
        patientId: 1,
        orderCode: 'PH${DateTime.now().millisecondsSinceEpoch}',
        status: 'pending',
        paymentStatus: 'unpaid',
        total: cartTotal,
        deliveryAddress: address,
        createdAt: DateTime.now(),
        items: _cart
            .map((c) => PharmacyOrderItem(
                id: c.medicine.id,
                medicineId: c.medicine.id,
                name: c.medicine.name,
                quantity: c.quantity,
                price: c.medicine.price))
            .toList(),
      ),
    );
    clearCart();
    return true;
  }

  List<Medicine> _mockMedicines() => [
        Medicine(id: 1, name: 'Paracetamol 500mg', category: 'Pain Relief', manufacturer: 'Cipla', price: 25, stock: 240),
        Medicine(id: 2, name: 'Crocin Advance', category: 'Pain Relief', manufacturer: 'GSK', price: 35, stock: 180),
        Medicine(id: 3, name: 'Cetirizine 10mg', category: 'Allergy', manufacturer: 'Sun Pharma', price: 22, stock: 320),
        Medicine(id: 4, name: 'Amoxicillin 500mg', category: 'Antibiotic', manufacturer: 'Cipla', price: 95, stock: 120, requiresPrescription: true),
        Medicine(id: 5, name: 'Azithromycin 500mg', category: 'Antibiotic', manufacturer: 'Sun Pharma', price: 145, stock: 90, requiresPrescription: true),
        Medicine(id: 6, name: 'Omeprazole 20mg', category: 'Gastro', manufacturer: 'Dr Reddys', price: 65, stock: 200),
        Medicine(id: 7, name: 'Pantoprazole 40mg', category: 'Gastro', manufacturer: 'Lupin', price: 80, stock: 150),
        Medicine(id: 8, name: 'Metformin 500mg', category: 'Diabetes', manufacturer: 'USV', price: 45, stock: 180, requiresPrescription: true),
        Medicine(id: 9, name: 'Atorvastatin 10mg', category: 'Cardiac', manufacturer: 'Cipla', price: 75, stock: 140, requiresPrescription: true),
        Medicine(id: 10, name: 'Amlodipine 5mg', category: 'Cardiac', manufacturer: 'Cipla', price: 55, stock: 175, requiresPrescription: true),
        Medicine(id: 11, name: 'Vitamin D3 60K', category: 'Vitamins', manufacturer: 'Mankind', price: 40, stock: 220),
        Medicine(id: 12, name: 'B-Complex Forte', category: 'Vitamins', manufacturer: 'Pfizer', price: 110, stock: 160),
        Medicine(id: 13, name: 'ORS Sachet', category: 'Hydration', manufacturer: 'WHO Formula', price: 18, stock: 400),
        Medicine(id: 14, name: 'Dolo 650', category: 'Pain Relief', manufacturer: 'Micro Labs', price: 32, stock: 280),
      ];

  List<PharmacyOrder> _mockOrders() => [
        PharmacyOrder(
          id: 401,
          patientId: 1,
          orderCode: 'PH1715000001',
          status: 'delivered',
          paymentStatus: 'paid',
          total: 220,
          deliveryAddress: 'A-101, Sun Apartments, Mumbai',
          createdAt: DateTime.now().subtract(const Duration(days: 5)),
          items: [
            PharmacyOrderItem(id: 1, medicineId: 1, name: 'Paracetamol 500mg', quantity: 2, price: 25),
            PharmacyOrderItem(id: 2, medicineId: 11, name: 'Vitamin D3 60K', quantity: 1, price: 40),
            PharmacyOrderItem(id: 3, medicineId: 13, name: 'ORS Sachet', quantity: 6, price: 18),
          ],
        ),
      ];
}
