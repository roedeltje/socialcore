<?php

namespace App\Handlers;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;

class NotificationsHandler extends Controller
{
    private $db;
    
    public function __construct()
    {
        // Skip parent::__construct() zoals bij Privacy en Security
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon alle notificaties voor de ingelogde gebruiker
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Haal alle notificaties op (zowel gelezen als ongelezen)
        $notifications = $this->getNotifications($userId);
        
        // Haal het aantal ongelezen notificaties op
        $unreadCount = $this->getUnreadCount($userId);
        
        $data = [
            'title' => 'Meldingen',
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        // Clear messages na tonen
        unset($_SESSION['success'], $_SESSION['error']);
        
        // Gebruik thema-engine zoals Privacy en Security
        $this->view('notifications/index', $data);
    }

    /**
     * Markeer een specifieke notificatie als gelezen
     */
    public function markAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if (!$notificationId) {
            $this->jsonResponse(['success' => false, 'message' => 'No notification ID provided']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([$notificationId, $userId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification marked as read']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Notification not found or already read']);
            }
        } catch (\Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Markeer alle notificaties als gelezen
     */
    public function markAllAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $userId = $_SESSION['user_id'];

        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = ? AND is_read = 0
            ");
            $success = $stmt->execute([$userId]);
            
            if ($success) {
                $affectedRows = $stmt->rowCount();
                $this->jsonResponse([
                    'success' => true, 
                    'message' => "Marked {$affectedRows} notifications as read",
                    'count' => $affectedRows
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update notifications']);
            }
        } catch (\Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Haal het aantal ongelezen notificaties op
     */
    public function getUnreadCount($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }
        
        if (!$userId) {
            return 0;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE user_id = ? AND is_read = 0
            ");
            $stmt->execute([$userId]);
            
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * API endpoint voor het ophalen van notification count (voor AJAX)
     */
    public function getCountApi()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['count' => 0]);
            return;
        }

        $count = $this->getUnreadCount();
        $this->jsonResponse(['count' => $count]);
    }

    /**
     * Verwijder een notificatie
     */
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if (!$notificationId) {
            $this->jsonResponse(['success' => false, 'message' => 'No notification ID provided']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([$notificationId, $userId]);
            
            if ($success && $stmt->rowCount() > 0) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification deleted']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Notification not found']);
            }
        } catch (\Exception $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Maak een nieuwe notificatie aan (helper functie voor andere controllers)
     */
    public static function create($userId, $type, $title, $message, $actionUrl = null, $relatedUserId = null, $relatedPostId = null, $relatedCommentId = null, $relatedFriendshipId = null)
    {
        try {
            $db = Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                INSERT INTO notifications (
                    user_id, type, related_user_id, related_post_id, 
                    related_comment_id, related_friendship_id, title, message, action_url
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $userId, $type, $relatedUserId, $relatedPostId, 
                $relatedCommentId, $relatedFriendshipId, $title, $message, $actionUrl
            ]);
        } catch (\Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Haal alle notificaties op voor een gebruiker
     */
    private function getNotifications($userId, $limit = 50)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    n.*,
                    ru.username as related_username,
                    COALESCE(rup.display_name, ru.username) as related_display_name,
                    rup.avatar as related_avatar
                FROM notifications n
                LEFT JOIN users ru ON n.related_user_id = ru.id
                LEFT JOIN user_profiles rup ON ru.id = rup.user_id
                WHERE n.user_id = ?
                ORDER BY n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format de notificaties voor weergave
            foreach ($notifications as &$notification) {
                $notification['formatted_date'] = $this->formatDate($notification['created_at']);
                $notification['from_avatar'] = $this->getNotificationAvatarUrl($notification['related_avatar']);
                $notification['from_name'] = $notification['related_display_name'] ?? 'Systeem';
                $notification['from_username'] = $notification['related_username'] ?? '';
                
                // Voeg type-specifieke data toe
                $notification = $this->enrichNotificationData($notification);
            }

            return $notifications;
        } catch (\Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verrijk notificatie data met type-specifieke informatie
     */
    private function enrichNotificationData($notification)
    {
        switch ($notification['type']) {
            case 'friend_request':
                $notification['icon'] = '👥';
                $notification['icon_class'] = 'bg-green-100 text-green-600';
                $notification['action_url'] = "/?route=profile&user={$notification['related_username']}";
                break;
            case 'friend_accepted':
                $notification['icon'] = '✅';
                $notification['icon_class'] = 'bg-blue-100 text-blue-600';
                $notification['action_url'] = "/?route=profile&user={$notification['related_username']}";
                break;
            case 'post_like':
                $notification['icon'] = '❤️';
                $notification['icon_class'] = 'bg-red-100 text-red-600';
                if ($notification['related_post_id']) {
                    $notification['post_preview'] = $this->getPostPreview($notification['related_post_id']);
                    // NIEUWE PERMALINK URL in plaats van profiel link
                    $notification['action_url'] = "/?route=post&id={$notification['related_post_id']}";
                }
                break;
            case 'post_comment':
                $notification['icon'] = '💬';
                $notification['icon_class'] = 'bg-blue-100 text-blue-600';
                if ($notification['related_post_id']) {
                    $notification['post_preview'] = $this->getPostPreview($notification['related_post_id']);
                    // NIEUWE PERMALINK URL direct naar comments sectie
                    $notification['action_url'] = "/?route=post&id={$notification['related_post_id']}#comments";
                }
                if ($notification['related_comment_id']) {
                    $notification['comment_preview'] = $this->getCommentPreview($notification['related_comment_id']);
                }
                break;
            case 'system':
                $notification['icon'] = '🔔';
                $notification['icon_class'] = 'bg-gray-100 text-gray-600';
                break;
            default:
                $notification['icon'] = '🔔';
                $notification['icon_class'] = 'bg-gray-100 text-gray-600';
        }

        return $notification;
    }

    /**
     * Helper functie om post preview te krijgen
     */
    private function getPostPreview($postId, $maxLength = 50)
    {
        try {
            // Haal post content op
            $stmt = $this->db->prepare("SELECT content FROM posts WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$postId]);
            $content = $stmt->fetchColumn();
            
            // Als er tekst content is, toon die
            if ($content && trim($content) !== '') {
                return substr($content, 0, $maxLength) . (strlen($content) > $maxLength ? '...' : '');
            }
            
            // Als er geen tekst is, check voor media in post_media tabel
            $stmt = $this->db->prepare("
                SELECT media_type, file_name 
                FROM post_media 
                WHERE post_id = ? 
                ORDER BY display_order ASC 
                LIMIT 1
            ");
            $stmt->execute([$postId]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($media) {
                switch ($media['media_type']) {
                    case 'image':
                        return '📷 [Afbeelding]';
                    case 'video':
                        return '🎥 [Video]';
                    case 'audio':
                        return '🎵 [Audio]';
                    default:
                        return '📎 [Media bestand]';
                }
            }
            
            // Geen content en geen media
            return '[Leeg bericht]';
            
        } catch (\Exception $e) {
            error_log("Error getting post preview: " . $e->getMessage());
        }
        
        return 'Bericht niet beschikbaar';
    }

    /**
     * Helper functie om comment preview te krijgen
     */
    private function getCommentPreview($commentId, $maxLength = 100)
    {
        try {
            $stmt = $this->db->prepare("SELECT content FROM post_comments WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$commentId]);
            $content = $stmt->fetchColumn();
            
            if ($content) {
                return substr($content, 0, $maxLength) . (strlen($content) > $maxLength ? '...' : '');
            }
        } catch (\Exception $e) {
            error_log("Error getting comment preview: " . $e->getMessage());
        }
        
        return 'Reactie niet beschikbaar';
    }

    /**
     * Helper functie om de username van de post eigenaar te krijgen
     */
    private function getPostOwnerUsername($postId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.username 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$postId]);
            $username = $stmt->fetchColumn();
            
            return $username ?: 'unknown';
        } catch (\Exception $e) {
            error_log("Error getting post owner username: " . $e->getMessage());
            return 'unknown';
        }
    }

    /**
     * Helper functie om avatar URL te krijgen voor notificaties
     */
    private function getNotificationAvatarUrl($avatarPath)
    {
        if (empty($avatarPath)) {
            return base_url('theme-assets/default/images/default-avatar.png');
        }
        
        if (str_starts_with($avatarPath, 'http')) {
            return $avatarPath;
        }
        
        if (str_starts_with($avatarPath, 'theme-assets')) {
            return base_url($avatarPath);
        }
        
        return base_url('uploads/' . $avatarPath);
    }

    /**
     * Format datetime voor weergave
     */
    private function formatDate($datetime)
    {
        if (empty($datetime) || $datetime === null) {
            return 'Onbekende tijd';
        }
        
        try {
            $date = new \DateTime($datetime);
            $now = new \DateTime();
            $diff = $now->diff($date);
            
            if ($diff->days == 0) {
                if ($diff->h > 0) {
                    return $diff->h . ' uur geleden';
                } elseif ($diff->i > 0) {
                    return $diff->i . ' minuten geleden';
                } else {
                    return 'Net nu';
                }
            } elseif ($diff->days == 1) {
                return 'Gisteren om ' . $date->format('H:i');
            } else {
                return $date->format('d-m-Y H:i');
            }
        } catch (\Exception $e) {
            error_log("Error formatting date '$datetime': " . $e->getMessage());
            return 'Onbekende tijd';
        }
    }

    /**
     * Helper functie voor JSON responses
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ========================================
    // 🔔 NOTIFICATIONSHANDLER API METHODS
    // ========================================

    /**
     * API: Get all notifications for user
     */
    public function apiGetNotifications()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $limit = intval($_GET['limit'] ?? 20);
        $offset = intval($_GET['offset'] ?? 0);
        $type = $_GET['type'] ?? null; // friend_request, like, comment, message
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $whereClause = "WHERE n.user_id = ?";
            $params = [$userId];
            
            if ($type) {
                $whereClause .= " AND n.type = ?";
                $params[] = $type;
            }
            
            $stmt = $db->prepare("
                SELECT n.*, 
                    u.username as from_username,
                    COALESCE(up.display_name, u.username) as from_display_name,
                    up.avatar as from_avatar
                FROM notifications n
                LEFT JOIN users u ON n.from_user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                {$whereClause}
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format notifications
            foreach ($notifications as &$notification) {
                $notification['from_avatar_url'] = get_avatar_url($notification['from_avatar']);
                $notification['time_ago'] = format_time_ago($notification['created_at']);
                $notification['is_read'] = (bool)$notification['is_read'];
                
                // Format notification message
                $notification['formatted_message'] = $this->formatNotificationMessage($notification);
                
                // Add action URL
                $notification['action_url'] = $this->getNotificationActionUrl($notification);
            }
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'has_more' => count($notifications) >= $limit
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Get unread notification count
     */
    public function apiGetUnreadCount()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                SELECT COUNT(*) as unread_count 
                FROM notifications 
                WHERE user_id = ? AND is_read = 0
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'unread_count' => intval($result['unread_count'])
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error getting unread count: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Mark specific notification as read
     */
    public function apiMarkAsRead()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
            exit;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$notificationId, $userId]);
            $affectedRows = $stmt->rowCount();
            
            echo json_encode([
                'success' => $affectedRows > 0,
                'message' => $affectedRows > 0 ? 'Notification marked as read' : 'Notification not found'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Mark all notifications as read
     */
    public function apiMarkAllAsRead()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $type = $_POST['type'] ?? null; // Optional: mark only specific type as read
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $whereClause = "WHERE user_id = ? AND is_read = 0";
            $params = [$userId];
            
            if ($type) {
                $whereClause .= " AND type = ?";
                $params[] = $type;
            }
            
            $stmt = $db->prepare("
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                {$whereClause}
            ");
            
            $stmt->execute($params);
            $affectedRows = $stmt->rowCount();
            
            echo json_encode([
                'success' => true,
                'marked_read' => $affectedRows,
                'message' => "{$affectedRows} notifications marked as read"
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error marking notifications as read: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Delete notification
     */
    public function apiDeleteNotification()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
            exit;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                DELETE FROM notifications 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$notificationId, $userId]);
            $affectedRows = $stmt->rowCount();
            
            echo json_encode([
                'success' => $affectedRows > 0,
                'message' => $affectedRows > 0 ? 'Notification deleted' : 'Notification not found'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting notification: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Get notification statistics
     */
    public function apiGetStats()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            // Get counts by type
            $stmt = $db->prepare("
                SELECT 
                    type,
                    COUNT(*) as total,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread
                FROM notifications 
                WHERE user_id = ?
                GROUP BY type
            ");
            
            $stmt->execute([$userId]);
            $typeStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get total counts
            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_notifications,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as total_unread,
                    COUNT(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as today_notifications
                FROM notifications 
                WHERE user_id = ?
            ");
            
            $stmt->execute([$userId]);
            $totalStats = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'by_type' => $typeStats,
                    'totals' => $totalStats
                ]
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error getting notification stats: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Create new notification (for system use)
     */
    public function apiCreateNotification()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
            exit;
        }
        
        $targetUserId = $_POST['target_user_id'] ?? null;
        $type = $_POST['type'] ?? null;
        $message = $_POST['message'] ?? null;
        $fromUserId = $_SESSION['user_id'];
        $relatedId = $_POST['related_id'] ?? null; // post_id, comment_id, etc.
        
        if (!$targetUserId || !$type || !$message) {
            echo json_encode(['success' => false, 'message' => 'Target user, type and message required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                INSERT INTO notifications (
                    user_id, from_user_id, type, message, related_id, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$targetUserId, $fromUserId, $type, $message, $relatedId]);
            $notificationId = $db->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'notification_id' => $notificationId,
                'message' => 'Notification created successfully'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error creating notification: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    // ========================================
    // 🔧 HELPER METHODS
    // ========================================

    /**
     * Format notification message based on type
     */
    private function formatNotificationMessage($notification)
    {
        $fromName = $notification['from_display_name'] ?? $notification['from_username'] ?? 'Someone';
        
        switch ($notification['type']) {
            case 'friend_request':
                return "{$fromName} heeft je een vriendschapsverzoek gestuurd";
                
            case 'friend_accept':
                return "{$fromName} heeft je vriendschapsverzoek geaccepteerd";
                
            case 'like':
                return "{$fromName} vindt je bericht leuk";
                
            case 'comment':
                return "{$fromName} heeft gereageerd op je bericht";
                
            case 'comment_like':
                return "{$fromName} vindt je reactie leuk";
                
            case 'message':
                return "{$fromName} heeft je een bericht gestuurd";
                
            case 'mention':
                return "{$fromName} heeft je genoemd in een bericht";
                
            case 'follow':
                return "{$fromName} volgt je nu";
                
            default:
                return $notification['message'] ?? 'Nieuwe notificatie';
        }
    }

    /**
     * Get action URL for notification
     */
    private function getNotificationActionUrl($notification)
    {
        switch ($notification['type']) {
            case 'friend_request':
                return base_url('?route=friends/requests');
                
            case 'friend_accept':
                return base_url('?route=profile&user=' . $notification['from_username']);
                
            case 'like':
            case 'comment':
                if ($notification['related_id']) {
                    return base_url('?route=post&id=' . $notification['related_id']);
                }
                return base_url('?route=feed');
                
            case 'message':
                return base_url('?route=messages&user=' . $notification['from_username']);
                
            case 'mention':
                if ($notification['related_id']) {
                    return base_url('?route=post&id=' . $notification['related_id']);
                }
                return base_url('?route=feed');
                
            default:
                return base_url('?route=notifications');
        }
    }
}