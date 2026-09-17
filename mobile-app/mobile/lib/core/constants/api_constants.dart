import 'package:shared_preferences/shared_preferences.dart';

class ApiConstants {
  // Default URL points to localhost:8000 (works seamlessly via `adb reverse tcp:8000 tcp:8000` on USB debugging)
  static const String defaultBaseUrl = 'http://127.0.0.1:8000/api';
  static const String defaultStorageBaseUrl = 'http://127.0.0.1:8000';

  static String _baseUrl = defaultBaseUrl;

  static String get baseUrl => _baseUrl;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    final customUrl = prefs.getString('custom_api_base_url');
    if (customUrl != null && customUrl.isNotEmpty) {
      _baseUrl = customUrl;
    }
  }

  static Future<void> setCustomBaseUrl(String url) async {
    _baseUrl = url.endsWith('/') ? url.substring(0, url.length - 1) : url;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('custom_api_base_url', _baseUrl);
  }

  static Future<void> resetToDefault() async {
    _baseUrl = defaultBaseUrl;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('custom_api_base_url');
  }

  // Auth endpoints
  static String get login => '$_baseUrl/auth/login';
  static String get register => '$_baseUrl/auth/register';
  static String get logout => '$_baseUrl/auth/logout';
  static String get me => '$_baseUrl/auth/me';

  // Citizen report endpoints
  static String get categories => '$_baseUrl/categories';
  static String get reports => '$_baseUrl/reports';
  static String reportDetail(int id) => '$_baseUrl/reports/$id';

  // Worker task endpoints
  static String get workerTasks => '$_baseUrl/tasks';
  static String workerTaskDetail(int id) => '$_baseUrl/tasks/$id';
  static String workerTaskAccept(int id) => '$_baseUrl/tasks/$id/accept';
  static String workerTaskReject(int id) => '$_baseUrl/tasks/$id/reject';
  static String workerTaskPhotos(int id) => '$_baseUrl/tasks/$id/photos';
  static String workerTaskComplete(int id) => '$_baseUrl/tasks/$id/complete';

  // Spatial & public master data endpoints
  static String get mapWastePoints => '$_baseUrl/map/waste-points';
  static String get mapHeatmap => '$_baseUrl/map/heatmap';
  static String get mapBoundaries => '$_baseUrl/map/boundaries';
  static String get wasteBanks => '$_baseUrl/waste-banks';
  static String get landfills => '$_baseUrl/landfills';
  static String get news => '$_baseUrl/news';
  static String newsDetail(String slug) => '$_baseUrl/news/$slug';
  static String get weather => '$_baseUrl/weather';
  static String get settings => '$_baseUrl/settings';
}
