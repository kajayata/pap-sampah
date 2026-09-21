import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../core/storage/token_storage.dart';
import '../models/user.dart';

class AuthService {
  static Future<User> login({
    required String email,
    required String password,
    String deviceName = 'flutter_mobile',
  }) async {
    final res = await ApiClient.post(
      ApiConstants.login,
      body: {
        'email': email,
        'password': password,
        'device_name': deviceName,
      },
    );

    final data = (res is Map && res['data'] != null && res['data'] is Map) ? res['data'] : res;

    if (data is Map && data['token'] != null && data['user'] != null) {
      final token = data['token'] as String;
      final user = User.fromJson(Map<String, dynamic>.from(data['user']));
      await TokenStorage.saveAuth(token: token, user: user);
      return user;
    }

    throw ApiException('Format respons server tidak valid');
  }

  static Future<User> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    final res = await ApiClient.post(
      ApiConstants.register,
      body: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
        if (phone != null && phone.isNotEmpty) 'phone': phone,
      },
    );

    final data = (res is Map && res['data'] != null && res['data'] is Map) ? res['data'] : res;

    if (data is Map && data['token'] != null && data['user'] != null) {
      final token = data['token'] as String;
      final user = User.fromJson(Map<String, dynamic>.from(data['user']));
      await TokenStorage.saveAuth(token: token, user: user);
      return user;
    }

    throw ApiException('Gagal mendaftarkan akun masyarakat');
  }

  static Future<User?> getCurrentUser() async {
    try {
      final res = await ApiClient.get(ApiConstants.me);
      final data = (res is Map && res['data'] != null) ? res['data'] : (res is Map && res['user'] != null ? res['user'] : res);
      if (data is Map) {
        final user = User.fromJson(Map<String, dynamic>.from(data));
        final token = await TokenStorage.getToken();
        if (token != null) {
          await TokenStorage.saveAuth(token: token, user: user);
        }
        return user;
      }
    } catch (_) {
      // Return cached user if offline or network error
      return TokenStorage.getUser();
    }
    return null;
  }

  static Future<void> logout() async {
    try {
      await ApiClient.post(ApiConstants.logout);
    } catch (_) {
      // Always clear local storage even if backend call fails
    } finally {
      await TokenStorage.clear();
    }
  }
}
