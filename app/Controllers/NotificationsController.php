<?php

namespace App\Controllers;

use App\Database\Database;
use App\Auth\Auth;
use PDO;

class NotificationsController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon alle notificaties voor de ingelogde gebruiker
     */
    public function index()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Haal het aantal ongelezen notificaties op VOORDAT we ze markeren
        $unreadCount = $this->getUnreadCount($userId);
        
        // Haal alle notificaties op voor deze gebruiker
        $notifications = $this->getNotifications($userId);
        
        // VERPLAATST: Markeer alle notificaties als gelezen NA het ophalen
        // (dit gebeurt nu via JavaScript of een aparte AJAX call)
        
        $data = [
            'title' => 'Meldingen',
            'notifications' => $notifications,
            'unread_count' => $unreadCount // Toon het echte aantal ongelezen
        ];
        
        $this->view('notifications/index', $data);
    }

    /**
     * Markeer alle notificaties als gelezen (via AJAX)
     */
    public function markAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $this->markAllAsRead($userId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    /**
     * Haal het aantal ongelezen notificaties op (voor in de navigatie)
     */
    public function getUnreadCount($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }
        
        if (!$userId) {
            return 0;
        }

        // Check of gebruiker alle notificaties als gelezen heeft gemarkeerd
        $readAt = $_SESSION['notifications_read_at'] ?? 0;

        try {
            // Tel vriendschapsverzoeken die NIEUWER zijn dan de "gelezen" timestamp
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM friendships 
                WHERE friend_id = ? 
                AND status = 'pending'
                AND UNIX_TIMESTAMP(created_at) > ?
            ");
            $stmt->execute([$userId, $readAt]);
            
            return $stmt->fetchColumn();

        } catch (\Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Haal alle notificaties op voor een gebruiker
     */
    private function getNotifications($userId, $limit = 50)
    {
        $notifications = [];

        try {
            // 1. Vriendschapsverzoeken
            $stmt = $this->db->prepare("
                SELECT 
                    f.id,
                    f.created_at,
                    'friend_request' as type,
                    u.id as from_user_id,
                    u.username as from_username,
                    COALESCE(up.display_name, u.username) as from_name,
                    up.avatar as from_avatar
                FROM friendships f
                JOIN users u ON f.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE f.friend_id = ? AND f.status = 'pending'
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$userId]);
            $friendRequests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($friendRequests as $friendRequest) { // ← Was $request, nu $friendRequest
            $notifications[] = [
                'id' => 'friend_' . $friendRequest['id'],
                'type' => 'friend_request',
                'created_at' => $friendRequest['created_at'],
                'formatted_date' => $this->formatDate($friendRequest['created_at']), // ← Gecorrigeerd
                'from_user_id' => $friendRequest['from_user_id'],
                'from_username' => $friendRequest['from_username'],
                'from_name' => $friendRequest['from_name'],
                'from_avatar' => $this->getAvatarUrl($friendRequest['from_avatar']),
                'message' => $friendRequest['from_name'] . ' heeft je een vriendschapsverzoek gestuurd',
                'action_url' => base_url('friends/requests'),
                'is_read' => false
    ];
            }

            // 2. Nieuwe likes op eigen posts (laatste 7 dagen)
            $stmt = $this->db->prepare("
                SELECT 
                    pl.id,
                    pl.created_at,
                    'post_like' as type,
                    u.id as from_user_id,
                    u.username as from_username,
                    COALESCE(up.display_name, u.username) as from_name,
                    up.avatar as from_avatar,
                    p.id as post_id,
                    SUBSTRING(p.content, 1, 50) as post_preview
                FROM post_likes pl
                JOIN users u ON pl.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                JOIN posts p ON pl.post_id = p.id
                WHERE p.user_id = ? 
                AND pl.user_id != ? 
                AND pl.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY pl.created_at DESC
                LIMIT 20
            ");
            $stmt->execute([$userId, $userId]);
            $likes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($likes as $like) {
                $notifications[] = [
                    'id' => 'like_' . $like['id'],
                    'type' => 'post_like',
                    'created_at' => $like['created_at'],
                    'formatted_date' => $this->formatDate($like['created_at']),
                    'from_user_id' => $like['from_user_id'],
                    'from_username' => $like['from_username'],
                    'from_name' => $like['from_name'],
                    'from_avatar' => $this->getAvatarUrl($like['from_avatar']),
                    'message' => $like['from_name'] . ' vindt je bericht leuk',
                    'action_url' => base_url('profile?tab=krabbels'),
                    'post_preview' => $like['post_preview'],
                    'is_read' => false
                ];
            }

            // 3. Nieuwe comments op eigen posts (laatste 7 dagen)
            $stmt = $this->db->prepare("
                SELECT 
                    pc.id,
                    pc.created_at,
                    'post_comment' as type,
                    u.id as from_user_id,
                    u.username as from_username,
                    COALESCE(up.display_name, u.username) as from_name,
                    up.avatar as from_avatar,
                    p.id as post_id,
                    SUBSTRING(p.content, 1, 50) as post_preview,
                    SUBSTRING(pc.content, 1, 100) as comment_preview
                FROM post_comments pc
                JOIN users u ON pc.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                JOIN posts p ON pc.post_id = p.id
                WHERE p.user_id = ? 
                AND pc.user_id != ? 
                AND pc.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY pc.created_at DESC
                LIMIT 20
            ");
            $stmt->execute([$userId, $userId]);
            $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($comments as $comment) {
                $notifications[] = [
                    'id' => 'comment_' . $comment['id'],
                    'type' => 'post_comment',
                    'created_at' => $comment['created_at'],
                    'formatted_date' => $this->formatDate($comment['created_at']),
                    'from_user_id' => $comment['from_user_id'],
                    'from_username' => $comment['from_username'],
                    'from_name' => $comment['from_name'],
                    'from_avatar' => $this->getAvatarUrl($comment['from_avatar']),
                    'message' => $comment['from_name'] . ' reageerde op je bericht',
                    'action_url' => base_url('profile?tab=krabbels'),
                    'post_preview' => $comment['post_preview'],
                    'comment_preview' => $comment['comment_preview'],
                    'is_read' => false
                ];
            }

        } catch (\Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
        }

        // Sorteer alle notificaties op datum (nieuwste eerst)
        usort($notifications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($notifications, 0, $limit);
    }

    /**
     * Markeer alle notificaties als gelezen (voor toekomstige implementatie)
     */
    private function markAllAsRead($userId)
    {
        // Voor nu doen we nog niets, maar later kunnen we hier 
        // een notifications tabel gebruiken om gelezen status bij te houden
        return true;
    }

    /**
     * Helper functie om avatar URL te krijgen
     */
    private function getAvatarUrl($avatarPath)
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
        // Controleer op null/empty waarden
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
}