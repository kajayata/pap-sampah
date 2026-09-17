import 'waste_report.dart';

class CleanupTaskPhoto {
  final int id;
  final String url;
  final String type; // BEFORE or AFTER
  final String? uploaderName;
  final DateTime? createdAt;

  CleanupTaskPhoto({
    required this.id,
    required this.url,
    required this.type,
    this.uploaderName,
    this.createdAt,
  });

  bool get isAfter => type.toUpperCase() == 'AFTER';
  bool get isBefore => type.toUpperCase() == 'BEFORE';

  factory CleanupTaskPhoto.fromJson(Map<String, dynamic> json) {
    String? uploader;
    if (json['uploader'] is Map) {
      uploader = json['uploader']['name'];
    }

    return CleanupTaskPhoto(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      url: json['url'] ?? '',
      type: json['type'] ?? 'AFTER',
      uploaderName: uploader ?? json['uploader_name'],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }
}

class TaskWorkerAssignment {
  final int id;
  final int workerId;
  final String workerName;
  final String? workerPhone;
  final String status;
  final String statusLabel;
  final String? rejectionReason;

  TaskWorkerAssignment({
    required this.id,
    required this.workerId,
    required this.workerName,
    this.workerPhone,
    required this.status,
    required this.statusLabel,
    this.rejectionReason,
  });

  factory TaskWorkerAssignment.fromJson(Map<String, dynamic> json) {
    String name = 'Petugas Kebersihan';
    String? phone;
    if (json['worker'] is Map) {
      name = json['worker']['name'] ?? name;
      phone = json['worker']['phone'];
    }

    return TaskWorkerAssignment(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      workerId: json['worker_id'] is int ? json['worker_id'] : int.tryParse(json['worker_id'].toString()) ?? 0,
      workerName: name,
      workerPhone: phone,
      status: json['status'] ?? 'PENDING',
      statusLabel: json['status_label'] ?? 'Menunggu Respon',
      rejectionReason: json['rejection_reason'],
    );
  }
}

class CleanupTask {
  final int id;
  final String status;
  final String statusLabel;
  final String? notes; // Catatan / Instruksi Peralatan dari Admin Desa
  final DateTime? assignedAt;
  final DateTime? startedAt;
  final DateTime? completedAt;
  final String myStatus; // PENDING, ACCEPTED, REJECTED, COMPLETED
  final String myStatusLabel;
  final String? myRejectionReason;
  final String? assignedByName;
  final String? assignedByPhone;
  final WasteReport? report;
  final List<TaskWorkerAssignment> workers;
  final List<CleanupTaskPhoto> photos;

  CleanupTask({
    required this.id,
    required this.status,
    required this.statusLabel,
    this.notes,
    this.assignedAt,
    this.startedAt,
    this.completedAt,
    required this.myStatus,
    required this.myStatusLabel,
    this.myRejectionReason,
    this.assignedByName,
    this.assignedByPhone,
    this.report,
    this.workers = const [],
    this.photos = const [],
  });

  bool get isMyAssignmentPending => myStatus == 'PENDING';
  bool get isMyAssignmentAccepted => myStatus == 'ACCEPTED';
  bool get isMyAssignmentRejected => myStatus == 'REJECTED';
  bool get isMyAssignmentCompleted => myStatus == 'COMPLETED';

  bool get isTaskCompleted => status == 'COMPLETED';
  bool get isTaskInProgress => status == 'IN_PROGRESS';

  List<CleanupTaskPhoto> get beforePhotos => photos.where((p) => p.isBefore).toList();
  List<CleanupTaskPhoto> get afterPhotos => photos.where((p) => p.isAfter).toList();

  factory CleanupTask.fromJson(Map<String, dynamic> json) {
    String? assignedName;
    String? assignedPhone;
    if (json['assigned_by'] is Map) {
      assignedName = json['assigned_by']['name'];
      assignedPhone = json['assigned_by']['phone'];
    }

    WasteReport? reportObj;
    if (json['report'] is Map) {
      reportObj = WasteReport.fromJson(json['report']);
    }

    List<TaskWorkerAssignment> workerList = [];
    if (json['workers'] is List) {
      workerList = (json['workers'] as List)
          .map((w) => TaskWorkerAssignment.fromJson(w))
          .toList();
    }

    List<CleanupTaskPhoto> photoList = [];
    if (json['photos'] is List) {
      photoList = (json['photos'] as List)
          .map((p) => CleanupTaskPhoto.fromJson(p))
          .toList();
    }

    // Determine myStatus if coming from task detail or list
    String workerStatus = json['my_status'] ?? 'PENDING';
    String workerStatusLabel = json['my_status_label'] ?? 'Menunggu';
    String? rejectReason = json['my_rejection_reason'];

    if (json['my_assignment'] is Map) {
      final myAssign = json['my_assignment'];
      workerStatus = myAssign['status'] ?? workerStatus;
      workerStatusLabel = myAssign['status_label'] ?? workerStatusLabel;
      rejectReason = myAssign['rejection_reason'] ?? rejectReason;
    }

    return CleanupTask(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      status: json['status'] ?? 'PENDING',
      statusLabel: json['status_label'] ?? 'Ditugaskan',
      notes: json['notes'],
      assignedAt: json['assigned_at'] != null ? DateTime.tryParse(json['assigned_at']) : null,
      startedAt: json['started_at'] != null ? DateTime.tryParse(json['started_at']) : null,
      completedAt: json['completed_at'] != null ? DateTime.tryParse(json['completed_at']) : null,
      myStatus: workerStatus,
      myStatusLabel: workerStatusLabel,
      myRejectionReason: rejectReason,
      assignedByName: assignedName,
      assignedByPhone: assignedPhone,
      report: reportObj,
      workers: workerList,
      photos: photoList,
    );
  }
}
