import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/doctor_controller.dart';
import '../../widgets/doctor_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';

class DoctorsListScreen extends StatefulWidget {
  const DoctorsListScreen({super.key});

  @override
  State<DoctorsListScreen> createState() => _DoctorsListScreenState();
}

class _DoctorsListScreenState extends State<DoctorsListScreen> {
  final _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DoctorController>().fetchDoctors();
    });
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<DoctorController>();
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Find Doctors')),
      body: Column(
        children: [
          _searchBar(),
          if (ctrl.specializations.isNotEmpty) _chips(ctrl),
          Expanded(
            child: ctrl.loading
                ? const LoadingView(message: 'Loading doctors...')
                : ctrl.doctors.isEmpty
                    ? const EmptyState(
                        icon: Icons.search_off_rounded,
                        title: 'No doctors found',
                        message: 'Try adjusting your search or filters',
                      )
                    : ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: ctrl.doctors.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 10),
                        itemBuilder: (_, i) {
                          final d = ctrl.doctors[i];
                          return DoctorCard(
                            doctor: d,
                            onTap: () => Navigator.pushNamed(
                              context, AppRoutes.bookAppointment,
                              arguments: d,
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }

  Widget _searchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
      child: TextField(
        controller: _searchCtrl,
        onChanged: (v) => context.read<DoctorController>().setQuery(v),
        decoration: const InputDecoration(
          hintText: 'Search doctor name or specialization...',
          prefixIcon: Icon(Icons.search_rounded),
        ),
      ),
    );
  }

  Widget _chips(DoctorController ctrl) {
    return SizedBox(
      height: 44,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 12),
        children: [
          _chip(label: 'All', selected: ctrl.specializationFilter == null,
              onTap: () => ctrl.setSpecialization(null)),
          ...ctrl.specializations.map(
            (s) => _chip(
              label: s.name,
              selected: ctrl.specializationFilter == s.id,
              onTap: () => ctrl.setSpecialization(s.id),
            ),
          ),
        ],
      ),
    );
  }

  Widget _chip(
      {required String label,
      required bool selected,
      required VoidCallback onTap}) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
      child: GestureDetector(
        onTap: onTap,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : AppColors.surface,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
                color: selected ? AppColors.primary : AppColors.border),
          ),
          child: Text(
            label,
            style: TextStyle(
              color: selected ? Colors.white : AppColors.textSecondary,
              fontWeight: FontWeight.w600,
              fontSize: 12.5,
            ),
          ),
        ),
      ),
    );
  }
}
