import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/landfill.dart';
import '../models/news_article.dart';
import '../models/waste_bank.dart';
import '../models/waste_map_point.dart';
import '../models/weather_data.dart';

class PublicService {
  /// Fetch spatial waste points with optional village & type filter ('all', 'active', 'resolved')
  static Future<List<WasteMapPoint>> getWastePoints({int? villageId, String type = 'all'}) async {
    final queryParams = <String, String>{
      'type': type,
    };
    if (villageId != null) {
      queryParams['village_id'] = villageId.toString();
    }

    final uri = Uri.parse(ApiConstants.mapWastePoints).replace(queryParameters: queryParams);
    final response = await ApiClient.get(uri.toString());

    if (response is Map && response['data'] != null && response['data']['points'] is List) {
      final List list = response['data']['points'];
      return list.map((item) => WasteMapPoint.fromJson(item)).toList();
    }
    return [];
  }

  /// Fetch active waste banks (Bank Sampah)
  static Future<List<WasteBank>> getWasteBanks({int? villageId, String? search}) async {
    final queryParams = <String, String>{};
    if (villageId != null) {
      queryParams['village_id'] = villageId.toString();
    }
    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }

    final uri = Uri.parse(ApiConstants.wasteBanks).replace(queryParameters: queryParams.isNotEmpty ? queryParams : null);
    final response = await ApiClient.get(uri.toString());

    if (response is Map && response['data'] != null && response['data']['waste_banks'] is List) {
      final List list = response['data']['waste_banks'];
      return list.map((item) => WasteBank.fromJson(item)).toList();
    }
    return [];
  }

  /// Fetch active landfills (TPA)
  static Future<List<Landfill>> getLandfills() async {
    final response = await ApiClient.get(ApiConstants.landfills);

    if (response is Map && response['data'] != null && response['data']['landfills'] is List) {
      final List list = response['data']['landfills'];
      return list.map((item) => Landfill.fromJson(item)).toList();
    }
    return [];
  }

  /// Fetch published environmental educational articles & news
  static Future<List<NewsArticle>> getNews({String? search, int perPage = 10}) async {
    final queryParams = <String, String>{
      'per_page': perPage.toString(),
    };
    if (search != null && search.trim().isNotEmpty) {
      queryParams['search'] = search.trim();
    }

    final uri = Uri.parse(ApiConstants.news).replace(queryParameters: queryParams);
    final response = await ApiClient.get(uri.toString());

    if (response is Map && response['data'] != null && response['data']['articles'] is List) {
      final List list = response['data']['articles'];
      return list.map((item) => NewsArticle.fromJson(item)).toList();
    }
    return [];
  }

  /// Fetch single news article detail
  static Future<NewsArticle?> getNewsDetail(String slug) async {
    final response = await ApiClient.get(ApiConstants.newsDetail(slug));
    if (response is Map && response['data'] != null) {
      return NewsArticle.fromJson(response['data']);
    }
    return null;
  }

  /// Fetch current weather data for Sumbersari District
  static Future<WeatherData?> getWeather() async {
    try {
      final response = await ApiClient.get(ApiConstants.weather);
      if (response is Map && response['data'] != null) {
        return WeatherData.fromJson(response['data']);
      }
    } catch (_) {
      // Fallback null if weather fails
    }
    return null;
  }
}
