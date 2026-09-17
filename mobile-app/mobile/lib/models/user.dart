class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final int? villageId;
  final String? villageName;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.villageId,
    this.villageName,
  });

  bool get isMasyarakat => role == 'masyarakat';
  bool get isPetugas => role == 'petugas_kebersihan';
  bool get isSuperAdmin => role == 'super_admin_kecamatan';
  bool get isAdminDesa => role == 'admin_desa';

  factory User.fromJson(Map<String, dynamic> json) {
    String roleName = 'masyarakat';
    if (json['role'] is Map && json['role']['name'] != null) {
      roleName = json['role']['name'];
    } else if (json['role'] is String) {
      roleName = json['role'];
    }

    String? villageName;
    int? villageId = json['village_id'];
    if (json['village'] is Map) {
      villageName = json['village']['name'];
      villageId = json['village']['id'] ?? villageId;
    }

    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      role: roleName,
      villageId: villageId,
      villageName: villageName,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role': role,
      'village_id': villageId,
      'village_name': villageName,
    };
  }
}
