import 'package:flutter/services.dart';

class CurrencyInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
      TextEditingValue oldValue, TextEditingValue newValue) {
    if (newValue.text.isEmpty) {
      return newValue.copyWith(text: '');
    }

    // Remove any non-digit characters
    String cleanText = newValue.text.replaceAll(RegExp(r'[^\d]'), '');
    
    if (cleanText.isEmpty) {
      return newValue.copyWith(text: '');
    }

    // Remove leading zeros, but keep '0' if it's the only digit
    cleanText = cleanText.replaceFirst(RegExp(r'^0+'), '');
    if (cleanText.isEmpty) {
      cleanText = '0';
    }

    // Manual thousands separator with dots
    String formatted = '';
    int count = 0;
    for (int i = cleanText.length - 1; i >= 0; i--) {
      formatted = cleanText[i] + formatted;
      count++;
      if (count % 3 == 0 && i != 0) {
        formatted = '.$formatted';
      }
    }

    // Hitung posisi kursor baru
    int cursorDiff = formatted.length - oldValue.text.length;
    int newCursorPosition = oldValue.selection.baseOffset + cursorDiff;
    
    if (newCursorPosition < 0) newCursorPosition = 0;
    if (newCursorPosition > formatted.length) newCursorPosition = formatted.length;

    // Jika pengguna baru saja mengetik di akhir
    if (newValue.selection.baseOffset == newValue.text.length) {
      newCursorPosition = formatted.length;
    }

    return TextEditingValue(
      text: formatted,
      selection: TextSelection.collapsed(offset: newCursorPosition),
    );
  }
}
