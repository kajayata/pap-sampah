import 'waste_category.dart';

class WasteReportPhoto {
  final int id;
  final String url;
  final bool isAfter;

  WasteReportPhoto({
    required this.id,
    required this.url,
    this.isAfter = false,
  });

  factory WasteReportPhoto.fromJson(Map<String, dynamic> json) {
    return WasteReportPhoto(
      id: json['id'] ?? 0,
      url: json['url'] ?? '',
      isAfter: json['type'] == 'AFTER' || json['is_after'] == true,
    );
  }
}

class StatusHistoryItem {
  final String fromStatus;
  final String toStatus;
  final String? notes;
  final String? changedByName;
  final DateTime? createdAt;

  StatusHistoryItem({
    required this.fromStatus,
    required this.toStatus,
    this.notes,
    this.changedByName,
    this.createdAt,
  });

  factory StatusHistoryItem.fromJson(Map<String, dynamic> json) {
    return StatusHistoryItem(
      fromStatus: json['from_status'] ?? '',
      toStatus: json['to_status'] ?? '',
      notes: json['notes'],
      changedByName: json['changed_by'] is Map ? json['changed_by']['name'] : null,
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }
}

class WasteReport {
  final int id;
  final String reportCode;
  final String status;
  final String statusLabel;
  final String? description;
  final double? latitude;
  final double? longitude;
  final int? villageId;
  final String? villageName;
  final WasteCategory? category;
  final String? reporterName;
  final List<WasteReportPhoto> photos;
  final List<WasteReportPhoto> cleanupPhotos;
  final List<StatusHistoryItem> statusHistories;
  final DateTime? createdAt;
  final DateTime? resolvedAt;

  WasteReport({
    required this.id,
    required this.reportCode,
    required this.status,
    required this.statusLabel,
    this.description,
    this.latitude,
    this.longitude,
    this.villageId,
    this.villageName,
    this.category,
    this.reporterName,
    this.photos = const [],
    this.cleanupPhotos = const [],
    this.statusHistories = const [],
    this.createdAt,
    this.resolvedAt,
  });

  bool get isResolved => status == 'RESOLVED';
  bool get isPending => status == 'PENDING_VALIDATION';
  bool get isValidated => status == 'VALIDATED';
  bool get isInProgress => status == 'IN_PROGRESS' || status == 'ASSIGNED';
  bool get isRejected => status == 'REJECTED';

  String? get primaryPhotoUrl {
    if (photos.isNotEmpty) return photos.first.url;
    return null;
  }

  factory WasteReport.fromJson(Map<String, dynamic> json) {
    List<WasteReportPhoto> reportPhotos = [];
    if (json['photos'] is List) {
      reportPhotos = (json['photos'] as List)
          .map((p) => WasteReportPhoto.fromJson(p))
          .toList();
    } else if (json['primary_photo_url'] != null) {
      reportPhotos = [
        WasteReportPhoto(id: 0, url: json['primary_photo_url']),
      ];
    }

    List<WasteReportPhoto> taskPhotos = [];
    if (json['cleanup_task'] is Map && json['cleanup_task']['photos'] is List) {
      taskPhotos = (json['cleanup_task']['photos'] as List)
          .map((p) => WasteReportPhoto.fromJson(p))
          .toList();
    } else if (json['after_photos'] is List) {
      taskPhotos = (json['after_photos'] as List)
          .map((p) => WasteReportPhoto.fromJson(p))
          .toList();
    }

    List<StatusHistoryItem> histories = [];
    if (json['status_histories'] is List) {
      histories = (json['status_histories'] as List)
          .map((h) => StatusHistoryItem.fromJson(h))
          .toList();
    }

    WasteCategory? cat;
    if (json['category'] is Map) {
      cat = WasteCategory.fromJson(json['category']);
    } else if (json['category_name'] != null) {
      cat = WasteCategory(id: 0, name: json['category_name']);
    }

    String? vName = json['village_name'];
    int? vId = json['village_id'];
    if (json['village'] is Map) {
      vName = json['village']['name'] ?? vName;
      vId = json['village']['id'] ?? vId;
    }

    return WasteReport(
      id: json['id'] ?? 0,
      reportCode: json['report_code'] ?? '',
      status: json['status'] ?? 'PENDING_VALIDATION',
      statusLabel: json['status_label'] ?? json['status'] ?? '',
      description: json['description'],
      latitude: json['latitude'] != null ? (json['latitude'] as num).toDouble() : null,
      longitude: json['longitude'] != null ? (json['longitude'] as num).toDouble() : null,
      villageId: vId,
      villageName: vName,
      category: cat,
      reporterName: json['reporter'] is Map ? json['reporter']['name'] : json['reporter_name'],
      photos: reportPhotos,
      cleanupPhotos: taskPhotos,
      statusHistories: histories,
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
      resolvedAt: json['resolved_at'] != null ? DateTime.tryParse(json['resolved_at']) : null,
    );
  }
}
