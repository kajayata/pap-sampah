import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/core/theme/app_theme.dart';
import 'package:mobile/models/cleanup_task.dart';
import 'package:mobile/models/landfill.dart';
import 'package:mobile/models/news_article.dart';
import 'package:mobile/models/user.dart';
import 'package:mobile/models/waste_bank.dart';
import 'package:mobile/models/waste_category.dart';
import 'package:mobile/models/waste_map_point.dart';
import 'package:mobile/models/waste_report.dart';
import 'package:mobile/models/weather_data.dart';

void main() {
  group('Mobile Models Unit Tests', () {
    test('User model parses json correctly', () {
      final json = {
        'id': 10,
        'name': 'Budi Santoso',
        'email': 'budi@sumbersari.go.id',
        'role': 'petugas_kebersihan',
        'village_id': 3,
        'village_name': 'Kebonsari',
        'phone': '081234567890',
      };

      final user = User.fromJson(json);
      expect(user.id, 10);
      expect(user.name, 'Budi Santoso');
      expect(user.isPetugas, isTrue);
      expect(user.isMasyarakat, isFalse);
      expect(user.villageName, 'Kebonsari');
    });

    test('WasteCategory parses json correctly', () {
      final json = {
        'id': 1,
        'name': 'Sampah Plastik',
        'description': 'Limbah plastik anorganik',
      };

      final cat = WasteCategory.fromJson(json);
      expect(cat.id, 1);
      expect(cat.name, 'Sampah Plastik');
    });

    test('WasteReport parses status and getters correctly', () {
      final json = <String, dynamic>{
        'id': 101,
        'report_code': 'REP-202609-001',
        'status': 'RESOLVED',
        'status_label': 'Selesai Dibersihkan',
        'latitude': -8.1724,
        'longitude': 113.7222,
        'description': 'Tumpukan sampah liar di tepi selokan',
        'photos': [
          {'id': 1, 'url': 'http://127.0.0.1:8000/storage/reports/before.jpg', 'type': 'BEFORE'},
        ],
        'cleanup_task': {
          'photos': [
            {'id': 2, 'url': 'http://127.0.0.1:8000/storage/cleanup/after.jpg', 'type': 'AFTER'},
          ],
        },
      };

      final report = WasteReport.fromJson(json);
      expect(report.id, 101);
      expect(report.isResolved, isTrue);
      expect(report.photos.length, 1);
      expect(report.cleanupPhotos.length, 1);
      expect(report.cleanupPhotos.first.isAfter, isTrue);
    });

    test('CleanupTask enforces notes and worker status', () {
      final json = <String, dynamic>{
        'id': 42,
        'status': 'IN_PROGRESS',
        'status_label': 'Sedang Dikerjakan',
        'notes': 'Bawa sekop, karung, dan sarung tangan tebal',
        'my_status': 'ACCEPTED',
        'my_status_label': 'Telah Diterima',
        'assigned_by': {'name': 'Admin Desa Kebonsari', 'phone': '0811111111'},
        'photos': [
          {'id': 5, 'url': 'http://test.com/before.jpg', 'type': 'BEFORE'},
          {'id': 6, 'url': 'http://test.com/after.jpg', 'type': 'AFTER'},
        ],
      };

      final task = CleanupTask.fromJson(json);
      expect(task.id, 42);
      expect(task.notes, contains('sarung tangan'));
      expect(task.isMyAssignmentAccepted, isTrue);
      expect(task.beforePhotos.length, 1);
      expect(task.afterPhotos.length, 1);
    });

    test('WasteMapPoint, WasteBank, Landfill parse correctly', () {
      final mapPt = WasteMapPoint.fromJson({
        'id': 1,
        'report_code': 'REP-001',
        'latitude': -8.17,
        'longitude': 113.72,
        'status': 'PENDING_VALIDATION',
        'status_label': 'Menunggu Validasi',
        'is_resolved': false,
      });
      expect(mapPt.id, 1);
      expect(mapPt.isResolved, isFalse);

      final bank = WasteBank.fromJson({
        'id': 5,
        'name': 'Bank Sampah Resik Sejahtera',
        'address': 'Jl. Mastrip No. 12',
        'phone': '08987654321',
        'latitude': -8.16,
        'longitude': 113.71,
      });
      expect(bank.name, 'Bank Sampah Resik Sejahtera');

      final landfill = Landfill.fromJson({
        'id': 1,
        'name': 'TPA Sumbersari',
        'latitude': -8.18,
        'longitude': 113.73,
      });
      expect(landfill.name, 'TPA Sumbersari');
    });

    test('NewsArticle and WeatherData parse correctly', () {
      final news = NewsArticle.fromJson({
        'id': 7,
        'title': 'Tips Memilah Sampah Rumah Tangga',
        'slug': 'tips-memilah-sampah',
        'content': 'Langkah awal 3R...',
        'author': {'name': 'Admin DLH'},
      });
      expect(news.title, contains('Memilah Sampah'));
      expect(news.authorName, 'Admin DLH');

      final weather = WeatherData.fromJson({
        'temperature': 29.5,
        'condition': 'Cerah',
        'humidity': 70,
        'wind_speed': 12.0,
      });
      expect(weather.temperature, 29.5);
      expect(weather.humidity, 70);
    });

    test('AppTheme brand colors match ui-context.md', () {
      expect(AppTheme.emphasis.toARGB32(), 0xFF5B7E3C);
      expect(AppTheme.accentSecondary.toARGB32(), 0xFFFFD65A);
      expect(AppTheme.accentWarning.toARGB32(), 0xFFFF9D23);
      expect(AppTheme.accentDanger.toARGB32(), 0xFFEA5252);
    });
  });
}
