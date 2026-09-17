class Landfill {
  final int id;
  final String name;
  final String? address;
  final String? description;
  final bool isActive;
  final double latitude;
  final double longitude;
  final int? villageId;
  final String? villageName;

  Landfill({
    required this.id,
    required this.name,
    this.address,
    this.description,
    required this.isActive,
    required this.latitude,
    required this.longitude,
    this.villageId,
    this.villageName,
  });

  factory Landfill.fromJson(Map<String, dynamic> json) {
    String? villageName;
    if (json['village'] is Map) {
      villageName = json['village']['name'];
    }

    return Landfill(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      name: json['name'] ?? '',
      address: json['address'],
      description: json['description'],
      isActive: json['is_active'] ?? true,
      latitude: json['latitude'] is num ? (json['latitude'] as num).toDouble() : double.tryParse(json['latitude']?.toString() ?? '0') ?? 0.0,
      longitude: json['longitude'] is num ? (json['longitude'] as num).toDouble() : double.tryParse(json['longitude']?.toString() ?? '0') ?? 0.0,
      villageId: json['village_id'] is int ? json['village_id'] : int.tryParse(json['village_id']?.toString() ?? ''),
      villageName: villageName ?? json['village_name'],
    );
  }
}
