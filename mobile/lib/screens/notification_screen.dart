import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:timeago/timeago.dart' as timeago;
import '../models/notification.dart';
import '../services/notification_service.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  final NotificationService _notificationService = NotificationService();
  List<SystemNotification> _notifications = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    final notifs = await _notificationService.getNotifications();
    setState(() {
      _notifications = notifs;
      _isLoading = false;
    });
  }

  Future<void> _markAsRead(SystemNotification notif) async {
    if (notif.isRead) return;
    final success = await _notificationService.markAsRead(notif.id);
    if (success) {
      setState(() {
        notif.isRead = true;
      });
    }
  }

  Future<void> _markAllRead() async {
    final success = await _notificationService.markAllAsRead();
    if (success) {
      setState(() {
        for (var n in _notifications) {
          n.isRead = true;
        }
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text('Notifikasi', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
        actions: [
          if (_notifications.any((n) => !n.isRead))
            TextButton(
              onPressed: _markAllRead,
              child: Text('Baca Semua', style: TextStyle(color: Color(0xFF9F1521), fontWeight: FontWeight.bold, fontSize: 12)),
            ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadNotifications,
        color: const Color(0xFF9F1521),
        child: _isLoading 
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : _notifications.isEmpty 
            ? _buildEmptyState()
            : _buildList(),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.notifications_none_rounded, size: 80, color: Colors.grey.shade300),
          const SizedBox(height: 15),
          Text('Belum ada notifikasi', style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey.shade400)),
        ],
      ),
    );
  }

  Widget _buildList() {
    return ListView.builder(
      itemCount: _notifications.length,
      padding: const EdgeInsets.symmetric(vertical: 10),
      itemBuilder: (context, index) {
        final notif = _notifications[index];
        return InkWell(
          onTap: () => _markAsRead(notif),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: notif.isRead ? Colors.white : Colors.grey.withOpacity(0.03),
              border: Border(
                bottom: BorderSide(color: Colors.grey.shade100),
                left: notif.isRead 
                    ? BorderSide.none 
                    : const BorderSide(color: Color(0xFF9F1521), width: 4),
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Stack(
                  children: [
                    _buildIcon(notif.type),
                    if (!notif.isRead)
                      Positioned(
                        right: 0,
                        top: 0,
                        child: Container(
                          width: 8,
                          height: 8,
                          decoration: BoxDecoration(
                            color: const Color(0xFF9F1521),
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 1.5),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(width: 15),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(notif.title, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14)),
                          ),
                          Text(timeago.format(notif.createdAt, locale: 'en_short'), style: TextStyle(fontSize: 11, color: Colors.grey.shade500)),
                        ],
                      ),
                      const SizedBox(height: 5),
                      Text(notif.message, style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade700, height: 1.4)),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildIcon(String type) {
    IconData icon;
    Color color;
    
    switch (type) {
      case 'product':
        icon = Icons.inventory_2_outlined;
        color = Colors.orange;
        break;
      case 'order':
        icon = Icons.shopping_bag_outlined;
        color = Colors.green;
        break;
      case 'penarikan':
        icon = Icons.account_balance_wallet_outlined;
        color = Colors.blue;
        break;
      default:
        icon = Icons.notifications_outlined;
        color = const Color(0xFF9F1521);
    }

    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        shape: BoxShape.circle,
      ),
      child: Icon(icon, color: color, size: 20),
    );
  }
}
