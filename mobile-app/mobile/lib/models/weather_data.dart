class WeatherData {
  final String district;
  final String city;
  final double temperature;
  final String temperatureUnit;
  final int humidity;
  final String humidityUnit;
  final double windSpeed;
  final String windSpeedUnit;
  final String condition;
  final String description;
  final String icon;
  final String fetchedAt;

  WeatherData({
    required this.district,
    required this.city,
    required this.temperature,
    required this.temperatureUnit,
    required this.humidity,
    required this.humidityUnit,
    required this.windSpeed,
    required this.windSpeedUnit,
    required this.condition,
    required this.description,
    required this.icon,
    required this.fetchedAt,
  });

  factory WeatherData.fromJson(Map<String, dynamic> json) {
    return WeatherData(
      district: json['district'] ?? 'Sumbersari',
      city: json['city'] ?? 'Jember',
      temperature: json['temperature'] is num ? (json['temperature'] as num).toDouble() : 28.0,
      temperatureUnit: json['temperature_unit'] ?? '°C',
      humidity: json['humidity'] is int ? json['humidity'] : int.tryParse(json['humidity']?.toString() ?? '75') ?? 75,
      humidityUnit: json['humidity_unit'] ?? '%',
      windSpeed: json['wind_speed'] is num ? (json['wind_speed'] as num).toDouble() : 10.0,
      windSpeedUnit: json['wind_speed_unit'] ?? 'km/h',
      condition: json['condition'] ?? 'Cerah Berawan',
      description: json['description'] ?? 'Kondisi cuaca cerah berawan',
      icon: json['icon'] ?? 'partly_cloudy',
      fetchedAt: json['fetched_at'] ?? '',
    );
  }
}
