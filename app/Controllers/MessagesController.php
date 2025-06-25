<?php

namespace App\Controllers;
use Exception;

// FORCE IMMEDIATE DEBUG - direct na namespace
file_put_contents('/tmp/controller_loaded_' . time() . '.txt', 
    'MessagesController loaded at: ' . date('Y-m-d H:i:s')
);

use App\Database\Database;
use App\Auth\Auth;
use PDO;
use App\Helpers\Logger;
use App\Controllers\PrivacyController;

class MessagesController extends Controller
{
    private $db;
    
    public function __construct()
    {
    // FORCE debug logging - dit moet lukken
    if (!is_dir('/tmp')) {
        mkdir('/tmp', 0777, true);
    }
    
    $this->db = Database::getInstance()->getPdo();
    
    // Initialize logger
    Logger::init();
    }

    /**
     * Inbox - Toon alle gesprekken van de gebruiker
     */
    public function index()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Haal alle gesprekken op (gegroepeerd per gebruiker)
        $conversations = $this->getConversations($userId);
        
        // Tel ongelezen berichten
        $unreadCount = $this->getUnreadCount($userId);
        
        $data = [
            'title' => 'Berichten',
            'conversations' => $conversations,
            'unread_count' => $unreadCount,
            'current_user_id' => $userId
        ];

        // // Check welk thema actief is
        // $activeTheme = $_SESSION['theme'] ?? 'default'; // Of hoe je het thema bepaalt
        
        // if ($activeTheme === 'twitter') {
        //     $data['pageCSS'] = [
        //         'theme-assets/twitter/css/components.css'
        //     ];
        // }
        
        $this->view('messages/index', $data);
    }
    
    /**
     * Toon een specifieke conversatie
     */
    public function conversation($otherUserId = null)
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Valideer andere gebruiker ID
        if (!$otherUserId || !is_numeric($otherUserId)) {
            $_SESSION['error_message'] = 'Ongeldige gebruiker.';
            redirect('messages');
            return;
        }
        
        // Controleer of andere gebruiker bestaat
        $otherUser = $this->getUserById($otherUserId);
        if (!$otherUser) {
            $_SESSION['error_message'] = 'Gebruiker niet gevonden.';
            redirect('messages');
            return;
        }
        
        // Haal berichten op tussen deze twee gebruikers
        $messages = $this->getConversationMessages($userId, $otherUserId);
        
        // Markeer berichten als gelezen
        $this->markMessagesAsRead($userId, $otherUserId);
        
        $data = [
            'title' => 'Conversatie met ' . $otherUser['display_name'],
            'other_user' => $otherUser,
            'messages' => $messages,
            'current_user_id' => $userId
        ];
        
        $this->view('messages/conversation', $data);
    }
    
    /**
     * 🔒 GEFIXED: Toon compose pagina met correcte privacy handling
     */
    public function compose($prefilledUsername = null)
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $senderId = $_SESSION['user_id'];
        $recipientUser = null;
        $canSendMessage = true;
        $privacyError = null;

        // Als er een username is opgegeven, controleer privacy
        if ($prefilledUsername) {
            // Haal ontvanger gegevens op
            $stmt = $this->db->prepare("
                SELECT u.id, u.username, COALESCE(up.display_name, u.username) as display_name 
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                WHERE u.username = ?
            ");
            $stmt->execute([$prefilledUsername]);
            $recipientUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recipientUser) {
                // 🔒 PRIVACY CHECK
                $canSendMessage = $this->canSendMessageTo($recipientUser['id'], $senderId);
                
                if (!$canSendMessage) {
                    $privacyError = "Je kunt geen berichten sturen naar {$recipientUser['display_name']}. Deze gebruiker accepteert alleen berichten van vrienden.";
                }
            }
        }

        // 🆕 NIEUW: Haal ALLE gebruikers op met privacy status
        $allUsers = $this->getAllUsersWithPrivacyStatus($senderId);

        $data = [
            'title' => 'Nieuw bericht',
            'recipient_user' => $recipientUser,
            'can_send_message' => $canSendMessage,
            'privacy_error' => $privacyError,
            'all_users' => $allUsers  // 🆕 Alle gebruikers met privacy info
        ];

        $this->view('messages/compose', $data);
    }

    /**
     * 🆕 NIEUW: Haal alle gebruikers op met privacy status
     */
    private function getAllUsersWithPrivacyStatus($senderId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar,
                    ups.messages_from
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id  
                LEFT JOIN user_privacy_settings ups ON u.id = ups.user_id
                WHERE u.id != ?
                ORDER BY up.display_name ASC, u.username ASC
            ");
            $stmt->execute([$senderId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Voeg privacy status toe aan elke gebruiker
            foreach ($users as &$user) {
                $user['avatar_url'] = get_avatar_url($user['avatar']);
                $user['can_send_message'] = $this->canSendMessageTo($user['id'], $senderId);
                
                // Bepaal waarom niet als kan_send_message false is
                if (!$user['can_send_message']) {
                    switch ($user['messages_from'] ?? 'friends') {
                        case 'nobody':
                            $user['privacy_reason'] = 'Accepteert geen berichten';
                            break;
                        case 'friends':
                            $user['privacy_reason'] = 'Alleen vrienden';
                            break;
                        default:
                            $user['privacy_reason'] = 'Niet beschikbaar';
                    }
                }
            }

            return $users;
            
        } catch (\Exception $e) {
            error_log("Error getting users with privacy: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 🔒 BIJGEWERKT: Verstuur bericht MET privacy check
     */
    public function send()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('messages');
            return;
        }

        $senderId = $_SESSION['user_id'];
        
        // 🔧 FIX: Support voor beide field namen
        $recipientId = $_POST['recipient_id'] ?? $_POST['receiver_id'] ?? null;
        $recipientUsername = $_POST['recipient_username'] ?? $_POST['receiver_username'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        
        // 🔧 FIX: Support voor beide field namen
        $message = trim($_POST['message'] ?? $_POST['content'] ?? '');

        // Als recipient_id leeg is maar username wel gevuld, zoek dan de ID op
        if (empty($recipientId) && !empty($recipientUsername)) {
            try {
                $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$recipientUsername]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    $recipientId = $user['id'];
                }
            } catch (\Exception $e) {
                error_log("Error finding user by username: " . $e->getMessage());
            }
        }

        // Validatie
        if (empty($recipientId)) {
            $_SESSION['error_message'] = 'Geen geldige ontvanger geselecteerd.';
            redirect('messages/compose');
            return;
        }
        
        if (empty($message)) {
            $_SESSION['error_message'] = 'Bericht mag niet leeg zijn.';
            redirect('messages/compose');
            return;
        }

        // 🔒 PRIVACY CHECK
        if (!$this->canSendMessageTo($recipientId, $senderId)) {
            $_SESSION['error_message'] = 'Je kunt geen berichten sturen naar deze gebruiker. Deze gebruiker accepteert alleen berichten van vrienden.';
            redirect('messages/compose');
            return;
        }

        try {
            // Verstuur het bericht
            $stmt = $this->db->prepare("
                INSERT INTO messages (sender_id, recipient_id, subject, message, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$senderId, $recipientId, $subject, $message]);
            
            $_SESSION['success_message'] = 'Bericht succesvol verzonden!';
            redirect('messages/conversation?user=' . $recipientId);
            
        } catch (Exception $e) {
            error_log("Error sending message: " . $e->getMessage());
            $_SESSION['error_message'] = 'Er ging iets mis bij het verzenden van het bericht.';
            redirect('messages/compose');
        }
    }

    
    /**
     * 🔒 GEFIXED: Antwoord op bericht met betere error handling
     */
    public function reply()
    {
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
                return;
            }
            redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('messages');
            return;
        }

        $senderId = $_SESSION['user_id'];
        
        // 🔧 FIX: Support voor beide field namen
        $recipientId = $_POST['recipient_id'] ?? $_POST['receiver_id'] ?? null;
        $message = trim($_POST['message'] ?? $_POST['content'] ?? '');

        $errorMessage = null;

        // Validatie
        if (empty($recipientId)) {
            $errorMessage = 'Geen ontvanger gevonden';
        } elseif (empty($message)) {
            $errorMessage = 'Bericht mag niet leeg zijn';
        } else {
            // 🔒 PRIVACY CHECK
            if (!$this->canSendMessageTo($recipientId, $senderId)) {
                $errorMessage = 'Je kunt geen berichten meer sturen naar deze gebruiker. Deze gebruiker accepteert alleen berichten van vrienden.';
            } else {
                // Alles OK, verstuur bericht
                try {
                    $stmt = $this->db->prepare("
                        INSERT INTO messages (sender_id, recipient_id, message, created_at)
                        VALUES (?, ?, ?, NOW())
                    ");
                    
                    $stmt->execute([$senderId, $recipientId, $message]);
                    
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Bericht verzonden']);
                        return;
                    }
                    
                    $_SESSION['success_message'] = 'Bericht succesvol verzonden!';
                    redirect('messages/conversation?user=' . $recipientId);
                    return;
                    
                } catch (Exception $e) {
                    error_log("Error sending reply: " . $e->getMessage());
                    $errorMessage = 'Er ging iets mis bij het verzenden van het bericht';
                }
            }
        }

        // Als we hier komen, is er een fout opgetreden
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            return;
        }
        
        $_SESSION['error_message'] = $errorMessage;
        redirect('messages');
    }
    
    /**
     * Verwerk foto upload voor bericht - VERBETERDE VERSIE
     */
    private function handlePhotoUpload($file, $messageId)
    {
        try {
            // Validatie
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                return null;
            }
            
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                return null;
            }
            
            // Maak upload directory
            $year = date('Y');
            $month = date('m');
            $upload_dir = BASE_PATH . '/public/uploads/messages/' . $year . '/' . $month;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Genereer unieke bestandsnaam
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = 'message_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . '/' . $file_name;
            
            // Upload bestand
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'messages/' . $year . '/' . $month . '/' . $file_name;
                
                // Update message in database
                $stmt = $this->db->prepare("UPDATE messages SET attachment_path = ?, attachment_type = 'photo' WHERE id = ?");
                $stmt->execute([$image_path, $messageId]);
                
                return base_url('uploads/' . $image_path);
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Photo upload error: " . $e->getMessage());
            return null;
        }
    }
        
    /**
     * Maak thumbnail van foto
     */
    private function createThumbnail($sourcePath, $thumbnailPath, $maxWidth = 300, $maxHeight = 300)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) return false;
            
            $sourceWidth = $imageInfo[0];
            $sourceHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Bereken nieuwe afmetingen
            $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
            $newWidth = intval($sourceWidth * $ratio);
            $newHeight = intval($sourceHeight * $ratio);
            
            // Maak resource van bron
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                default:
                    return false;
            }
            
            if (!$sourceImage) return false;
            
            // Maak nieuwe image
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Behoud transparantie voor PNG en GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            // Resize
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
            
            // Sla op
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($thumbnail, $thumbnailPath, 90);
                    break;
                case 'image/png':
                    imagepng($thumbnail, $thumbnailPath);
                    break;
                case 'image/gif':
                    imagegif($thumbnail, $thumbnailPath);
                    break;
            }
            
            // Opruimen
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("Thumbnail creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verwerk emoji shortcuts in tekst
     */
    private function processEmojiShortcuts($text)
    {
        $shortcuts = [
            ':smile:' => '😊',
            ':happy:' => '😊',
            ':grin:' => '😄',
            ':laugh:' => '😂',
            ':wink:' => '😉',
            ':love:' => '😍',
            ':heart:' => '❤️',
            ':kiss:' => '😘',
            ':hug:' => '🤗',
            ':thumbs_up:' => '👍',
            ':thumbs_down:' => '👎',
            ':ok:' => '👌',
            ':clap:' => '👏',
            ':fire:' => '🔥',
            ':star:' => '⭐',
            ':sun:' => '☀️',
            ':moon:' => '🌙',
            ':party:' => '🎉',
            ':cake:' => '🎂',
            ':gift:' => '🎁',
            ':music:' => '🎵',
            ':coffee:' => '☕',
            ':beer:' => '🍺',
            ':pizza:' => '🍕',
            ':car:' => '🚗',
            ':plane:' => '✈️',
            ':phone:' => '📱',
            ':computer:' => '💻',
            ':money:' => '💰',
            ':check:' => '✅',
            ':cross:' => '❌',
            ':warning:' => '⚠️',
            ':info:' => 'ℹ️',
            ':sad:' => '😢',
            ':cry:' => '😭',
            ':angry:' => '😠',
            ':shocked:' => '😱',
            ':cool:' => '😎',
            ':tired:' => '😴',
            ':sick:' => '🤒',
            ':crazy:' => '🤪',
            ':thinking:' => '🤔',
            ':shrug:' => '🤷'
        ];
        
        return str_replace(array_keys($shortcuts), array_values($shortcuts), $text);
    }
    
    /**
     * Markeer bericht als gelezen
     */
    public function markAsRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
            return;
        }
        
        $messageId = $_POST['message_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$messageId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Geen bericht ID']);
            return;
        }
        
        try {
            // Markeer als gelezen (alleen als je de ontvanger bent)
            $stmt = $this->db->prepare("
                UPDATE messages 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND receiver_id = ? AND is_read = 0
            ");
            
            $stmt->execute([$messageId, $userId]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Gemarkeerd als gelezen']);
            
        } catch (\Exception $e) {
            error_log("Error marking message as read: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Fout bij markeren']);
        }
    }
    
    /**
     * Haal alle gesprekken op voor een gebruiker
     */
    private function getConversations($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    conversation_partner.id as user_id,
                    conversation_partner.username,
                    COALESCE(up.display_name, conversation_partner.username) as display_name,
                    up.avatar,
                    latest.content as last_message,
                    latest.created_at as last_message_time,
                    latest.sender_id as last_sender_id,
                    latest.attachment_type as last_message_attachment_type,
                    COALESCE(unread_count.count, 0) as unread_count
                FROM (
                    -- Haal laatste bericht per conversatie op
                    SELECT 
                        CASE 
                            WHEN sender_id = ? THEN receiver_id 
                            ELSE sender_id 
                        END as other_user_id,
                        MAX(created_at) as max_time
                    FROM messages 
                    WHERE (sender_id = ? OR receiver_id = ?)
                        AND deleted_by_sender = 0 
                        AND deleted_by_receiver = 0
                    GROUP BY other_user_id
                ) conversations
                
                JOIN messages latest ON (
                    (latest.sender_id = ? AND latest.receiver_id = conversations.other_user_id) OR
                    (latest.receiver_id = ? AND latest.sender_id = conversations.other_user_id)
                ) AND latest.created_at = conversations.max_time
                
                JOIN users conversation_partner ON conversation_partner.id = conversations.other_user_id
                LEFT JOIN user_profiles up ON conversation_partner.id = up.user_id
                
                LEFT JOIN (
                    SELECT receiver_id, sender_id, COUNT(*) as count
                    FROM messages 
                    WHERE receiver_id = ? AND is_read = 0
                        AND deleted_by_receiver = 0
                    GROUP BY sender_id
                ) unread_count ON unread_count.sender_id = conversations.other_user_id
                
                ORDER BY latest.created_at DESC
            ");
            
            $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format timestamps en avatar URLs
            foreach ($conversations as &$conversation) {
                $conversation['last_message_time_formatted'] = $this->formatDate($conversation['last_message_time']);
                $conversation['avatar_url'] = $this->getAvatarUrl($conversation['avatar']);
                $conversation['last_message_preview'] = $this->truncateText($conversation['last_message'], 50);
                
                // Format bijlage preview
                if ($conversation['last_message_attachment_type'] === 'photo') {
                    $conversation['last_message_preview'] = '📷 Foto';
                }
            }
            
            return $conversations;
            
        } catch (\Exception $e) {
            error_log("Error getting conversations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Haal berichten tussen twee gebruikers op
     */
    private function getConversationMessages($userId, $otherUserId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.subject,
                    m.content,
                    m.is_read,
                    m.created_at,
                    m.parent_message_id,
                    m.attachment_path,
                    m.attachment_type,
                    sender.username as sender_username,
                    COALESCE(sender_profile.display_name, sender.username) as sender_name,
                    sender_profile.avatar as sender_avatar
                FROM messages m
                JOIN users sender ON m.sender_id = sender.id
                LEFT JOIN user_profiles sender_profile ON sender.id = sender_profile.user_id
                WHERE (
                    (m.sender_id = ? AND m.receiver_id = ? AND m.deleted_by_sender = 0) OR
                    (m.sender_id = ? AND m.receiver_id = ? AND m.deleted_by_receiver = 0)
                )
                ORDER BY m.created_at ASC
            ");
            
            $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format timestamps en avatar URLs
            foreach ($messages as &$message) {
                $message['created_at_formatted'] = $this->formatDate($message['created_at']);
                $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
                $message['is_own_message'] = $message['sender_id'] == $userId;
                
                // Format bijlage URL
                if ($message['attachment_path']) {
                    $message['attachment_url'] = base_url('uploads/' . $message['attachment_path']);
                    
                    // Thumbnail URL voor foto's
                    if ($message['attachment_type'] === 'photo') {
                        $pathInfo = pathinfo($message['attachment_path']);
                        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
                        $thumbnailFullPath = BASE_PATH . '/public/uploads/' . $thumbnailPath;
                        
                        if (file_exists($thumbnailFullPath)) {
                            $message['thumbnail_url'] = base_url('uploads/' . $thumbnailPath);
                        } else {
                            $message['thumbnail_url'] = $message['attachment_url'];
                        }
                    }
                }
            }
            
            return $messages;
            
        } catch (\Exception $e) {
            error_log("Error getting conversation messages: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Markeer berichten als gelezen
     */
    private function markMessagesAsRead($userId, $senderId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE messages 
                SET is_read = 1, read_at = NOW() 
                WHERE receiver_id = ? AND sender_id = ? AND is_read = 0
            ");
            
            $stmt->execute([$userId, $senderId]);
            
        } catch (\Exception $e) {
            error_log("Error marking messages as read: " . $e->getMessage());
        }
    }
    
    /**
     * Tel ongelezen berichten
     */
    private function getUnreadCount($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM messages 
                WHERE receiver_id = ? AND is_read = 0 AND deleted_by_receiver = 0
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;
            
        } catch (\Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Helper functies
     */
    private function getUserById($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.username, u.email,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $user['avatar_url'] = $this->getAvatarUrl($user['avatar']);
            }
            
            return $user;
            
        } catch (\Exception $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return null;
        }
    }
    
    private function getUserByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.username, u.email,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.username = ?
            ");
            
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $user['avatar_url'] = $this->getAvatarUrl($user['avatar']);
            }
            
            return $user;
            
        } catch (\Exception $e) {
            error_log("Error getting user by username: " . $e->getMessage());
            return null;
        }
    }
    
    private function getAllUsers()
    {
        try {
            $currentUserId = $_SESSION['user_id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.username,
                    COALESCE(up.display_name, u.username) as display_name
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id != ?
                ORDER BY display_name ASC
            ");
            
            $stmt->execute([$currentUserId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return [];
        }
    }
    
    // private function getAvatarUrl($avatarPath)
    // {
    //     if (empty($avatarPath)) {
    //         return base_url('theme-assets/default/images/default-avatar.png');
    //     }
        
    //     if (str_starts_with($avatarPath, 'theme-assets')) {
    //         return base_url($avatarPath);
    //     }
        
    //     return base_url('uploads/' . $avatarPath);
    // }
    
    private function formatDate($datetime)
    {
        $date = new \DateTime($datetime);
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h > 0) {
                return $diff->h . 'u geleden';
            } elseif ($diff->i > 0) {
                return $diff->i . 'm geleden';
            } else {
                return 'Net nu';
            }
        } elseif ($diff->days == 1) {
            return 'Gisteren ' . $date->format('H:i');
        } elseif ($diff->days < 7) {
            return $diff->days . ' dagen geleden';
        } else {
            return $date->format('d-m-Y H:i');
        }
    }
    
    private function truncateText($text, $length = 50)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }

    public function checkNewMessages()
    {
        // Suppress warnings voor clean JSON output
        error_reporting(E_ERROR | E_PARSE);
        
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || !isset($_GET['user']) || !isset($_GET['since'])) {
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $otherUserId = $_GET['user'];
        $sinceTimestamp = $_GET['since'];
        $sinceDate = date('Y-m-d H:i:s', intval($sinceTimestamp / 1000));
            
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as new_count
                FROM messages 
                WHERE sender_id = ? AND receiver_id = ? 
                AND created_at > ? 
                AND deleted_by_receiver = 0
            ");
            
            $stmt->execute([$otherUserId, $userId, $sinceDate]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'hasNewMessages' => $result['new_count'] > 0,
                'newCount' => $result['new_count']
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getNewMessages()
    {
        error_reporting(E_ERROR | E_PARSE);
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || !isset($_GET['user']) || !isset($_GET['since'])) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $otherUserId = $_GET['user'];
        $sinceTimestamp = $_GET['since'];
        $sinceDate = date('Y-m-d H:i:s', intval($sinceTimestamp / 1000));
        
        try {
            // Haal nieuwe berichten op
            $stmt = $this->db->prepare("
                SELECT 
                    m.id, m.content, m.created_at, m.attachment_path, m.attachment_type,
                    sender.username as sender_username,
                    COALESCE(sender_profile.display_name, sender.username) as sender_name,
                    sender_profile.avatar as sender_avatar
                FROM messages m
                JOIN users sender ON m.sender_id = sender.id
                LEFT JOIN user_profiles sender_profile ON sender.id = sender_profile.user_id
                WHERE m.sender_id = ? AND m.receiver_id = ? 
                AND m.created_at > ? 
                AND m.deleted_by_receiver = 0
                ORDER BY m.created_at ASC
            ");
            
            $stmt->execute([$otherUserId, $userId, $sinceDate]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format berichten
            foreach ($messages as &$message) {
                $message['created_at_formatted'] = $this->formatDate($message['created_at']);
                $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
                
                // Format bijlage URL
                if ($message['attachment_path']) {
                    $message['attachment_url'] = base_url('uploads/' . $message['attachment_path']);
                    
                    // Thumbnail URL voor foto's
                    if ($message['attachment_type'] === 'photo') {
                        $pathInfo = pathinfo($message['attachment_path']);
                        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
                        $thumbnailFullPath = BASE_PATH . '/public/uploads/' . $thumbnailPath;
                        
                        if (file_exists($thumbnailFullPath)) {
                            $message['thumbnail_url'] = base_url('uploads/' . $thumbnailPath);
                        } else {
                            $message['thumbnail_url'] = $message['attachment_url'];
                        }
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Haal een specifiek bericht op met alle gegevens
     */
    private function getMessageById($messageId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.content,
                    m.created_at,
                    m.attachment_path,
                    m.attachment_type,
                    sender.username as sender_username,
                    COALESCE(sender_profile.display_name, sender.username) as sender_name,
                    sender_profile.avatar as sender_avatar
                FROM messages m
                JOIN users sender ON m.sender_id = sender.id
                LEFT JOIN user_profiles sender_profile ON sender.id = sender_profile.user_id
                WHERE m.id = ?
            ");
            
            $stmt->execute([$messageId]);
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($message) {
                // Format data voor frontend
                $message['created_at_formatted'] = $this->formatDate($message['created_at']);
                $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
                $message['is_own_message'] = $message['sender_id'] == $_SESSION['user_id'];
                
                // Format bijlage URL
                if ($message['attachment_path']) {
                    $message['attachment_url'] = base_url('uploads/' . $message['attachment_path']);
                    
                    // Thumbnail URL voor foto's
                    if ($message['attachment_type'] === 'photo') {
                        $pathInfo = pathinfo($message['attachment_path']);
                        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
                        $thumbnailFullPath = BASE_PATH . '/public/uploads/' . $thumbnailPath;
                        
                        if (file_exists($thumbnailFullPath)) {
                            $message['thumbnail_url'] = base_url('uploads/' . $thumbnailPath);
                        } else {
                            $message['thumbnail_url'] = $message['attachment_url'];
                        }
                    }
                }
            }
            
            return $message;
            
        } catch (Exception $e) {
            error_log('Get message by ID error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔒 PRIVACY: Check of viewer berichten mag sturen naar doelgebruiker
     */
    private function canSendMessageTo($recipientUserId, $senderUserId)
    {
        // Kan niet naar jezelf sturen
        if ($recipientUserId == $senderUserId) {
            return false;
        }

        // Haal privacy instellingen van ontvanger op
        $privacySettings = $this->getPrivacySettings($recipientUserId);
        
        if (!$privacySettings) {
            // Geen privacy instellingen = iedereen mag berichten sturen (backwards compatibility)
            return true;
        }

        switch ($privacySettings['messages_from']) {
            case 'everyone':
                return true;
                
            case 'nobody':
                return false;
                
            case 'friends':
                return $this->areFriends($recipientUserId, $senderUserId);
                
            default:
                return true; // Fallback
        }
    }

    /**
     * 🔒 PRIVACY: Haal privacy instellingen op voor een gebruiker
     */
    private function getPrivacySettings($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_privacy_settings 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting privacy settings: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔒 PRIVACY: Check of twee gebruikers vrienden zijn
     */
    private function areFriends($userId1, $userId2)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM friendships 
                WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?))
                AND status = 'accepted'
            ");
            $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
            
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking friendship: " . $e->getMessage());
            return false;
        }
    }
}