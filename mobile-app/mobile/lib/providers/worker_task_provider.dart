import 'dart:io';
import 'package:flutter/material.dart';
import '../models/cleanup_task.dart';
import '../services/worker_service.dart';

class WorkerTaskProvider extends ChangeNotifier {
  List<CleanupTask> _tasks = [];
  CleanupTask? _currentTask;
  bool _isLoading = false;
  bool _isActionLoading = false;
  String? _errorMessage;
  String _statusFilter = 'ALL';

  List<CleanupTask> get tasks => _tasks;
  CleanupTask? get currentTask => _currentTask;
  bool get isLoading => _isLoading;
  bool get isActionLoading => _isActionLoading;
  String? get errorMessage => _errorMessage;
  String get statusFilter => _statusFilter;

  Future<void> loadTasks({String? status, bool refresh = false}) async {
    if (status != null) {
      _statusFilter = status;
    }

    _isLoading = true;
    _errorMessage = null;
    if (refresh) _tasks.clear();
    notifyListeners();

    try {
      _tasks = await WorkerService.getTasks(
        status: _statusFilter == 'ALL' ? null : _statusFilter,
      );
    } catch (e) {
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadTaskDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentTask = await WorkerService.getTaskDetail(id);
    } catch (e) {
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> acceptTask(int id) async {
    _isActionLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await WorkerService.acceptTask(id);
      await loadTaskDetail(id);
      await loadTasks();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      return false;
    } finally {
      _isActionLoading = false;
      notifyListeners();
    }
  }

  Future<bool> rejectTask(int id, String reason) async {
    _isActionLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await WorkerService.rejectTask(id, reason);
      await loadTaskDetail(id);
      await loadTasks();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      return false;
    } finally {
      _isActionLoading = false;
      notifyListeners();
    }
  }

  Future<bool> uploadPhoto({
    required int taskId,
    required File photoFile,
    required String type,
    double? latitude,
    double? longitude,
  }) async {
    _isActionLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await WorkerService.uploadPhoto(
        taskId: taskId,
        photoFile: photoFile,
        type: type,
        latitude: latitude,
        longitude: longitude,
      );
      await loadTaskDetail(taskId);
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      return false;
    } finally {
      _isActionLoading = false;
      notifyListeners();
    }
  }

  Future<bool> completeTask(int id) async {
    _isActionLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await WorkerService.completeTask(id);
      await loadTaskDetail(id);
      await loadTasks();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      return false;
    } finally {
      _isActionLoading = false;
      notifyListeners();
    }
  }
}
