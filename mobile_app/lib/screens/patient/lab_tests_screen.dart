import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../config/app_colors.dart';
import '../../config/routes.dart';
import '../../controllers/lab_controller.dart';
import '../../models/lab.dart';
import '../../utils/helpers.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/role_scaffold.dart';
import '../../widgets/status_badge.dart';

class LabTestsScreen extends StatefulWidget {
  const LabTestsScreen({super.key});

  @override
  State<LabTestsScreen> createState() => _LabTestsScreenState();
}

class _LabTestsScreenState extends State<LabTestsScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;
  final _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<LabController>().fetch();
    });
  }

  @override
  void dispose() {
    _tabs.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _bookTest(LabTest test) async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );
    if (date == null || !mounted) return;
    final ok = await context.read<LabController>().book(test, date);
    if (!mounted) return;
    if (ok) {
      AppHelpers.snack(context, '${test.name} booked for ${AppHelpers.formatDate(date)}');
      _tabs.animateTo(1);
    }
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<LabController>();
    return RoleScaffold(
      title: 'Lab Tests',
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
                Tab(text: 'Browse Tests'),
                Tab(text: 'My Bookings'),
              ],
            ),
          ),
          Expanded(
            child: ctrl.loading
                ? const LoadingView()
                : TabBarView(
                    controller: _tabs,
                    children: [
                      _testsList(ctrl),
                      _bookingsList(ctrl),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _testsList(LabController ctrl) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
          child: TextField(
            controller: _searchCtrl,
            onChanged: (v) => ctrl.setQuery(v),
            decoration: const InputDecoration(
              hintText: 'Search tests...',
              prefixIcon: Icon(Icons.search_rounded),
            ),
          ),
        ),
        Expanded(
          child: ctrl.tests.isEmpty
              ? const EmptyState(
                  icon: Icons.search_off_rounded,
                  title: 'No tests found',
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: ctrl.tests.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (_, i) {
                    final t = ctrl.tests[i];
                    return _testCard(t);
                  },
                ),
        ),
      ],
    );
  }

  Widget _testCard(LabTest t) {
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
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: AppColors.warning.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.science_rounded,
                    color: AppColors.warning),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(t.name,
                        style: const TextStyle(
                            fontSize: 14.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary)),
                    if (t.category != null) ...[
                      const SizedBox(height: 2),
                      Text(t.category!,
                          style: const TextStyle(
                              fontSize: 12, color: AppColors.textMuted)),
                    ],
                  ],
                ),
              ),
              Text(AppHelpers.currency(t.price),
                  style: const TextStyle(
                      color: AppColors.accent,
                      fontSize: 16,
                      fontWeight: FontWeight.w800)),
            ],
          ),
          if (t.description != null) ...[
            const SizedBox(height: 8),
            Text(t.description!,
                style: const TextStyle(
                    fontSize: 12.5, color: AppColors.textSecondary, height: 1.5)),
          ],
          const SizedBox(height: 10),
          Row(
            children: [
              if (t.durationHours != null) ...[
                const Icon(Icons.schedule_rounded,
                    size: 13, color: AppColors.textMuted),
                const SizedBox(width: 4),
                Text('Results in ${t.durationHours}h',
                    style: const TextStyle(
                        fontSize: 11.5, color: AppColors.textSecondary)),
              ],
              const Spacer(),
              SizedBox(
                width: 110,
                child: PrimaryButton(
                  label: 'Book',
                  fullWidth: true,
                  padding:
                      const EdgeInsets.symmetric(vertical: 10, horizontal: 12),
                  onPressed: () => _bookTest(t),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _bookingsList(LabController ctrl) {
    if (ctrl.bookings.isEmpty) {
      return const EmptyState(
        icon: Icons.event_note_rounded,
        title: 'No bookings yet',
        message: 'Your lab test bookings will appear here',
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: ctrl.bookings.length,
      separatorBuilder: (_, __) => const SizedBox(height: 10),
      itemBuilder: (_, i) {
        final b = ctrl.bookings[i];
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
                    child: Text(b.labTest?.name ?? 'Test',
                        style: const TextStyle(
                            fontSize: 14.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary)),
                  ),
                  StatusBadge(status: b.status),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.calendar_today_rounded,
                      size: 13, color: AppColors.textMuted),
                  const SizedBox(width: 4),
                  Text(AppHelpers.formatDate(b.bookingDate),
                      style: const TextStyle(
                          fontSize: 12, color: AppColors.textSecondary)),
                  const Spacer(),
                  if (b.resultFile != null)
                    TextButton.icon(
                      onPressed: () =>
                          AppHelpers.snack(context, 'Downloading report...'),
                      icon: const Icon(Icons.download_rounded, size: 16),
                      label: const Text('Download'),
                    ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }
}
