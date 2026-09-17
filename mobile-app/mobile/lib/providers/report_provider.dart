import 'dart:io';
import 'package:flutter/material.dart';
import '../models/waste_category.dart';
import '../models/waste_report.dart';
import '../services/report_service.dart';

class ReportProvider extends ChangeNotifier {
  List<WasteCategory> _categories = [];
  List<WasteReport> _reports = [];
  bool _isLoading = false;
  bool _isSubmitting = false;
  String? _errorMessage;
  String _currentStatusFilter = 'ALL';

  List<WasteCategory> get categories => _categories;
  List<WasteReport> get reports => _reports;
  bool get isLoading => _isLoading;
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;
  String get currentStatusFilter => _currentStatusFilter;

  Future<void> loadCategories() async {
    try {
      _categories = await ReportService.getCategories();
      notifyListeners();
    } catch (_) {}
  }

  Future<void> loadReports({String? status, bool refresh = false}) async {
    if (status != null) {
      _currentStatusFilter = status;
    }

    _isLoading = true;
    _errorMessage = null;
    if (refresh) _reports.clear();
    notifyListeners();

    try {
      _reports = await ReportService.getMyReports(
        status: _currentStatusFilter == 'ALL' ? null : _currentStatusFilter,
      );
    } catch (e) {
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<WasteReport?> submitReport({
    required File photoFile,
    required int categoryId,
    required double latitude,
    required double longitude,
    String? description,
  }) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final newReport = await ReportService.submitReport(
        photoFile: photoFile,
        categoryId: categoryId,
        latitude: latitude,
        longitude: longitude,
        description: description,
      );
      _reports.insert(0, newReport);
      return newReport;
    } catch (e) {
      _errorMessage = e.toString();
      return null;
    } finally {
      _isSubmitting = false;
      notifyListeners();
    }
  }
}
