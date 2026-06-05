class Medicine {
  final int id;
  final String name;
  final String? category;
  final String? manufacturer;
  final num price;
  final int stock;
  final bool requiresPrescription;
  final String? description;
  final String? image;

  Medicine({
    required this.id,
    required this.name,
    this.category,
    this.manufacturer,
    required this.price,
    this.stock = 0,
    this.requiresPrescription = false,
    this.description,
    this.image,
  });

  factory Medicine.fromJson(Map<String, dynamic> json) => Medicine(
        id: json['id'] as int,
        name: json['name'] as String,
        category: json['category'] as String?,
        manufacturer: json['manufacturer'] as String?,
        price: json['price'] as num? ?? 0,
        stock: json['stock'] as int? ?? 0,
        requiresPrescription: (json['requires_prescription'] as bool?) ?? false,
        description: json['description'] as String?,
        image: json['image'] as String?,
      );
}

class PharmacyOrder {
  final int id;
  final int patientId;
  final String orderCode;
  final String status;
  final String paymentStatus;
  final num total;
  final String? deliveryAddress;
  final DateTime createdAt;
  final List<PharmacyOrderItem> items;

  PharmacyOrder({
    required this.id,
    required this.patientId,
    required this.orderCode,
    this.status = 'pending',
    this.paymentStatus = 'unpaid',
    required this.total,
    this.deliveryAddress,
    required this.createdAt,
    this.items = const [],
  });

  factory PharmacyOrder.fromJson(Map<String, dynamic> json) => PharmacyOrder(
        id: json['id'] as int,
        patientId: json['patient_id'] as int,
        orderCode: json['order_code'] as String? ?? '',
        status: json['status'] as String? ?? 'pending',
        paymentStatus: json['payment_status'] as String? ?? 'unpaid',
        total: json['total'] as num? ?? 0,
        deliveryAddress: json['delivery_address'] as String?,
        createdAt: DateTime.parse(json['created_at'] as String),
        items: (json['items'] as List?)
                ?.map((e) => PharmacyOrderItem.fromJson(e))
                .toList() ??
            const [],
      );
}

class PharmacyOrderItem {
  final int id;
  final int medicineId;
  final String name;
  final int quantity;
  final num price;

  PharmacyOrderItem({
    required this.id,
    required this.medicineId,
    required this.name,
    required this.quantity,
    required this.price,
  });

  factory PharmacyOrderItem.fromJson(Map<String, dynamic> json) => PharmacyOrderItem(
        id: json['id'] as int? ?? 0,
        medicineId: json['medicine_id'] as int? ?? 0,
        name: json['name'] as String? ?? json['medicine']?['name'] ?? '',
        quantity: json['quantity'] as int? ?? 1,
        price: json['price'] as num? ?? 0,
      );
}

class CartItem {
  final Medicine medicine;
  int quantity;

  CartItem({required this.medicine, this.quantity = 1});

  num get total => medicine.price * quantity;
}
