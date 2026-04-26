import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class StaticPageScreen extends StatelessWidget {
  final String title;
  final String content;

  const StaticPageScreen({
    super.key,
    required this.title,
    required this.content,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        backgroundColor: const Color(0xFF9F1521),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF9F1521),
              ),
            ),
            const SizedBox(height: 20),
            Text(
              content,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 14,
                height: 1.6,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 40),
            const Divider(),
            const SizedBox(height: 20),
            Center(
              child: Text(
                'Telcopedia - Platform Jual Beli Mahasiswa Telkom',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  color: Colors.grey,
                  fontStyle: FontStyle.italic,
                ),
              ),
            ),
            const SizedBox(height: 50),
          ],
        ),
      ),
    );
  }
}
