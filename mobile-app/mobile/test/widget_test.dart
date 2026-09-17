import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/main.dart';

void main() {
  testWidgets('App renders splash screen smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const PapSampahApp());
    expect(find.text('Kecamatan Sumbersari — Jember'), findsOneWidget);
  });
}
