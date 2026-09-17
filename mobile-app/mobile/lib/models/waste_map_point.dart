class WasteMapPoint {
  final int id;
  final String reportCode;
  final double latitude;
  final double longitude;
  final String status;
  final String statusLabel;
  final bool isResolved;
  final String? categoryName;
  final int? villageId;
  final String? villageName;
  final String? description;
  final String? createdAtFormatted;
  final String? resolvedAtFormatted;
  final String? primaryPhotoUrl;

  WasteMapPoint({
    required this.id,
    required this.reportCode,
    required this.latitude,
    required this.longitude,
    required this.status,
    required this.statusLabel,
    required this.isResolved,
    this.categoryName,
    this.villageId,
    this.villageName,
    this.description,
    this.createdAtFormatted,
    this.resolvedAtFormatted,
    this.primaryPhotoUrl,
  });

  factory WasteMapPoint.fromJson(Map<String, dynamic> json) {
    return WasteMapPoint(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      reportCode: json['report_code'] ?? '',
      latitude: json['latitude'] is num ? (json['latitude'] as num).toDouble() : double.tryParse(json['latitude']?.toString() ?? '0') ?? 0.0,
      longitude: json['longitude'] is num ? (json['longitude'] as num).toDouble() : double.tryParse(json['longitude']?.toString() ?? '0') ?? 0.0,
      status: json['status'] ?? 'PENDING_VALIDATION',
      statusLabel: json['status_label'] ?? 'Menunggu Validasi',
      isResolved: json['is_resolved'] ?? (json['status'] == 'RESOLVED'),
      categoryName: json['category_name'],
      villageId: json['village_id'] is int ? json['village_id'] : int.tryParse(json['village_id']?.toString() ?? ''),
      villageName: json['village_name'],
      description: json['description'],
      createdAtFormatted: json['created_at_formatted'],
      resolvedAtFormatted: json['resolved_at_formatted'],
      primaryPhotoUrl: json['primary_photo_url'],
    );
  }
}
