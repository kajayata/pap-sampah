import 'dart:async';
import 'package:flutter/material.dart';
import '../models/waste_report.dart';
import '../services/notification_service.dart';
import '../services/worker_service.dart';

/// NotificationProvider — monitors state changes in reports and tasks,
/// then triggers local notifications when relevant status transitions occur.
class NotificationProvider extends ChangeNotifier {
  Timer? _pollingTimer;
  bool _isRunning = false;

  // Tracks last known statuses to detect changes
  final Map<int, String> _lastReportStatuses = {};
  final Map<int, String> _lastTaskStatuses = {};

  List<NotificationItem> _notifications = [];
  List<NotificationItem> get notifications => _notifications;
  int get unreadCount => _notifications.where((n) => !n.isRead).length;

  /// Start polling for status changes (call after successful login)
  void startPolling({bool isCitizen = true}) {
    if (_isRunning) return;
    _isRunning = true;

    // Poll every 45 seconds
    _pollingTimer = Timer.periodic(const Duration(seconds: 45), (timer) {
      if (isCitizen) {
        _pollCitizenReports();
      } else {
        _pollWorkerTasks();
      }
    });
  }

  void stopPolling() {
    _pollingTimer?.cancel();
    _pollingTimer = null;
    _isRunning = false;
    _lastReportStatuses.clear();
    _lastTaskStatuses.clear();
  }

  Future<void> _pollCitizenReports() async {
    try {
      final reports = await _fetchReports();
      for (final report in reports) {
        final lastStatus = _lastReportStatuses[report.id];
        if (lastStatus != null && lastStatus != report.status) {
          // Status changed — fire local notification
          await NotificationService.citizenReportStatusChanged(
            reportId: report.id,
            reportCode: report.reportCode,
            newStatusLabel: report.statusLabel,
          );

          _addNotification(NotificationItem(
            id: 'report_${report.id}_${report.status}',
            title: 'Status Laporan Diperbarui',
            body: 'Laporan ${report.reportCode} kini berstatus: ${report.statusLabel}',
            type: NotificationItemType.reportStatus,
            refId: report.id,
            createdAt: DateTime.now(),
          ));
        }
        _lastReportStatuses[report.id] = report.status;
      }
    } catch (_) {}
  }

  Future<void> _pollWorkerTasks() async {
    try {
      final tasks = await WorkerService.getTasks();
      for (final task in tasks) {
        final lastMyStatus = _lastTaskStatuses[task.id];

        // Detect new PENDING task
        if (lastMyStatus == null && task.isMyAssignmentPending) {
          await NotificationService.workerNewTask(task.id);
          _addNotification(NotificationItem(
            id: 'task_new_${task.id}',
            title: '🧹 Tugas Pembersihan Baru',
            body: 'Anda mendapat tugas pembersihan #${task.id}. Buka untuk menerima.',
            type: NotificationItemType.workerTask,
            refId: task.id,
            createdAt: DateTime.now(),
          ));
        }

        // Detect task completion (all workers done)
        if (lastMyStatus != null &&
            lastMyStatus != 'COMPLETED' &&
            task.isMyAssignmentCompleted) {
          await NotificationService.workerTaskCompleted(task.id);
          _addNotification(NotificationItem(
            id: 'task_done_${task.id}',
            title: '✅ Tugas Selesai',
            body: 'Tugas pembersihan #${task.id} berhasil diselesaikan!',
            type: NotificationItemType.workerTask,
            refId: task.id,
            createdAt: DateTime.now(),
          ));
        }

        _lastTaskStatuses[task.id] = task.myStatus;
      }
    } catch (_) {}
  }

  // Stub: fetch reports from provider state (to avoid circular dependency)
  Future<List<WasteReport>> _fetchReports() async => [];

  void _addNotification(NotificationItem item) {
    // Avoid duplicates
    if (_notifications.any((n) => n.id == item.id)) return;
    _notifications = [item, ..._notifications];
    if (_notifications.length > 50) {
      _notifications = _notifications.take(50).toList();
    }
    notifyListeners();
  }

  /// Manually add a notification (e.g. from action results)
  void addNotification(NotificationItem item) => _addNotification(item);

  /// Mark all as read
  void markAllRead() {
    _notifications = _notifications.map((n) => n.copyWith(isRead: true)).toList();
    notifyListeners();
  }

  /// Mark single notification as read
  void markRead(String id) {
    _notifications = _notifications.map((n) {
      if (n.id == id) return n.copyWith(isRead: true);
      return n;
    }).toList();
    notifyListeners();
  }

  void clearAll() {
    _notifications = [];
    notifyListeners();
  }

  @override
  void dispose() {
    stopPolling();
    super.dispose();
  }
}

enum NotificationItemType { reportStatus, workerTask, system }

class NotificationItem {
  final String id;
  final String title;
  final String body;
  final NotificationItemType type;
  final int? refId;
  final DateTime createdAt;
  final bool isRead;

  const NotificationItem({
    required this.id,
    required this.title,
    required this.body,
    required this.type,
    this.refId,
    required this.createdAt,
    this.isRead = false,
  });

  NotificationItem copyWith({bool? isRead}) {
    return NotificationItem(
      id: id,
      title: title,
      body: body,
      type: type,
      refId: refId,
      createdAt: createdAt,
      isRead: isRead ?? this.isRead,
    );
  }
}
