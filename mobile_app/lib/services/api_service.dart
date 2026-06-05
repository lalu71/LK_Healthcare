import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

/// Lightweight HTTP wrapper. Controllers should use this to call the backend.
/// Currently used as a stub — controllers return mock data until you wire
/// real endpoints in.
class ApiService {
  ApiService._();
  static final ApiService instance = ApiService._();

  String? _token;

  void setToken(String? token) => _token = token;
  String? get token => _token;

  Map<String, String> _headers({bool json = true}) => {
        if (json) 'Content-Type': 'application/json',
        'Accept': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  Future<dynamic> get(String url) async {
    try {
      final res = await http.get(Uri.parse(url), headers: _headers()).timeout(
            const Duration(seconds: 20),
          );
      return _handle(res);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }

  Future<dynamic> post(String url, {Map<String, dynamic>? body}) async {
    try {
      final res = await http
          .post(Uri.parse(url),
              headers: _headers(), body: jsonEncode(body ?? {}))
          .timeout(const Duration(seconds: 20));
      return _handle(res);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }

  Future<dynamic> put(String url, {Map<String, dynamic>? body}) async {
    try {
      final res = await http
          .put(Uri.parse(url),
              headers: _headers(), body: jsonEncode(body ?? {}))
          .timeout(const Duration(seconds: 20));
      return _handle(res);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }

  Future<dynamic> delete(String url) async {
    try {
      final res = await http
          .delete(Uri.parse(url), headers: _headers())
          .timeout(const Duration(seconds: 20));
      return _handle(res);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }

  dynamic _handle(http.Response res) {
    final body = res.body.isEmpty ? {} : jsonDecode(res.body);
    if (res.statusCode >= 200 && res.statusCode < 300) {
      return body;
    }
    final msg = body is Map && body['message'] != null
        ? body['message'].toString()
        : 'Request failed (${res.statusCode})';
    throw ApiException(msg, statusCode: res.statusCode);
  }
}

class ApiException implements Exception {
  final String message;
  final int? statusCode;
  ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}
