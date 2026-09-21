import 'package:flutter/material.dart';
import '../core/storage/token_storage.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

enum AuthStatus { initial, authenticating, authenticated, unauthenticated }

class AuthProvider extends ChangeNotifier {
  AuthStatus _status = AuthStatus.initial;
  User? _currentUser;
  String? _errorMessage;

  AuthStatus get status => _status;
  User? get currentUser => _currentUser;
  String? get errorMessage => _errorMessage;
  bool get isAuthenticated => _status == AuthStatus.authenticated && _currentUser != null;

  Future<void> checkAuthStatus() async {
    final hasToken = await TokenStorage.hasToken();
    if (!hasToken) {
      _status = AuthStatus.unauthenticated;
      _currentUser = null;
      notifyListeners();
      return;
    }

    _currentUser = await TokenStorage.getUser();
    _status = _currentUser != null ? AuthStatus.authenticated : AuthStatus.unauthenticated;
    notifyListeners();

    // Refresh user profile asynchronously
    if (_status == AuthStatus.authenticated) {
      try {
        final refreshedUser = await AuthService.getCurrentUser();
        if (refreshedUser != null) {
          _currentUser = refreshedUser;
          notifyListeners();
        }
      } catch (_) {}
    }
  }

  Future<bool> login(String email, String password) async {
    _status = AuthStatus.authenticating;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await AuthService.login(email: email, password: password);
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _status = AuthStatus.unauthenticated;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    _status = AuthStatus.authenticating;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await AuthService.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
        phone: phone,
      );
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _status = AuthStatus.unauthenticated;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await AuthService.logout();
    _currentUser = null;
    _status = AuthStatus.unauthenticated;
    notifyListeners();
  }
}
