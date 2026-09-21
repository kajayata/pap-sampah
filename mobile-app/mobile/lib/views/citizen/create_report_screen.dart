import 'dart:io';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/waste_category.dart';
import '../../providers/report_provider.dart';

class CreateReportScreen extends StatefulWidget {
  const CreateReportScreen({super.key});

  @override
  State<CreateReportScreen> createState() => _CreateReportScreenState();
}

class _CreateReportScreenState extends State<CreateReportScreen> {
  final _formKey = GlobalKey<FormState>();
  final _descController = TextEditingController();
  final _picker = ImagePicker();

  File? _selectedImage;
  WasteCategory? _selectedCategory;
  Position? _currentPosition;
  bool _isGettingLocation = false;
  String? _locationStatus;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReportProvider>().loadCategories();
      _getCurrentLocation();
    });
  }

  @override
  void dispose() {
    _descController.dispose();
    super.dispose();
  }

  Future<void> _pickImage(ImageSource source) async {
    try {
      final picked = await _picker.pickImage(
        source: source,
        maxWidth: 1600,
        maxHeight: 1600,
        imageQuality: 85,
      );
      if (picked != null) {
        setState(() => _selectedImage = File(picked.path));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memilih foto: $e')),
        );
      }
    }
  }

  Future<void> _getCurrentLocation() async {
    setState(() {
      _isGettingLocation = true;
      _locationStatus = 'Mendeteksi koordinat GPS...';
    });

    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          _isGettingLocation = false;
          _locationStatus = 'Layanan lokasi (GPS) tidak aktif';
        });
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          setState(() {
            _isGettingLocation = false;
            _locationStatus = 'Izin lokasi ditolak';
          });
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        setState(() {
          _isGettingLocation = false;
          _locationStatus = 'Izin lokasi ditolak permanen';
        });
        return;
      }

      final position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(accuracy: LocationAccuracy.high),
      );

      setState(() {
        _currentPosition = position;
        _isGettingLocation = false;
        _locationStatus = 'Koordinat: ${position.latitude.toStringAsFixed(5)}, ${position.longitude.toStringAsFixed(5)}';
      });
    } catch (e) {
      setState(() {
        _isGettingLocation = false;
        _locationStatus = 'Gagal mendeteksi lokasi otomatis: $e';
      });
    }
  }

  Future<void> _handleSubmit() async {
    if (_selectedImage == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan ambil foto bukti sampah terlebih dahulu!'),
          backgroundColor: AppTheme.accentDanger,
        ),
      );
      return;
    }

    if (_selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan pilih kategori jenis sampah!'),
          backgroundColor: AppTheme.accentDanger,
        ),
      );
      return;
    }

    if (_currentPosition == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Koordinat lokasi GPS diperlukan. Tekan tombol ambil koordinat.'),
          backgroundColor: AppTheme.accentDanger,
        ),
      );
      return;
    }

    if (!_formKey.currentState!.validate()) return;

    final provider = context.read<ReportProvider>();
    final result = await provider.submitReport(
      photoFile: _selectedImage!,
      categoryId: _selectedCategory!.id,
      latitude: _currentPosition!.latitude,
      longitude: _currentPosition!.longitude,
      description: _descController.text.trim(),
    );

    if (!mounted) return;

    if (result != null) {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
          title: const Row(
            children: [
              Icon(Icons.check_circle_rounded, color: AppTheme.emphasis, size: 28),
              SizedBox(width: 10),
              Text('Laporan Terkirim!'),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Terima kasih atas kepedulian Anda terhadap lingkungan.'),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.emphasis.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Kode Laporan:', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                    Text(
                      result.reportCode,
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.emphasis),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Laporan akan segera diverifikasi oleh pihak Kelurahan terkait.',
                style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
              ),
            ],
          ),
          actions: [
            ElevatedButton(
              onPressed: () {
                Navigator.pop(ctx);
                Navigator.pop(context, true);
              },
              child: const Text('Lihat Riwayat Laporan'),
            ),
          ],
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(provider.errorMessage ?? 'Gagal mengirimkan laporan.'),
          backgroundColor: AppTheme.accentDanger,
          duration: const Duration(seconds: 4),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final categories = context.watch<ReportProvider>().categories;
    final isSubmitting = context.watch<ReportProvider>().isSubmitting;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Lapor Sampah Liar'),
      ),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(20),
          children: [
            // Photo Picker Section
            const Text(
              '1. Foto Kondisi Sampah',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 6),
            const Text(
              'Ambil foto yang memperlihatkan tumpukan sampah dan sekitarnya secara jelas.',
              style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
            ),
            const SizedBox(height: 12),

            if (_selectedImage != null)
              Stack(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(20),
                    child: Image.file(
                      _selectedImage!,
                      height: 220,
                      width: double.infinity,
                      fit: BoxFit.cover,
                    ),
                  ),
                  Positioned(
                    top: 10,
                    right: 10,
                    child: CircleAvatar(
                      backgroundColor: Colors.black54,
                      radius: 18,
                      child: IconButton(
                        icon: const Icon(Icons.close, size: 18, color: Colors.white),
                        onPressed: () => setState(() => _selectedImage = null),
                      ),
                    ),
                  ),
                ],
              )
            else
              Row(
                children: [
                  Expanded(
                    child: GestureDetector(
                      onTap: () => _pickImage(ImageSource.camera),
                      child: Container(
                        height: 130,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppTheme.borderDefault),
                        ),
                        child: const Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.camera_alt_rounded, size: 36, color: AppTheme.emphasis),
                            SizedBox(height: 8),
                            Text('Buka Kamera', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                            Text('Ambil langsung', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: GestureDetector(
                      onTap: () => _pickImage(ImageSource.gallery),
                      child: Container(
                        height: 130,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppTheme.borderDefault),
                        ),
                        child: const Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.photo_library_rounded, size: 36, color: Color(0xFF0D9488)),
                            SizedBox(height: 8),
                            Text('Dari Galeri', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                            Text('Pilih foto', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            const SizedBox(height: 24),

            // Location GPS Section
            const Text(
              '2. Lokasi Koordinat GPS',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 6),
            const Text(
              'Pastikan Anda berada di wilayah Kecamatan Sumbersari.',
              style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
            ),
            const SizedBox(height: 10),

            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.borderDefault),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(
                        _currentPosition != null ? Icons.check_circle : Icons.my_location_rounded,
                        color: _currentPosition != null ? AppTheme.emphasis : AppTheme.accentWarning,
                        size: 20,
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          _locationStatus ?? 'Menunggu deteksi GPS...',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: _currentPosition != null ? AppTheme.emphasis : AppTheme.textPrimary,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: _isGettingLocation ? null : _getCurrentLocation,
                      icon: _isGettingLocation
                          ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2))
                          : const Icon(Icons.refresh_rounded, size: 16),
                      label: Text(_currentPosition == null ? 'Deteksi Ulang GPS' : 'Perbarui Koordinat GPS'),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Category Section
            const Text(
              '3. Kategori Sampah',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            DropdownButtonFormField<WasteCategory>(
              initialValue: _selectedCategory,
              decoration: const InputDecoration(
                hintText: 'Pilih jenis kategori sampah',
                prefixIcon: Icon(Icons.category_outlined, size: 20),
              ),
              items: categories.map((cat) {
                return DropdownMenuItem(
                  value: cat,
                  child: Text(cat.name, style: const TextStyle(fontSize: 14)),
                );
              }).toList(),
              onChanged: (cat) => setState(() => _selectedCategory = cat),
              validator: (v) => v == null ? 'Kategori sampah wajib dipilih' : null,
            ),
            const SizedBox(height: 24),

            // Description Section
            const Text(
              '4. Deskripsi & Patokan Lokasi',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: _descController,
              maxLines: 3,
              decoration: const InputDecoration(
                hintText: 'Contoh: Di pinggir selokan dekat pos ronda, banyak plastik dan dahan kayu...',
              ),
            ),
            const SizedBox(height: 32),

            // Submit CTA Button
            ElevatedButton(
              onPressed: isSubmitting ? null : _handleSubmit,
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 16),
              ),
              child: isSubmitting
                  ? const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)),
                        SizedBox(width: 12),
                        Text('Mengirimkan Laporan...'),
                      ],
                    )
                  : const Text('Kirim Laporan Sampah', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }
}
