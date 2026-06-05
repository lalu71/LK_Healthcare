import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../controllers/blood_controller.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/validators.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/status_badge.dart';

class BloodBankScreen extends StatefulWidget {
  const BloodBankScreen({super.key});

  @override
  State<BloodBankScreen> createState() => _BloodBankScreenState();
}

class _BloodBankScreenState extends State<BloodBankScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 3, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<BloodController>().fetch();
    });
  }

  @override
  void dispose() {
    _tabs.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<BloodController>();
    return RoleScaffold(
      title: 'Blood Bank',
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
                Tab(text: 'Inventory'),
                Tab(text: 'Donors'),
                Tab(text: 'My Requests'),
              ],
            ),
          ),
          Expanded(
            child: ctrl.loading
                ? const LoadingView()
                : TabBarView(
                    controller: _tabs,
                    children: [
                      _inventoryView(ctrl),
                      _donorsView(ctrl),
                      _myRequestsView(ctrl),
                    ],
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showActions,
        icon: const Icon(Icons.add_rounded),
        label: const Text('Request / Donate'),
      ),
    );
  }

  Widget _inventoryView(BloodController ctrl) {
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
        childAspectRatio: 1.5,
      ),
      itemCount: ctrl.inventory.length,
      itemBuilder: (_, i) {
        final inv = ctrl.inventory[i];
        return Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                AppColors.danger.withOpacity(0.05),
                AppColors.danger.withOpacity(0.12),
              ],
            ),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.danger.withOpacity(0.2)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.bloodtype_rounded,
                      color: AppColors.danger, size: 32),
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: inv.units < 5
                          ? AppColors.danger
                          : AppColors.success,
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(inv.units < 5 ? 'LOW' : 'OK',
                        style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800)),
                  ),
                ],
              ),
              const Spacer(),
              Text(inv.bloodGroup,
                  style: const TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w800,
                      color: AppColors.danger)),
              Text('${inv.units} units available',
                  style: const TextStyle(
                      fontSize: 12, color: AppColors.textSecondary)),
            ],
          ),
        );
      },
    );
  }

  Widget _donorsView(BloodController ctrl) {
    if (ctrl.donors.isEmpty) {
      return const EmptyState(
          icon: Icons.volunteer_activism_rounded, title: 'No donors');
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: ctrl.donors.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (_, i) {
        final d = ctrl.donors[i];
        return Container(
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
                  color: AppColors.danger.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Center(
                  child: Text(d.bloodGroup,
                      style: const TextStyle(
                          color: AppColors.danger,
                          fontWeight: FontWeight.w800,
                          fontSize: 13)),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(d.name,
                        style: const TextStyle(
                            fontWeight: FontWeight.w700, fontSize: 14)),
                    Text('${d.phone}${d.city != null ? " • ${d.city}" : ""}',
                        style: const TextStyle(
                            fontSize: 12, color: AppColors.textSecondary)),
                  ],
                ),
              ),
              IconButton(
                onPressed: () =>
                    AppHelpers.snack(context, 'Calling ${d.name}...'),
                icon: const Icon(Icons.call_rounded, color: AppColors.success),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _myRequestsView(BloodController ctrl) {
    if (ctrl.requests.isEmpty) {
      return const EmptyState(
          icon: Icons.list_alt_rounded,
          title: 'No requests yet',
          message: 'Tap the button below to request blood');
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: ctrl.requests.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (_, i) {
        final r = ctrl.requests[i];
        return Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text('${r.patientName} • ${r.bloodGroup}',
                        style: const TextStyle(
                            fontSize: 14.5, fontWeight: FontWeight.w700)),
                  ),
                  StatusBadge(status: r.status),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                '${r.units} units${r.hospital != null ? " • ${r.hospital}" : ""}${r.neededBy != null ? " • by ${AppHelpers.formatDate(r.neededBy!)}" : ""}',
                style: const TextStyle(
                    fontSize: 12.5, color: AppColors.textSecondary),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showActions() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (_) => Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
          color: AppColors.bg,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 44,
              height: 4,
              decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 20),
            ListTile(
              leading: Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: AppColors.danger.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.bloodtype_rounded,
                    color: AppColors.danger),
              ),
              title: const Text('Request Blood',
                  style: TextStyle(fontWeight: FontWeight.w700)),
              subtitle: const Text('Place an emergency or scheduled request'),
              onTap: () {
                Navigator.pop(context);
                _showRequestForm();
              },
            ),
            ListTile(
              leading: Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: AppColors.success.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.volunteer_activism_rounded,
                    color: AppColors.success),
              ),
              title: const Text('Register as Donor',
                  style: TextStyle(fontWeight: FontWeight.w700)),
              subtitle: const Text('Save lives by donating blood'),
              onTap: () {
                Navigator.pop(context);
                _showDonorForm();
              },
            ),
            const SizedBox(height: 12),
          ],
        ),
      ),
    );
  }

  void _showRequestForm() {
    final formKey = GlobalKey<FormState>();
    final name = TextEditingController();
    final hospital = TextEditingController();
    final contact = TextEditingController();
    String bloodGroup = 'O+';
    int units = 1;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: EdgeInsets.only(
              bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: const BoxDecoration(
              color: AppColors.bg,
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: SingleChildScrollView(
              child: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Request Blood',
                        style: TextStyle(
                            fontSize: 18, fontWeight: FontWeight.w800)),
                    const SizedBox(height: 16),
                    CustomTextField(
                        label: 'Patient Name',
                        controller: name,
                        prefixIcon: Icons.person_outline,
                        validator: (v) => Validators.required(v, 'Name')),
                    const SizedBox(height: 12),
                    const Text('Blood Group',
                        style: TextStyle(
                            fontWeight: FontWeight.w600, fontSize: 13)),
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 8,
                      children: BloodGroup.all
                          .map((g) => GestureDetector(
                                onTap: () =>
                                    setSheetState(() => bloodGroup = g),
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 14, vertical: 8),
                                  decoration: BoxDecoration(
                                    color: bloodGroup == g
                                        ? AppColors.danger
                                        : AppColors.surface,
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(
                                        color: bloodGroup == g
                                            ? AppColors.danger
                                            : AppColors.border),
                                  ),
                                  child: Text(g,
                                      style: TextStyle(
                                          color: bloodGroup == g
                                              ? Colors.white
                                              : AppColors.textPrimary,
                                          fontWeight: FontWeight.w600,
                                          fontSize: 13)),
                                ),
                              ))
                          .toList(),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        const Text('Units: ',
                            style: TextStyle(fontWeight: FontWeight.w600)),
                        IconButton(
                            onPressed: () => setSheetState(
                                () => units = units > 1 ? units - 1 : 1),
                            icon: const Icon(Icons.remove_circle_outline)),
                        Text('$units',
                            style: const TextStyle(
                                fontSize: 16, fontWeight: FontWeight.w700)),
                        IconButton(
                            onPressed: () => setSheetState(() => units++),
                            icon: const Icon(Icons.add_circle_outline)),
                      ],
                    ),
                    CustomTextField(
                        label: 'Hospital (Optional)',
                        controller: hospital,
                        prefixIcon: Icons.local_hospital_outlined),
                    const SizedBox(height: 12),
                    CustomTextField(
                        label: 'Contact Number',
                        controller: contact,
                        prefixIcon: Icons.phone_outlined,
                        keyboardType: TextInputType.phone,
                        validator: Validators.phone),
                    const SizedBox(height: 20),
                    PrimaryButton(
                      label: 'Submit Request',
                      icon: Icons.send_rounded,
                      onPressed: () async {
                        if (!formKey.currentState!.validate()) return;
                        await context.read<BloodController>().request(
                              patientName: name.text,
                              bloodGroup: bloodGroup,
                              units: units,
                              hospital: hospital.text,
                              contact: contact.text,
                            );
                        if (ctx.mounted) {
                          Navigator.pop(ctx);
                          AppHelpers.snack(context, 'Request submitted');
                        }
                      },
                    ),
                    const SizedBox(height: 12),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  void _showDonorForm() {
    final formKey = GlobalKey<FormState>();
    final name = TextEditingController();
    final phone = TextEditingController();
    final city = TextEditingController();
    String bloodGroup = 'O+';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: EdgeInsets.only(
              bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: const BoxDecoration(
              color: AppColors.bg,
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: SingleChildScrollView(
              child: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Register as Donor',
                        style: TextStyle(
                            fontSize: 18, fontWeight: FontWeight.w800)),
                    const SizedBox(height: 16),
                    CustomTextField(
                        label: 'Your Name',
                        controller: name,
                        prefixIcon: Icons.person_outline,
                        validator: (v) => Validators.required(v, 'Name')),
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      children: BloodGroup.all
                          .map((g) => GestureDetector(
                                onTap: () =>
                                    setSheetState(() => bloodGroup = g),
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 14, vertical: 8),
                                  decoration: BoxDecoration(
                                    color: bloodGroup == g
                                        ? AppColors.danger
                                        : AppColors.surface,
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(
                                        color: bloodGroup == g
                                            ? AppColors.danger
                                            : AppColors.border),
                                  ),
                                  child: Text(g,
                                      style: TextStyle(
                                          color: bloodGroup == g
                                              ? Colors.white
                                              : AppColors.textPrimary,
                                          fontWeight: FontWeight.w600)),
                                ),
                              ))
                          .toList(),
                    ),
                    const SizedBox(height: 12),
                    CustomTextField(
                        label: 'Phone',
                        controller: phone,
                        prefixIcon: Icons.phone_outlined,
                        keyboardType: TextInputType.phone,
                        validator: Validators.phone),
                    const SizedBox(height: 12),
                    CustomTextField(
                        label: 'City',
                        controller: city,
                        prefixIcon: Icons.location_city_outlined),
                    const SizedBox(height: 20),
                    PrimaryButton(
                      label: 'Register',
                      icon: Icons.volunteer_activism_rounded,
                      onPressed: () async {
                        if (!formKey.currentState!.validate()) return;
                        await context.read<BloodController>().registerDonor(
                              name: name.text,
                              bloodGroup: bloodGroup,
                              phone: phone.text,
                              city: city.text,
                            );
                        if (ctx.mounted) {
                          Navigator.pop(ctx);
                          AppHelpers.snack(context, 'Thank you for registering!');
                        }
                      },
                    ),
                    const SizedBox(height: 12),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
