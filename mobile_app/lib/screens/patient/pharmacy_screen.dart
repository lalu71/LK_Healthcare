import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/pharmacy_controller.dart';
import '../../models/pharmacy.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/status_badge.dart';

class PharmacyScreen extends StatefulWidget {
  const PharmacyScreen({super.key});

  @override
  State<PharmacyScreen> createState() => _PharmacyScreenState();
}

class _PharmacyScreenState extends State<PharmacyScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;
  final _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PharmacyController>().fetch();
    });
  }

  @override
  void dispose() {
    _tabs.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<PharmacyController>();
    return RoleScaffold(
      title: 'Pharmacy',
      actions: [
        Stack(
          alignment: Alignment.center,
          children: [
            IconButton(
              icon: const Icon(Icons.shopping_cart_outlined),
              onPressed: () => _showCart(ctrl),
            ),
            if (ctrl.cartCount > 0)
              Positioned(
                right: 6,
                top: 6,
                child: Container(
                  padding: const EdgeInsets.all(4),
                  constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                  decoration: const BoxDecoration(
                    color: AppColors.accent,
                    shape: BoxShape.circle,
                  ),
                  child: Text('${ctrl.cartCount}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.bold)),
                ),
              ),
          ],
        ),
      ],
      body: Column(
        children: [
          Container(
            color: AppColors.surface,
            child: TabBar(
              controller: _tabs,
              labelColor: AppColors.primary,
              unselectedLabelColor: AppColors.textSecondary,
              indicatorColor: AppColors.primary,
              indicatorWeight: 3,
              labelStyle: const TextStyle(fontWeight: FontWeight.w700),
              tabs: const [
                Tab(text: 'Medicines'),
                Tab(text: 'My Orders'),
              ],
            ),
          ),
          Expanded(
            child: ctrl.loading
                ? const LoadingView()
                : TabBarView(
                    controller: _tabs,
                    children: [_medicines(ctrl), _orders(ctrl)],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _medicines(PharmacyController ctrl) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
          child: TextField(
            controller: _searchCtrl,
            onChanged: (v) => ctrl.setQuery(v),
            decoration: const InputDecoration(
              hintText: 'Search medicines...',
              prefixIcon: Icon(Icons.search_rounded),
            ),
          ),
        ),
        SizedBox(
          height: 44,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12),
            children: ctrl.categories.map((cat) {
              final selected =
                  ctrl.selectedCategory == cat || (ctrl.selectedCategory == null && cat == 'All');
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
                child: GestureDetector(
                  onTap: () => ctrl.setCategory(cat == 'All' ? null : cat),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 180),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                    decoration: BoxDecoration(
                      color: selected ? AppColors.accent : AppColors.surface,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(
                          color: selected ? AppColors.accent : AppColors.border),
                    ),
                    child: Text(cat,
                        style: TextStyle(
                            color: selected ? Colors.white : AppColors.textSecondary,
                            fontWeight: FontWeight.w600,
                            fontSize: 12.5)),
                  ),
                ),
              );
            }).toList(),
          ),
        ),
        Expanded(
          child: ctrl.medicines.isEmpty
              ? const EmptyState(
                  icon: Icons.search_off_rounded, title: 'No medicines found')
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: ctrl.medicines.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (_, i) => _MedCard(medicine: ctrl.medicines[i]),
                ),
        ),
        if (ctrl.cartCount > 0)
          Container(
            padding: const EdgeInsets.all(14),
            decoration: const BoxDecoration(
              color: AppColors.surface,
              border: Border(top: BorderSide(color: AppColors.border)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('${ctrl.cartCount} items',
                          style: const TextStyle(
                              color: AppColors.textMuted, fontSize: 12)),
                      Text(AppHelpers.currency(ctrl.cartTotal),
                          style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w800,
                              color: AppColors.textPrimary)),
                    ],
                  ),
                ),
                SizedBox(
                  width: 160,
                  child: PrimaryButton(
                    label: 'View Cart',
                    icon: Icons.shopping_cart_rounded,
                    onPressed: () => _showCart(ctrl),
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }

  Widget _orders(PharmacyController ctrl) {
    if (ctrl.orders.isEmpty) {
      return const EmptyState(
        icon: Icons.shopping_bag_outlined,
        title: 'No orders yet',
        message: 'Your medicine orders will appear here',
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: ctrl.orders.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (_, i) {
        final o = ctrl.orders[i];
        return Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(o.orderCode,
                        style: const TextStyle(
                            fontSize: 13.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary)),
                  ),
                  StatusBadge(status: o.status),
                ],
              ),
              const SizedBox(height: 8),
              Text('${o.items.length} items',
                  style: const TextStyle(
                      fontSize: 12, color: AppColors.textSecondary)),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(AppHelpers.formatDate(o.createdAt),
                      style: const TextStyle(
                          fontSize: 11.5, color: AppColors.textMuted)),
                  Text(AppHelpers.currency(o.total),
                      style: const TextStyle(
                          color: AppColors.accent,
                          fontSize: 16,
                          fontWeight: FontWeight.w800)),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  void _showCart(PharmacyController ctrl) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _CartSheet(),
    );
  }
}

class _MedCard extends StatelessWidget {
  final Medicine medicine;
  const _MedCard({required this.medicine});

  @override
  Widget build(BuildContext context) {
    final ctrl = context.read<PharmacyController>();
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: AppColors.accent.withOpacity(0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.medication_rounded,
                color: AppColors.accent, size: 28),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(medicine.name,
                    style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary)),
                if (medicine.manufacturer != null) ...[
                  const SizedBox(height: 2),
                  Text(medicine.manufacturer!,
                      style: const TextStyle(
                          fontSize: 11.5, color: AppColors.textMuted)),
                ],
                const SizedBox(height: 6),
                Row(
                  children: [
                    Text(AppHelpers.currency(medicine.price),
                        style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: AppColors.accent)),
                    if (medicine.requiresPrescription) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppColors.danger.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: const Text('Rx',
                            style: TextStyle(
                                color: AppColors.danger,
                                fontSize: 10,
                                fontWeight: FontWeight.w700)),
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),
          Container(
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(12),
            ),
            child: IconButton(
              icon: const Icon(Icons.add_shopping_cart_rounded,
                  color: Colors.white, size: 20),
              onPressed: () {
                ctrl.addToCart(medicine);
                AppHelpers.snack(context, '${medicine.name} added to cart');
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _CartSheet extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<PharmacyController>();
    return DraggableScrollableSheet(
      initialChildSize: 0.6,
      maxChildSize: 0.92,
      minChildSize: 0.4,
      builder: (_, scrollCtrl) => Container(
        decoration: const BoxDecoration(
          color: AppColors.bg,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          children: [
            Container(
              margin: const EdgeInsets.only(top: 8),
              width: 44,
              height: 4,
              decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  const Icon(Icons.shopping_cart_rounded, color: AppColors.primary),
                  const SizedBox(width: 8),
                  const Text('Your Cart',
                      style: TextStyle(
                          fontSize: 18, fontWeight: FontWeight.w800)),
                  const Spacer(),
                  if (ctrl.cart.isNotEmpty)
                    TextButton(
                      onPressed: () => ctrl.clearCart(),
                      style: TextButton.styleFrom(foregroundColor: AppColors.danger),
                      child: const Text('Clear'),
                    ),
                ],
              ),
            ),
            Expanded(
              child: ctrl.cart.isEmpty
                  ? const EmptyState(
                      icon: Icons.shopping_cart_outlined,
                      title: 'Your cart is empty',
                    )
                  : ListView.separated(
                      controller: scrollCtrl,
                      padding: const EdgeInsets.all(16),
                      itemCount: ctrl.cart.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 10),
                      itemBuilder: (_, i) {
                        final it = ctrl.cart[i];
                        return Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppColors.surface,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(it.medicine.name,
                                        style: const TextStyle(
                                            fontWeight: FontWeight.w700,
                                            fontSize: 14)),
                                    Text(AppHelpers.currency(it.medicine.price),
                                        style: const TextStyle(
                                            color: AppColors.accent,
                                            fontWeight: FontWeight.w700)),
                                  ],
                                ),
                              ),
                              _qty(context, ctrl, it.medicine.id, it.quantity),
                            ],
                          ),
                        );
                      },
                    ),
            ),
            if (ctrl.cart.isNotEmpty)
              Container(
                padding: const EdgeInsets.all(16),
                decoration: const BoxDecoration(
                  color: AppColors.surface,
                  border: Border(top: BorderSide(color: AppColors.border)),
                ),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Total',
                            style: TextStyle(
                                fontSize: 14, color: AppColors.textSecondary)),
                        Text(AppHelpers.currency(ctrl.cartTotal),
                            style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.w800,
                                color: AppColors.textPrimary)),
                      ],
                    ),
                    const SizedBox(height: 12),
                    PrimaryButton(
                      label: 'Checkout',
                      icon: Icons.lock_rounded,
                      onPressed: () async {
                        await ctrl.placeOrder(address: 'Default Address');
                        if (context.mounted) {
                          Navigator.pop(context);
                          AppHelpers.snack(context, 'Order placed!');
                          Navigator.pushNamed(context, AppRoutes.payment,
                              arguments: {
                                'title': 'Pharmacy Order',
                                'amount': ctrl.cartTotal,
                              });
                        }
                      },
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _qty(BuildContext context, PharmacyController ctrl, int id, int qty) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surfaceAlt,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        children: [
          IconButton(
            onPressed: () => ctrl.updateQty(id, qty - 1),
            icon: const Icon(Icons.remove_rounded, size: 18),
            constraints: const BoxConstraints(minWidth: 36, minHeight: 36),
            padding: EdgeInsets.zero,
          ),
          Text('$qty',
              style: const TextStyle(
                  fontWeight: FontWeight.w700, fontSize: 14)),
          IconButton(
            onPressed: () => ctrl.updateQty(id, qty + 1),
            icon: const Icon(Icons.add_rounded, size: 18),
            constraints: const BoxConstraints(minWidth: 36, minHeight: 36),
            padding: EdgeInsets.zero,
          ),
        ],
      ),
    );
  }
}
