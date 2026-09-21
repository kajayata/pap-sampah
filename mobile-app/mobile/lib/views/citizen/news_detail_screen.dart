import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';
import '../../models/news_article.dart';

class NewsDetailScreen extends StatelessWidget {
  final NewsArticle article;

  const NewsDetailScreen({super.key, required this.article});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Edukasi & Berita', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 17)),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Featured Image
            if (article.thumbnailUrl != null)
              Image.network(
                article.thumbnailUrl!,
                width: double.infinity,
                height: 240,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => Container(
                  height: 200,
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  child: const Center(
                    child: Icon(Icons.menu_book_rounded, size: 64, color: AppTheme.primaryColor),
                  ),
                ),
              )
            else
              Container(
                height: 180,
                width: double.infinity,
                color: AppTheme.primaryColor.withValues(alpha: 0.1),
                child: const Center(
                  child: Icon(Icons.menu_book_rounded, size: 64, color: AppTheme.primaryColor),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title
                  Text(
                    article.title,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.textDark,
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Metadata Row
                  Row(
                    children: [
                      const CircleAvatar(
                        radius: 14,
                        backgroundColor: AppTheme.secondaryColor,
                        child: Icon(Icons.person, size: 16, color: Colors.black87),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        article.authorName ?? 'Admin DLH Jember',
                        style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppTheme.textDark),
                      ),
                      const Spacer(),
                      if (article.publishedAt != null) ...[
                        const Icon(Icons.calendar_today_rounded, size: 14, color: AppTheme.textMuted),
                        const SizedBox(width: 4),
                        Text(
                          article.publishedAt!.substring(0, 10),
                          style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                        ),
                      ],
                    ],
                  ),
                  const Divider(height: 32),

                  // Article Content
                  Text(
                    article.content,
                    style: const TextStyle(
                      fontSize: 15,
                      height: 1.7,
                      color: Color(0xFF374151),
                    ),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
