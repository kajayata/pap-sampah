import 'dart:io';
import 'package:http/http.dart' as http;
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/waste_category.dart';
import '../models/waste_report.dart';

class ReportService {
  static Future<List<WasteCategory>> getCategories() async {
    final res = await ApiClient.get(ApiConstants.categories);
    if (res['data'] is List) {
      return (res['data'] as List)
          .map((c) => WasteCategory.fromJson(c))
          .toList();
    }
    return [];
  }

  static Future<List<WasteReport>> getMyReports({String? status, int page = 1}) async {
    String url = '${ApiConstants.reports}?page=$page';
    if (status != null && status.isNotEmpty && status != 'ALL') {
      url += '&status=$status';
    }

    final res = await ApiClient.get(url);
    if (res['data'] is Map && res['data']['data'] is List) {
      return (res['data']['data'] as List)
          .map((r) => WasteReport.fromJson(r))
          .toList();
    } else if (res['data'] is List) {
      return (res['data'] as List)
          .map((r) => WasteReport.fromJson(r))
          .toList();
    }
    return [];
  }

  static Future<WasteReport> getReportDetail(int id) async {
    final res = await ApiClient.get(ApiConstants.reportDetail(id));
    if (res['data'] is Map) {
      return WasteReport.fromJson(res['data']);
    }
    throw ApiException('Format detail laporan tidak valid');
  }

  static Future<WasteReport> submitReport({
    required File photoFile,
    required int categoryId,
    required double latitude,
    required double longitude,
    String? description,
  }) async {
    final fields = <String, String>{
      'category_id': categoryId.toString(),
      'latitude': latitude.toString(),
      'longitude': longitude.toString(),
      if (description != null && description.isNotEmpty) 'description': description,
    };

    final multipartFile = await http.MultipartFile.fromPath(
      'photo',
      photoFile.path,
    );

    final res = await ApiClient.multipartPost(
      url: ApiConstants.reports,
      fields: fields,
      files: [multipartFile],
    );

    if (res['data'] is Map) {
      return WasteReport.fromJson(res['data']);
    }

    throw ApiException(res['message'] ?? 'Berhasil mengirimkan laporan');
  }
}
