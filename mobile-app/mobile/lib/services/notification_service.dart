import 'package:flutter_local_notifications/flutter_local_notifications.dart';

/// NotificationService — wrapper untuk flutter_local_notifications.
/// Digunakan untuk menampilkan notifikasi lokal saat status tugas/laporan berubah.
class NotificationService {
  static final FlutterLocalNotificationsPlugin _plugin =
      FlutterLocalNotificationsPlugin();

  static bool _initialized = false;

  static const AndroidNotificationDetails _androidDetails =
      AndroidNotificationDetails(
    'papsampah_main',
    'Pap Sampah Notifikasi',
    channelDescription: 'Notifikasi status laporan sampah dan tugas kebersihan',
    importance: Importance.high,
    priority: Priority.high,
    icon: '@mipmap/ic_launcher',
    color: null,
    enableVibration: true,
    playSound: true,
  );

  static const NotificationDetails _notifDetails = NotificationDetails(
    android: _androidDetails,
  );

  /// Initialize the notification plugin. Call once from main().
  static Future<void> init() async {
    if (_initialized) return;

    const AndroidInitializationSettings androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
    );

    await _plugin.initialize(
      initSettings,
      onDidReceiveNotificationResponse: _onNotifTapped,
    );

    // Request Android 13+ permission
    await _plugin
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.requestNotificationsPermission();

    _initialized = true;
  }

  static void _onNotifTapped(NotificationResponse response) {
    // Payload can be used for deep-linking in future iterations
  }

  /// Show a notification with [title] and [body].
  static Future<void> show({
    required int id,
    required String title,
    required String body,
    String? payload,
  }) async {
    if (!_initialized) await init();
    await _plugin.show(id, title, body, _notifDetails, payload: payload);
  }

  // --- Convenience helpers ---

  /// Notify worker about a new task assignment.
  static Future<void> workerNewTask(int taskId) async {
    await show(
      id: 1000 + taskId,
      title: '🧹 Tugas Pembersihan Baru',
      body:
          'Anda mendapat tugas pembersihan #$taskId. Buka aplikasi untuk menerima atau menolak.',
      payload: 'task:$taskId',
    );
  }

  /// Notify citizen that their report status changed.
  static Future<void> citizenReportStatusChanged({
    required int reportId,
    required String reportCode,
    required String newStatusLabel,
  }) async {
    await show(
      id: 2000 + reportId,
      title: '📋 Status Laporan Diperbarui',
      body: 'Laporan $reportCode kini berstatus: $newStatusLabel',
      payload: 'report:$reportId',
    );
  }

  /// Notify worker that all team members have completed a task.
  static Future<void> workerTaskCompleted(int taskId) async {
    await show(
      id: 3000 + taskId,
      title: '✅ Tugas Selesai!',
      body:
          'Tugas pembersihan #$taskId telah ditandai selesai. Menunggu verifikasi Admin Desa.',
      payload: 'task:$taskId',
    );
  }

  /// Cancel all displayed notifications.
  static Future<void> cancelAll() async {
    await _plugin.cancelAll();
  }
}
