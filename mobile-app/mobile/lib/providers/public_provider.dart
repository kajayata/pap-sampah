import 'package:flutter/material.dart';
import '../models/landfill.dart';
import '../models/news_article.dart';
import '../models/waste_bank.dart';
import '../models/waste_map_point.dart';
import '../models/weather_data.dart';
import '../services/public_service.dart';

class PublicProvider extends ChangeNotifier {
  // Map Data
  List<WasteMapPoint> _mapPoints = [];
  bool _isLoadingMap = false;
  String _mapFilter = 'all'; // 'all', 'active', 'resolved'
  WasteMapPoint? _selectedPoint;
  String? _mapErrorMessage;

  // Waste Banks
  List<WasteBank> _wasteBanks = [];
  bool _isLoadingBanks = false;
  String? _banksErrorMessage;

  // Landfills
  List<Landfill> _landfills = [];
  bool _isLoadingLandfills = false;

  // News
  List<NewsArticle> _newsList = [];
  bool _isLoadingNews = false;
  String? _newsErrorMessage;

  // Weather
  WeatherData? _weather;
  bool _isLoadingWeather = false;

  // Getters
  List<WasteMapPoint> get mapPoints => _mapPoints;
  bool get isLoadingMap => _isLoadingMap;
  String get mapFilter => _mapFilter;
  WasteMapPoint? get selectedPoint => _selectedPoint;
  String? get mapErrorMessage => _mapErrorMessage;

  List<WasteBank> get wasteBanks => _wasteBanks;
  bool get isLoadingBanks => _isLoadingBanks;
  String? get banksErrorMessage => _banksErrorMessage;

  List<Landfill> get landfills => _landfills;
  bool get isLoadingLandfills => _isLoadingLandfills;

  List<NewsArticle> get newsList => _newsList;
  bool get isLoadingNews => _isLoadingNews;
  String? get newsErrorMessage => _newsErrorMessage;

  WeatherData? get weather => _weather;
  bool get isLoadingWeather => _isLoadingWeather;

  /// Load map points with current or specified filter
  Future<void> loadMapPoints({String? filter}) async {
    if (filter != null) {
      _mapFilter = filter;
    }
    _isLoadingMap = true;
    _mapErrorMessage = null;
    notifyListeners();

    try {
      _mapPoints = await PublicService.getWastePoints(type: _mapFilter);
    } catch (e) {
      _mapErrorMessage = e.toString();
    } finally {
      _isLoadingMap = false;
      notifyListeners();
    }
  }

  void selectPoint(WasteMapPoint? point) {
    _selectedPoint = point;
    notifyListeners();
  }

  /// Load active waste banks
  Future<void> loadWasteBanks({String? search}) async {
    _isLoadingBanks = true;
    _banksErrorMessage = null;
    notifyListeners();

    try {
      _wasteBanks = await PublicService.getWasteBanks(search: search);
    } catch (e) {
      _banksErrorMessage = e.toString();
    } finally {
      _isLoadingBanks = false;
      notifyListeners();
    }
  }

  /// Load landfills
  Future<void> loadLandfills() async {
    _isLoadingLandfills = true;
    notifyListeners();

    try {
      _landfills = await PublicService.getLandfills();
    } catch (_) {
      // Ignored
    } finally {
      _isLoadingLandfills = false;
      notifyListeners();
    }
  }

  /// Load news articles
  Future<void> loadNews({String? search}) async {
    _isLoadingNews = true;
    _newsErrorMessage = null;
    notifyListeners();

    try {
      _newsList = await PublicService.getNews(search: search);
    } catch (e) {
      _newsErrorMessage = e.toString();
    } finally {
      _isLoadingNews = false;
      notifyListeners();
    }
  }

  /// Load weather info
  Future<void> loadWeather() async {
    _isLoadingWeather = true;
    notifyListeners();

    try {
      _weather = await PublicService.getWeather();
    } catch (_) {
      // Ignored
    } finally {
      _isLoadingWeather = false;
      notifyListeners();
    }
  }

  /// Refresh all data on citizen home screen
  Future<void> refreshHomeData() async {
    await Future.wait([
      loadWeather(),
      loadNews(),
      loadMapPoints(),
      loadWasteBanks(),
    ]);
  }
}
