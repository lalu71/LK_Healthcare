import 'package:flutter/foundation.dart';

import '../config/api_config.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../utils/constants.dart';

/// AuthController — handles login / register / logout state.
/// Backend wiring points are clearly marked; right now we simulate calls so
/// the UI can be driven end-to-end without a server.
class AuthController extends ChangeNotifier {
  AppUser? _user;
  bool _loading = false;
  String? _error;

  AppUser? get user => _user;
  bool get loading => _loading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;
  String get role => _user?.role ?? UserRole.patient;

  void _setLoading(bool v) {
    _loading = v;
    notifyListeners();
  }

  Future<bool> login(String email, String password,
      {String role = UserRole.patient}) async {
    _setLoading(true);
    _error = null;
    try {
      // === Backend integration point ===
      // final res = await ApiService.instance.post(ApiConfig.login,
      //   body: {'email': email, 'password': password});
      // _user = AppUser.fromJson(res['user']);
      // await StorageService.instance.saveToken(res['token']);

      await Future.delayed(const Duration(milliseconds: 900));
      _user = AppUser(
        id: 1,
        name: email.split('@').first,
        email: email,
        role: role,
        isActive: true,
      );
      await StorageService.instance.saveToken('mock-token');
      _setLoading(false);
      return true;
    } catch (e) {
      _error = e.toString();
      _setLoading(false);
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    String role = UserRole.patient,
  }) async {
    _setLoading(true);
    _error = null;
    try {
      // === Backend integration point ===
      // final res = await ApiService.instance.post(ApiConfig.register, body: {
      //   'name': name, 'email': email, 'phone': phone,
      //   'password': password, 'role': role,
      // });
      // _user = AppUser.fromJson(res['user']);
      // await StorageService.instance.saveToken(res['token']);

      await Future.delayed(const Duration(milliseconds: 900));
      _user = AppUser(
        id: 1,
        name: name,
        email: email,
        phone: phone,
        role: role,
      );
      await StorageService.instance.saveToken('mock-token');
      _setLoading(false);
      return true;
    } catch (e) {
      _error = e.toString();
      _setLoading(false);
      return false;
    }
  }

  Future<bool> forgotPassword(String email) async {
    _setLoading(true);
    try {
      // === Backend integration point ===
      // await ApiService.instance.post('${ApiConfig.baseUrl}/forgot-password',
      //   body: {'email': email});
      await Future.delayed(const Duration(milliseconds: 700));
      _setLoading(false);
      return true;
    } catch (e) {
      _error = e.toString();
      _setLoading(false);
      return false;
    }
  }

  Future<void> logout() async {
    // === Backend integration point ===
    // try { await ApiService.instance.post(ApiConfig.logout); } catch (_) {}
    _user = null;
    ApiService.instance.setToken(null);
    await StorageService.instance.clear();
    notifyListeners();
  }

  Future<void> tryAutoLogin() async {
    final token = await StorageService.instance.getToken();
    if (token == null) return;
    ApiService.instance.setToken(token);
    // === Backend integration point: fetch /api/v1/user with token ===
    final role = await StorageService.instance.getRole() ?? UserRole.patient;
    _user = AppUser(id: 1, name: 'Demo User', email: 'demo@lk.health', role: role);
    notifyListeners();
  }

  void switchRoleDemo(String role) {
    if (_user == null) return;
    _user = AppUser(
      id: _user!.id,
      name: _user!.name,
      email: _user!.email,
      phone: _user!.phone,
      role: role,
    );
    notifyListeners();
  }
}
