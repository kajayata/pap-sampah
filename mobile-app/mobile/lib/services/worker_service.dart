import 'dart:io';
import 'package:http/http.dart' as http;
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/cleanup_task.dart';

class WorkerService {
  /// Fetch list of assigned tasks for current worker
  static Future<List<CleanupTask>> getTasks({String? status}) async {
    final queryParams = <String, String>{};
    if (status != null && status != 'ALL') {
      queryParams['status'] = status;
    }

    final uri = Uri.parse(ApiConstants.workerTasks).replace(
      queryParameters: queryParams.isNotEmpty ? queryParams : null,
    );

    final response = await ApiClient.get(uri.toString());

    if (response is Map && response['data'] is List) {
      final List list = response['data'];
      return list.map((item) => CleanupTask.fromJson(item)).toList();
    }
    return [];
  }

  /// Fetch task detail with full report, photos, and assignments
  static Future<CleanupTask?> getTaskDetail(int id) async {
    final response = await ApiClient.get(ApiConstants.workerTaskDetail(id));

    if (response is Map && response['data'] != null && response['data']['task'] != null) {
      final Map<String, dynamic> taskData = Map<String, dynamic>.from(response['data']['task']);
      if (response['data']['my_assignment'] != null) {
        taskData['my_assignment'] = response['data']['my_assignment'];
      }
      return CleanupTask.fromJson(taskData);
    }
    return null;
  }

  /// Accept assigned task
  static Future<void> acceptTask(int id) async {
    await ApiClient.post(ApiConstants.workerTaskAccept(id));
  }

  /// Reject assigned task with mandatory reason
  static Future<void> rejectTask(int id, String reason) async {
    await ApiClient.post(
      ApiConstants.workerTaskReject(id),
      body: {'reason': reason},
    );
  }

  /// Upload before / after cleanup photo evidence
  static Future<void> uploadPhoto({
    required int taskId,
    required File photoFile,
    required String type, // 'BEFORE' or 'AFTER'
    double? latitude,
    double? longitude,
  }) async {
    final fields = <String, String>{
      'type': type.toUpperCase(),
    };
    if (latitude != null) fields['latitude'] = latitude.toString();
    if (longitude != null) fields['longitude'] = longitude.toString();

    final file = await http.MultipartFile.fromPath('photo', photoFile.path);

    await ApiClient.multipartPost(
      url: ApiConstants.workerTaskPhotos(taskId),
      fields: fields,
      files: [file],
    );
  }

  /// Complete worker's assignment (enforces Invariant #3: at least 1 AFTER photo)
  static Future<void> completeTask(int id) async {
    await ApiClient.post(ApiConstants.workerTaskComplete(id));
  }
}
