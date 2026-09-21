import 'package:flutter/material.dart';
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
        theme: AppTheme.lightTheme,
        home: const SplashScreen(),
      ),
    );
  }
}
