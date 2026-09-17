class NewsArticle {
  final int id;
  final String title;
  final String slug;
  final String? thumbnailUrl;
  final String content;
  final String? authorName;
  final String? publishedAt;

  NewsArticle({
    required this.id,
    required this.title,
    required this.slug,
    this.thumbnailUrl,
    required this.content,
    this.authorName,
    this.publishedAt,
  });

  factory NewsArticle.fromJson(Map<String, dynamic> json) {
    String? authorName;
    if (json['author'] is Map) {
      authorName = json['author']['name'];
    }

    return NewsArticle(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      thumbnailUrl: json['thumbnail_url'],
      content: json['content'] ?? '',
      authorName: authorName ?? json['author_name'],
      publishedAt: json['published_at'],
    );
  }
}
