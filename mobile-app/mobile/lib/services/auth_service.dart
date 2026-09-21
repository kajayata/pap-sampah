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

    if (res['data'] != null && res['data']['token'] != null) {
      final token = res['data']['token'] as String;
      final user = User.fromJson(res['data']['user']);
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

    if (res['data'] != null && res['data']['token'] != null) {
      final token = res['data']['token'] as String;
      final user = User.fromJson(res['data']['user']);
      await TokenStorage.saveAuth(token: token, user: user);
      return user;
    }

    throw ApiException('Gagal mendaftarkan akun masyarakat');
  }

  static Future<User?> getCurrentUser() async {
    try {
      final res = await ApiClient.get(ApiConstants.me);
      if (res['data'] != null) {
        final user = User.fromJson(res['data']);
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
