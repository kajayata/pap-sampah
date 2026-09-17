import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/waste_bank.dart';
import '../../models/waste_map_point.dart';
import '../../providers/public_provider.dart';
import 'report_detail_screen.dart';

class SpatialMapTab extends StatefulWidget {
  final double? targetLat;
  final double? targetLng;

  const SpatialMapTab({super.key, this.targetLat, this.targetLng});

  @override
  State<SpatialMapTab> createState() => SpatialMapTabState();
}

class SpatialMapTabState extends State<SpatialMapTab> {
  final MapController _mapController = MapController();
  final LatLng _sumbersariCenter = const LatLng(-8.1724, 113.7222);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final prov = Provider.of<PublicProvider>(context, listen: false);
      prov.loadMapPoints();
      prov.loadWasteBanks();

      if (widget.targetLat != null && widget.targetLng != null) {
        moveToCoordinates(widget.targetLat!, widget.targetLng!, zoom: 16.0);
      }
    });
  }

  void moveToCoordinates(double lat, double lng, {double zoom = 15.0}) {
    _mapController.move(LatLng(lat, lng), zoom);
  }

  Future<void> _moveToCurrentLocation() async {
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) return;
      }
      final pos = await Geolocator.getCurrentPosition();
      moveToCoordinates(pos.latitude, pos.longitude, zoom: 16.0);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal membaca lokasi GPS: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        final List<Marker> markers = [];

        // Add Waste Points Markers
        for (final pt in prov.mapPoints) {
          if (pt.latitude != 0.0 && pt.longitude != 0.0) {
            markers.add(
              Marker(
                point: LatLng(pt.latitude, pt.longitude),
                width: 44,
                height: 44,
                child: GestureDetector(
                  onTap: () => _showWastePointDetails(pt),
                  child: Container(
                    decoration: BoxDecoration(
                      color: pt.isResolved ? AppTheme.emphasis : AppTheme.danger,
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 2.5),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.25),
                          blurRadius: 6,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: Icon(
                      pt.isResolved ? Icons.check_circle_rounded : Icons.delete_outline_rounded,
                      color: Colors.white,
                      size: 22,
                    ),
                  ),
                ),
              ),
            );
          }
        }

        // Add Waste Bank Markers if filter is 'all' or 'banks'
        if (prov.mapFilter == 'all' || prov.mapFilter == 'banks') {
          for (final bank in prov.wasteBanks) {
            if (bank.latitude != 0.0 && bank.longitude != 0.0) {
              markers.add(
                Marker(
                  point: LatLng(bank.latitude, bank.longitude),
                  width: 44,
                  height: 44,
                  child: GestureDetector(
                    onTap: () => _showWasteBankDetails(bank),
                    child: Container(
                      decoration: BoxDecoration(
                        color: const Color(0xFF0F766E), // Teal
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2.5),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.25),
                            blurRadius: 6,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.recycling_rounded,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                  ),
                ),
              );
            }
          }
        }

        return Scaffold(
          body: Stack(
            children: [
              // OpenStreetMap View
              FlutterMap(
                mapController: _mapController,
                options: MapOptions(
                  initialCenter: _sumbersariCenter,
                  initialZoom: 13.5,
                  minZoom: 10.0,
                  maxZoom: 18.0,
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'com.kajayata.papsampah',
                  ),
                  MarkerLayer(markers: markers),
                ],
              ),

              // Filter Chips Bar (Floating Top)
              Positioned(
                top: 48,
                left: 16,
                right: 16,
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildFilterChip(
                        label: 'Semua',
                        isActive: prov.mapFilter == 'all',
                        onTap: () => prov.loadMapPoints(filter: 'all'),
                      ),
                      const SizedBox(width: 8),
                      _buildFilterChip(
                        label: 'Sampah Aktif',
                        icon: Icons.circle,
                        iconColor: AppTheme.danger,
                        isActive: prov.mapFilter == 'active',
                        onTap: () => prov.loadMapPoints(filter: 'active'),
                      ),
                      const SizedBox(width: 8),
                      _buildFilterChip(
                        label: 'Selesai (H+7)',
                        icon: Icons.circle,
                        iconColor: AppTheme.emphasis,
                        isActive: prov.mapFilter == 'resolved',
                        onTap: () => prov.loadMapPoints(filter: 'resolved'),
                      ),
                      const SizedBox(width: 8),
                      _buildFilterChip(
                        label: 'Bank Sampah',
                        icon: Icons.recycling_rounded,
                        iconColor: const Color(0xFF0F766E),
                        isActive: prov.mapFilter == 'banks',
                        onTap: () {
                          prov.loadMapPoints(filter: 'banks');
                        },
                      ),
                    ],
                  ),
                ),
              ),

              // Floating Controls (Right Bottom)
              Positioned(
                bottom: 24,
                right: 16,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    FloatingActionButton.small(
                      heroTag: 'refresh_map_btn',
                      backgroundColor: Colors.white,
                      foregroundColor: AppTheme.primaryColor,
                      onPressed: () {
                        prov.loadMapPoints();
                        prov.loadWasteBanks();
                      },
                      child: const Icon(Icons.refresh_rounded),
                    ),
                    const SizedBox(height: 10),
                    FloatingActionButton(
                      heroTag: 'my_location_btn',
                      backgroundColor: AppTheme.primaryColor,
                      foregroundColor: Colors.white,
                      onPressed: _moveToCurrentLocation,
                      child: const Icon(Icons.my_location_rounded),
                    ),
                  ],
                ),
              ),

              // Loading indicator overlay
              if (prov.isLoadingMap)
                Positioned(
                  top: 100,
                  left: 0,
                  right: 0,
                  child: Center(
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.1),
                            blurRadius: 6,
                          ),
                        ],
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primaryColor),
                          ),
                          SizedBox(width: 8),
                          Text('Memuat titik spasial...', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                        ],
                      ),
                    ),
                  ),
                ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildFilterChip({
    required String label,
    required bool isActive,
    required VoidCallback onTap,
    IconData? icon,
    Color? iconColor,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: isActive ? AppTheme.primaryColor : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isActive ? AppTheme.primaryColor : Colors.grey.shade300),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (icon != null) ...[
              Icon(icon, size: 12, color: isActive ? Colors.white : (iconColor ?? AppTheme.primaryColor)),
              const SizedBox(width: 6),
            ],
            Text(
              label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.bold,
                color: isActive ? Colors.white : AppTheme.textDark,
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showWastePointDetails(WasteMapPoint pt) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: pt.isResolved ? const Color(0xFFDCFCE7) : const Color(0xFFFEE2E2),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      pt.statusLabel,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: pt.isResolved ? AppTheme.emphasis : AppTheme.danger,
                      ),
                    ),
                  ),
                  const Spacer(),
                  Text(
                    pt.reportCode,
                    style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.textMuted, fontSize: 13),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                pt.categoryName ?? 'Sampah Liar',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  const Icon(Icons.location_on_rounded, size: 16, color: AppTheme.primaryColor),
                  const SizedBox(width: 4),
                  Text(
                    pt.villageName ?? 'Kecamatan Sumbersari',
                    style: const TextStyle(fontSize: 13, color: AppTheme.textMuted),
                  ),
                  if (pt.createdAtFormatted != null) ...[
                    const Text(' • ', style: TextStyle(color: AppTheme.textMuted)),
                    Text(pt.createdAtFormatted!, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                  ],
                ],
              ),
              if (pt.description != null && pt.description!.isNotEmpty) ...[
                const SizedBox(height: 10),
                Text(
                  pt.description!,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 13, color: Color(0xFF4B5563)),
                ),
              ],
              const SizedBox(height: 18),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ReportDetailScreen(reportId: pt.id),
                      ),
                    );
                  },
                  icon: const Icon(Icons.visibility_rounded, size: 18),
                  label: const Text('Buka Detail Laporan', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showWasteBankDetails(WasteBank bank) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: const Color(0xFFCCFBF1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(Icons.recycling_rounded, color: Color(0xFF0F766E), size: 20),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      bank.name,
                      style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              if (bank.address != null) ...[
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.location_on_outlined, size: 16, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(bank.address!, style: const TextStyle(fontSize: 13, color: Color(0xFF4B5563))),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
              ],
              if (bank.phone != null && bank.phone!.isNotEmpty) ...[
                Row(
                  children: [
                    const Icon(Icons.phone_rounded, size: 16, color: AppTheme.emphasis),
                    const SizedBox(width: 6),
                    Text(
                      bank.phone!,
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.emphasis),
                    ),
                  ],
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}
