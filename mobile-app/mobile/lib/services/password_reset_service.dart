import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../core/constants/api_constants.dart';

/// Mengembalikan null jika berhasil, atau pesan error jika gagal.
class PasswordResetService {
  static Future<String?> requestCode(String email) =>
      _post('/forgot-password', {'email': email});

  static Future<String?> reset({
    required String email,
    required String code,
    required String password,
    required String passwordConfirmation,
  }) =>
      _post('/reset-password', {
        'email': email,
        'code': code,
        'password': password,
        'password_confirmation': passwordConfirmation,
      });

  static Future<String?> _post(String path, Map<String, String> body) async {
    try {
      final res = await http
          .post(
            Uri.parse('${ApiConstants.baseUrl}$path'),
            headers: const {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: jsonEncode(body),
          )
          .timeout(const Duration(seconds: 20));

      if (res.statusCode >= 200 && res.statusCode < 300) return null;

      try {
        final data = jsonDecode(res.body);
        if (data is Map) {
          final errors = data['errors'];
          if (errors is Map && errors.isNotEmpty) {
            final first = errors.values.first;
            if (first is List && first.isNotEmpty) return first.first.toString();
          }
          if (data['message'] is String) return data['message'] as String;
        }
      } catch (_) {}
      return 'Terjadi kesalahan (${res.statusCode}). Coba lagi.';
    } on TimeoutException {
      return 'Koneksi terlalu lama. Periksa jaringan lalu coba lagi.';
    } catch (_) {
      return 'Tidak dapat terhubung ke server.';
    }
  }
}