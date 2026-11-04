import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'core/constants/api_constants.dart';
import 'core/theme/app_theme.dart';
import 'providers/auth_provider.dart';
import 'providers/notification_provider.dart';
import 'providers/public_provider.dart';
import 'providers/report_provider.dart';
import 'providers/worker_task_provider.dart';
import 'services/notification_service.dart';
import 'views/splash_screen.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await ApiConstants.init();
  await NotificationService.init();
  runApp(const PapSampahApp());
}

class PapSampahApp extends StatelessWidget {
  const PapSampahApp({super.key});

  static const double _maxWidth = 480;
  static const Color _outerBg = Color(0xFFE9FAF6);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => ReportProvider()),
        ChangeNotifierProvider(create: (_) => PublicProvider()),
        ChangeNotifierProvider(create: (_) => WorkerTaskProvider()),
        ChangeNotifierProvider(create: (_) => NotificationProvider()),
      ],
      child: MaterialApp(
        title: 'Pap Sampah Sumbersari',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme.copyWith(
          textTheme: GoogleFonts.plusJakartaSansTextTheme(
            AppTheme.lightTheme.textTheme,
          ),
        ),
        home: const SplashScreen(),
        builder: (context, child) {
          final mq = MediaQuery.of(context);

          // Di ponsel (lebar <= 480) tidak ada perubahan.
          if (mq.size.width <= _maxWidth) return child!;

          // Di tablet/desktop: konten dipusatkan selebar 480.
          return ColoredBox(
            color: _outerBg,
            child: Center(
              child: SizedBox(
                width: _maxWidth,
                child: MediaQuery(
                  data: mq.copyWith(size: Size(_maxWidth, mq.size.height)),
                  child: ClipRect(child: child!),
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}