<?php

namespace App\Handlers;

// 🚨 FORCE ERROR DISPLAY
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

use App\Controllers\Controller;
use App\Database\Database;
use App\Services\PostService;
use PDO;
use Exception;

class ChatHandler extends Controller
{
    private $db;

    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Chat overzicht - toont alle conversaties van de gebruiker
     */
    public function index() 
    {
        // Bepaal chat mode en laad JavaScript
        $chatMode = $this->shouldUseThemeChat() ? 'theme' : 'core';

        echo '<script>
            window.SOCIALCORE_CHAT_MODE = "' . $chatMode . '"; 
            console.log("🎯 Chat Index mode set to: ' . $chatMode . '");
        </script>';
        
        // Laad main.js ALTIJD voor chat functionaliteit
        echo '<script src="' . base_url('js/main.js') . '"></script>';
        echo '<script>console.log("💬 Chat Index - main.js loaded for ' . $chatMode . ' mode");</script>';

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUser = $this->getCurrentUser();
        $conversations = $this->getUserConversations($currentUser['id']);

        $data = [
            'page_title' => 'Chat',
            'conversations' => $conversations,
            'current_user' => $currentUser,
            'unread_count' => $this->getUnreadMessageCount($currentUser['id']),
            'chat_settings' => $this->getChatSettings(),
            'isChat' => true // ✅ Voorkomt theme.js loading
        ];

        // Bepaal welke chat interface te gebruiken
        if ($this->shouldUseThemeChat()) {
            include BASE_PATH . '/themes/default/pages/chatservice/index.php';
        } else {
            $this->loadCoreView('chat/index', $data);
        }
    }

    /**
     * Conversatie weergeven tussen twee gebruikers
     */
    public function conversation()
    {
        // 🚨 EERSTE REGEL VAN DE METHODE
    // echo "CONVERSATION METHOD STARTED<br>";
    // flush(); // Force output
    
    // $chatMode = $this->shouldUseThemeChat() ? 'theme' : 'core';
    // echo "CHAT MODE DETERMINED: " . $chatMode . "<br>";
    // flush();

    // echo '<script>
    //     window.SOCIALCORE_CHAT_MODE = "' . $chatMode . '"; 
    //     console.log("🎯 Chat mode set to: ' . $chatMode . '");
    // </script>';
    // echo "JS SCRIPT ADDED<br>";
    // flush();

        $chatMode = $this->shouldUseThemeChat() ? 'theme' : 'core';

        // Set chat mode en laad juiste JavaScript
        echo '<script>
            window.SOCIALCORE_CHAT_MODE = "' . $chatMode . '"; 
            console.log("🎯 Chat mode set to: ' . $chatMode . '");
        </script>';
        
        // Laad main.js ALLEEN voor Core Chat
        if ($chatMode === 'core') {
            echo '<script src="' . base_url('js/main.js') . '"></script>';
            echo '<script>console.log("💬 Core Chat - main.js loaded");</script>';
        } else {
            echo '<script>console.log("🎨 Theme Chat - using theme styling with main.js");</script>';
            // Theme Chat gebruikt main.js via normale theme loading
            echo '<script src="' . base_url('js/main.js') . '"></script>';
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUserId = $_SESSION['user_id'];
        $friendId = $_GET['with'] ?? null;
        
        if (!$friendId) {
            header('Location: /?route=chat&error=no_friend_specified');
            exit;
        }
        
        try {
            // Haal vriend informatie op
            $friend = $this->getUserById($friendId);
            if (!$friend) {
                header('Location: /?route=chat&error=friend_not_found');
                exit;
            }

            // Zoek of maak conversatie aan
            $conversation = $this->findOrCreateConversation($currentUserId, $friendId);
            
            // Haal berichten op
            $messages = $this->getConversationMessages($conversation['id']);
            
            // Markeer berichten als gelezen
            $this->markMessagesAsRead($conversation['id'], $currentUserId);

            $data = [
                'friend' => $friend,
                'conversation' => $conversation,
                'messages' => $messages,
                'currentUserId' => $currentUserId,
                'chat_settings' => $this->getChatSettings(),
                'isChat' => true // ✅ Voorkomt theme.js loading
            ];
            
            // Bepaal welke chat interface te gebruiken
            if ($this->shouldUseThemeChat()) {
    $title = 'Chat met ' . htmlspecialchars($friend['display_name'] ?: $friend['username']);
    $pageTitle = $title;
    include BASE_PATH . '/themes/default/pages/chatservice/conversation.php';
} else {
    // echo "DEBUG: About to call loadCoreView for conversation<br>";
    // echo "DEBUG: Data keys: " . implode(', ', array_keys($data)) . "<br>";
    $this->loadCoreView('chat/conversation', $data);
}
            
        } catch (Exception $e) {
            error_log("Conversation error: " . $e->getMessage());
            header('Location: /?route=chat&error=conversation_failed');
            exit;
        }
    }

    /**
     * Nieuw gesprek opstellen
     */
    public function compose()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUserId = $_SESSION['user_id'];
        
        try {
            // Haal alle vrienden op
            $friends = $this->getUserFriends($currentUserId);
            
            $data = [
                'friends' => $friends,
                'current_user' => $this->getCurrentUser(),
                'chat_settings' => $this->getChatSettings()
            ];
            
            // Bepaal welke chat interface te gebruiken
            if ($this->shouldUseThemeChat()) {
                $title = 'Nieuwe chat starten';
                $pageTitle = $title;
                include BASE_PATH . '/themes/default/pages/chatservice/compose.php';
            } else {
                $this->loadCoreView('chat/compose', $data);
            }
            
        } catch (Exception $e) {
            error_log("Compose error: " . $e->getMessage());
            header('Location: /?route=chat&error=compose_failed');
            exit;
        }
    }

    /**
     * Bericht verzenden (AJAX)
     */
    public function sendMessage() 
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
            exit;
        }

        try {
            $currentUserId = $_SESSION['user_id'];
            $friendId = $_POST['friend_id'] ?? null;
            $content = trim($_POST['content'] ?? '');

            // Validatie
            if (!$friendId) {
                echo json_encode(['success' => false, 'message' => 'Ontvanger is verplicht']);
                exit;
            }

            // Check of er content OF een foto is
            $hasContent = !empty($content);
            $hasPhoto = isset($_FILES['message_photo']) && !empty($_FILES['message_photo']['name']);
            
            if (!$hasContent && !$hasPhoto) {
                echo json_encode(['success' => false, 'message' => 'Bericht of foto is verplicht']);
                exit;
            }

            $chatSettings = $this->getChatSettings();
            $maxLength = $chatSettings['chat_max_message_length'] ?? 1000;
            
            if (strlen($content) > $maxLength) {
                echo json_encode(['success' => false, 'message' => "Bericht is te lang (max {$maxLength} karakters)"]);
                exit;
            }

            // Controleer of ontvanger bestaat
            $friend = $this->getUserById($friendId);
            if (!$friend) {
                echo json_encode(['success' => false, 'message' => 'Ontvanger niet gevonden']);
                exit;
            }

            // Zoek of maak conversatie aan
            $conversation = $this->findOrCreateConversation($currentUserId, $friendId);

            // Bepaal message type
            $messageType = 'text';
            if ($hasPhoto) {
                $messageType = 'image';
            }

            // Maak bericht aan
            $messageId = $this->createChatMessage($conversation['id'], $currentUserId, $content, $messageType);

            if (!$messageId) {
                echo json_encode(['success' => false, 'message' => 'Kon bericht niet aanmaken']);
                exit;
            }

            // Verwerk foto upload indien aanwezig
            $mediaInfo = null;
            if ($hasPhoto) {
                $uploadResult = $this->handlePhotoUpload($_FILES['message_photo'], $messageId);
                if (!$uploadResult['success']) {
                    // Verwijder bericht bij upload fout
                    $this->deleteChatMessage($messageId);
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
                $mediaInfo = $this->getMessageMedia($messageId);
            }

            // Update conversatie timestamp
            $this->updateConversationTimestamp($conversation['id']);

            // ✅ NIEUWE SECTIE: Haal volledige message data op
            $currentUser = $this->getCurrentUser();
            
            // Bouw complete message object voor frontend
            $messageData = [
                'id' => $messageId,
                'conversation_id' => $conversation['id'],
                'sender_id' => $currentUserId,
                'message_text' => $content,
                'message_type' => $messageType,
                'created_at' => date('Y-m-d H:i:s'), // Current timestamp
                'is_read' => 0,
                'username' => $currentUser['username'],
                'display_name' => $currentUser['display_name'],
                'sender_avatar_url' => $this->getAvatarUrl($currentUser['avatar']),
                'media_info' => $mediaInfo
            ];

            // Return volledige message data
            echo json_encode([
                'success' => true, 
                'message' => $messageData, // ✅ Volledige message data
                'message_id' => $messageId
            ]);

        } catch (Exception $e) {
            error_log("Send message error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server fout']);
        }
        exit;
    }

    /**
     * Nieuwe berichten ophalen (AJAX)
     */
    public function getNewMessages() 
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'messages' => []]);
            exit;
        }

        try {
            $currentUserId = $_SESSION['user_id'];
            $friendId = $_GET['friend_id'] ?? null;
            $lastMessageId = $_GET['last_message_id'] ?? 0;

            if (!$friendId) {
                echo json_encode(['success' => false, 'messages' => []]);
                exit;
            }

            $conversation = $this->findOrCreateConversation($currentUserId, $friendId);
            $newMessages = $this->getNewMessagesAfter($conversation['id'], $lastMessageId);
            
            echo json_encode(['success' => true, 'messages' => $newMessages]);

        } catch (Exception $e) {
            error_log("Get new messages error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'messages' => []]);
        }
        exit;
    }

    /**
     * =====================================
     * PRIVATE HELPER METHODS
     * =====================================
     */

    /**
     * Bepaal of thema chat gebruikt moet worden
     */
    // 
    
    private function shouldUseThemeChat()
    {
        // Simpel: volg de algemene platform mode
        return !is_core_mode();
    }

    /**
     * Haal chat instellingen op uit database
     */
    private function getChatSettings()
    {
        static $settings = null;
        
        if ($settings === null) {
            $settings = [
                'chat_mode' => 'auto',
                'chat_features_emoji' => '1',
                'chat_features_file_upload' => '1',
                'chat_features_real_time' => '0',
                'chat_max_message_length' => '1000',
                'chat_max_file_size' => '2048',
                'chat_online_timeout' => '15'
            ];

            try {
                $stmt = $this->db->prepare("
                    SELECT setting_name, setting_value 
                    FROM site_settings 
                    WHERE setting_name LIKE 'chat_%'
                ");
                $stmt->execute();
                $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                $settings = array_merge($settings, $dbSettings);
            } catch (Exception $e) {
                error_log("Error loading chat settings: " . $e->getMessage());
            }
        }
        
        return $settings;
    }

    /**
     * Zoek bestaande conversatie of maak nieuwe aan
     */
    private function findOrCreateConversation($user1Id, $user2Id)
    {
        // Zorg ervoor dat user1_id altijd de kleinste is
        $smallerId = min($user1Id, $user2Id);
        $largerId = max($user1Id, $user2Id);

        // Zoek bestaande conversatie
        $stmt = $this->db->prepare("
            SELECT * FROM chat_conversations 
            WHERE user1_id = ? AND user2_id = ?
        ");
        $stmt->execute([$smallerId, $largerId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conversation) {
            return $conversation;
        }

        // Maak nieuwe conversatie aan
        $stmt = $this->db->prepare("
            INSERT INTO chat_conversations (user1_id, user2_id, created_at, updated_at, last_message_at) 
            VALUES (?, ?, NOW(), NOW(), NULL)
        ");
        
        if ($stmt->execute([$smallerId, $largerId])) {
            $conversationId = $this->db->lastInsertId();
            
            // Haal de nieuwe conversatie op
            $stmt = $this->db->prepare("SELECT * FROM chat_conversations WHERE id = ?");
            $stmt->execute([$conversationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        throw new Exception("Kon conversatie niet aanmaken");
    }

    /**
     * Haal berichten van een conversatie op
     */
    private function getConversationMessages($conversationId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                cm.*,
                u.username,
                up.display_name,
                up.avatar
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE cm.conversation_id = ?
            ORDER BY cm.created_at ASC
        ");
        
        $stmt->execute([$conversationId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Voeg avatar URLs en media info toe
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getAvatarUrl($message['avatar']);
            
            if ($message['message_type'] === 'image') {
                $message['media_info'] = $this->getMessageMedia($message['id']);
            }
        }

        return $messages;
    }

    /**
     * Maak nieuw chat bericht aan
     */
    private function createChatMessage($conversationId, $senderId, $messageText, $messageType = 'text')
    {
        $stmt = $this->db->prepare("
            INSERT INTO chat_messages (conversation_id, sender_id, message_text, message_type, created_at, is_read) 
            VALUES (?, ?, ?, ?, NOW(), 0)
        ");
        
        if ($stmt->execute([$conversationId, $senderId, $messageText, $messageType])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Verwerk foto upload voor chat
     */
    private function handlePhotoUpload($file, $messageId)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload fout'];
        }

        // Kopieer PostService validatie logica
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Afbeelding is te groot. Maximum grootte is 5MB.'];
        }
        
        // File type validatie (zoals PostService)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Ongeldig bestandstype. Alleen JPEG, PNG, GIF en WebP zijn toegestaan.'];
        }
        
        // Image validatie (zoals PostService)
        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return ['success' => false, 'message' => 'Ongeldig afbeeldingsbestand.'];
        }
        
        // ✅ GEFIXTE Upload directory 
        $year = date('Y');
        $month = date('m');
        $upload_dir = BASE_PATH . '/public/uploads/chats/' . $year . '/' . $month;
        
        // Maak directory als het niet bestaat
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return ['success' => false, 'message' => 'Kon upload directory niet aanmaken.'];
            }
        }
        
        // Generate filename (zoals PostService)
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = 'chat_' . $messageId . '_' . uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . '/' . $file_name;
        
        // Move file (zoals PostService)
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $relativePath = 'chats/' . $year . '/' . $month . '/' . $file_name;
            
            // Database insert
            $stmt = $this->db->prepare("
                INSERT INTO chat_media (message_id, file_name, file_path, file_type, file_size, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$messageId, $file_name, $relativePath, $file['type'], $file['size']])) {
                return ['success' => true, 'path' => $relativePath];
            } else {
                // Verwijder bestand bij database fout
                @unlink($upload_path);
                return ['success' => false, 'message' => 'Database fout bij opslaan media.'];
            }
        }
        
        return ['success' => false, 'message' => 'Fout bij het uploaden van de afbeelding.'];
    }

    /**
     * Haal media informatie van een bericht op
     */
    private function getMessageMedia($messageId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM chat_media 
            WHERE message_id = ?
        ");
        $stmt->execute([$messageId]);
        $media = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($media) {
            $media['media_url'] = base_url('uploads/' . $media['file_path']);
        }

        return $media;
    }

    /**
     * Update conversatie timestamp
     */
    private function updateConversationTimestamp($conversationId)
    {
        $stmt = $this->db->prepare("
            UPDATE chat_conversations 
            SET updated_at = NOW(), last_message_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$conversationId]);
    }

    /**
     * Markeer berichten als gelezen
     */
    private function markMessagesAsRead($conversationId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE chat_messages 
            SET is_read = 1 
            WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
        ");
        $stmt->execute([$conversationId, $userId]);
    }

    /**
     * Haal gebruikers conversaties op
     */
    private function getUserConversations($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                cc.*,
                CASE 
                    WHEN cc.user1_id = ? THEN cc.user2_id 
                    ELSE cc.user1_id 
                END as friend_id,
                CASE 
                    WHEN cc.user1_id = ? THEN u2.username 
                    ELSE u1.username 
                END as friend_username,
                CASE 
                    WHEN cc.user1_id = ? THEN COALESCE(up2.display_name, u2.username)
                    ELSE COALESCE(up1.display_name, u1.username)
                END as friend_name,
                CASE 
                    WHEN cc.user1_id = ? THEN up2.avatar 
                    ELSE up1.avatar 
                END as friend_avatar,
                (SELECT COUNT(*) FROM chat_messages cm 
                 WHERE cm.conversation_id = cc.id 
                 AND cm.sender_id != ? 
                 AND cm.is_read = 0) as unread_count
            FROM chat_conversations cc
            JOIN users u1 ON cc.user1_id = u1.id
            JOIN users u2 ON cc.user2_id = u2.id
            LEFT JOIN user_profiles up1 ON u1.id = up1.user_id
            LEFT JOIN user_profiles up2 ON u2.id = up2.user_id
            WHERE cc.user1_id = ? OR cc.user2_id = ?
            ORDER BY cc.last_message_at DESC, cc.updated_at DESC
        ");
        
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Voeg avatar URLs toe
        foreach ($conversations as &$conversation) {
            $conversation['friend_avatar_url'] = $this->getAvatarUrl($conversation['friend_avatar']);
        }

        return $conversations;
    }

    /**
     * Haal vrienden van gebruiker op
     */
    private function getUserFriends($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, 
                u.username, 
                COALESCE(up.display_name, u.username) as display_name,
                up.avatar,
                CASE 
                    WHEN u.last_activity > DATE_SUB(NOW(), INTERVAL ? MINUTE) THEN 1
                    ELSE 0
                END as is_online
            FROM friendships f
            JOIN users u ON (f.friend_id = u.id OR f.user_id = u.id) AND u.id != ?
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE (f.user_id = ? OR f.friend_id = ?) AND f.status = 'accepted'
            ORDER BY is_online DESC, display_name ASC
        ");
        
        $chatSettings = $this->getChatSettings();
        $onlineTimeout = $chatSettings['chat_online_timeout'] ?? 15;
        
        $stmt->execute([$onlineTimeout, $userId, $userId, $userId]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($friends as &$friend) {
            $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
        }
        
        return $friends;
    }

    /**
     * Haal ongelezen berichten count op
     */
    private function getUnreadMessageCount($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM chat_messages cm
            JOIN chat_conversations cc ON cm.conversation_id = cc.id
            WHERE (cc.user1_id = ? OR cc.user2_id = ?)
            AND cm.sender_id != ? 
            AND cm.is_read = 0
        ");
        $stmt->execute([$userId, $userId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Haal nieuwe berichten op na bepaald bericht ID
     */
    private function getNewMessagesAfter($conversationId, $lastMessageId) 
    {
        $stmt = $this->db->prepare("
            SELECT 
                cm.*,
                u.username,
                up.display_name,
                up.avatar
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE cm.conversation_id = ? AND cm.id > ?
            ORDER BY cm.created_at ASC
        ");
        
        $stmt->execute([$conversationId, $lastMessageId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
            
            if ($message['message_type'] === 'image') {
                $message['media_info'] = $this->getMessageMedia($message['id']);
            }
        }
        
        return $messages;
    }

    /**
     * Haal gebruiker op by ID
     */
    private function getUserById($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, 
                u.username, 
                u.email, 
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
    }

    /**
     * Verwijder chat bericht
     */
    private function deleteChatMessage($messageId) 
    {
        // Verwijder eerst media
        $stmt = $this->db->prepare("DELETE FROM chat_media WHERE message_id = ?");
        $stmt->execute([$messageId]);
        
        // Dan het bericht
        $stmt = $this->db->prepare("DELETE FROM chat_messages WHERE id = ?");
        $stmt->execute([$messageId]);
    }

    // ========================================
    // 💬 CHATHANDLER API METHODS
    // ========================================

    /**
     * API: Get all conversations for user
     */
    public function apiGetConversations()
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
                SELECT DISTINCT
                    CASE 
                        WHEN c.user1_id = ? THEN c.user2_id 
                        ELSE c.user1_id 
                    END as other_user_id,
                    u.username as other_username,
                    COALESCE(up.display_name, u.username) as other_display_name,
                    up.avatar as other_avatar,
                    c.last_message_at,
                    c.last_message_preview,
                    COUNT(CASE WHEN cm.is_read = 0 AND cm.sender_id != ? THEN 1 END) as unread_count
                FROM chat_conversations c
                JOIN users u ON (
                    CASE 
                        WHEN c.user1_id = ? THEN c.user2_id 
                        ELSE c.user1_id 
                    END = u.id
                )
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN chat_messages cm ON c.id = cm.conversation_id
                WHERE c.user1_id = ? OR c.user2_id = ?
                GROUP BY c.id, other_user_id, u.username, up.display_name, up.avatar, c.last_message_at, c.last_message_preview
                ORDER BY c.last_message_at DESC
            ");
            
            $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
            $conversations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format conversations
            foreach ($conversations as &$conversation) {
                $conversation['other_avatar_url'] = get_avatar_url($conversation['other_avatar']);
                $conversation['time_ago'] = format_time_ago($conversation['last_message_at']);
                $conversation['unread_count'] = intval($conversation['unread_count']);
            }
            
            echo json_encode([
                'success' => true,
                'conversations' => $conversations
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching conversations: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Get messages in a conversation
     */
    public function apiGetConversation()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $conversationId = $_GET['conversation_id'] ?? null;
        $otherUserId = $_GET['user_id'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if (!$conversationId && !$otherUserId) {
            echo json_encode(['success' => false, 'message' => 'Conversation ID or User ID required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            // Get conversation ID if only user ID provided
            if (!$conversationId && $otherUserId) {
                $stmt = $db->prepare("
                    SELECT id FROM chat_conversations 
                    WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)
                ");
                $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
                $conv = $stmt->fetch();
                $conversationId = $conv ? $conv['id'] : null;
            }
            
            if (!$conversationId) {
                echo json_encode(['success' => false, 'message' => 'Conversation not found']);
                exit;
            }
            
            // Get messages
            $stmt = $db->prepare("
                SELECT cm.*, u.username, 
                    COALESCE(up.display_name, u.username) as sender_name,
                    up.avatar as sender_avatar
                FROM chat_messages cm
                JOIN users u ON cm.sender_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE cm.conversation_id = ?
                ORDER BY cm.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute([$conversationId, $limit, $offset]);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format messages
            foreach ($messages as &$message) {
                $message['sender_avatar_url'] = get_avatar_url($message['sender_avatar']);
                $message['time_ago'] = format_time_ago($message['created_at']);
                $message['is_own'] = ($message['sender_id'] == $userId);
                
                // Handle attachments
                if (!empty($message['attachment_path'])) {
                    $message['attachment_url'] = base_url('uploads/' . $message['attachment_path']);
                }
            }
            
            // Reverse array to show oldest first
            $messages = array_reverse($messages);
            
            echo json_encode([
                'success' => true,
                'conversation_id' => $conversationId,
                'messages' => $messages,
                'has_more' => count($messages) >= $limit
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching conversation: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Send new message
     */
    public function apiSendMessage()
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
        
        $recipientId = $_POST['recipient_id'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $conversationId = $_POST['conversation_id'] ?? null;
        $senderId = $_SESSION['user_id'];
        
        // Validation
        if (!$recipientId && !$conversationId) {
            echo json_encode(['success' => false, 'message' => 'Recipient ID or Conversation ID required']);
            exit;
        }
        
        $hasMessage = !empty($message);
        $hasAttachment = !empty($_FILES['attachment']['name']);
        
        if (!$hasMessage && !$hasAttachment) {
            echo json_encode(['success' => false, 'message' => 'Message content or attachment required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            $db->beginTransaction();
            
            // Get or create conversation
            if (!$conversationId) {
                // Check if conversation exists
                $stmt = $db->prepare("
                    SELECT id FROM chat_conversations 
                    WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)
                ");
                $stmt->execute([$senderId, $recipientId, $recipientId, $senderId]);
                $conv = $stmt->fetch();
                
                if ($conv) {
                    $conversationId = $conv['id'];
                } else {
                    // Create new conversation
                    $stmt = $db->prepare("
                        INSERT INTO chat_conversations (user1_id, user2_id, created_at, last_message_at) 
                        VALUES (?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([$senderId, $recipientId]);
                    $conversationId = $db->lastInsertId();
                }
            }
            
            // Handle file attachment
            $attachmentPath = null;
            $attachmentType = null;
            
            if ($hasAttachment) {
                $uploadResult = $this->handleAttachmentUpload($_FILES['attachment']);
                if ($uploadResult['success']) {
                    $attachmentPath = $uploadResult['path'];
                    $attachmentType = $uploadResult['type'];
                } else {
                    $db->rollBack();
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
            }
            
            // Insert message
            $stmt = $db->prepare("
                INSERT INTO chat_messages (
                    conversation_id, sender_id, message, attachment_path, attachment_type, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $conversationId, 
                $senderId, 
                $message, 
                $attachmentPath, 
                $attachmentType
            ]);
            
            $messageId = $db->lastInsertId();
            
            // Update conversation last message
            $preview = $hasMessage ? substr($message, 0, 100) : '[Attachment]';
            $stmt = $db->prepare("
                UPDATE chat_conversations 
                SET last_message_at = NOW(), last_message_preview = ? 
                WHERE id = ?
            ");
            $stmt->execute([$preview, $conversationId]);
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message_id' => $messageId,
                'conversation_id' => $conversationId,
                'message' => 'Message sent successfully'
            ]);
            
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Mark messages as read
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
        
        $conversationId = $_POST['conversation_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$conversationId) {
            echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
            exit;
        }
        
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            // Mark all messages in conversation as read for current user
            $stmt = $db->prepare("
                UPDATE chat_messages 
                SET is_read = 1, read_at = NOW() 
                WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
            ");
            
            $stmt->execute([$conversationId, $userId]);
            $affectedRows = $stmt->rowCount();
            
            echo json_encode([
                'success' => true,
                'marked_read' => $affectedRows,
                'message' => 'Messages marked as read'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error marking messages as read: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * API: Get unread message count
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
                FROM chat_messages cm
                JOIN chat_conversations cc ON cm.conversation_id = cc.id
                WHERE (cc.user1_id = ? OR cc.user2_id = ?) 
                AND cm.sender_id != ? 
                AND cm.is_read = 0
            ");
            
            $stmt->execute([$userId, $userId, $userId]);
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
     * Helper: Handle attachment upload
     */
    private function handleAttachmentUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File too large (max 5MB)'];
        }
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'chat_' . uniqid() . '.' . $extension;
        
        // Create upload directory
        $uploadDir = BASE_PATH . '/public/uploads/chats/' . date('Y/m');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . '/' . $filename;
        $relativePath = 'chats/' . date('Y/m') . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => $relativePath,
                'type' => $file['type']
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to save file'];
        }
    }

    private function loadCoreView($viewPath, $data = [])
    {
        // Check BASE_PATH constant
        if (!defined('BASE_PATH')) {
            throw new Exception('BASE_PATH constant not defined');
        }
        
        // Build path to Core view
        $coreViewPath = BASE_PATH . '/app/Views/core/' . $viewPath . '.php';
        
        // Check if Core view exists
        if (!file_exists($coreViewPath)) {
            // Fallback to regular chatservice view if Core view doesn't exist
            $fallbackPath = BASE_PATH . '/app/Views/chatservice/' . basename($viewPath) . '.php';
            
            if (file_exists($fallbackPath)) {
                extract($data);
                include $fallbackPath;
                return;
            }
            
            throw new Exception("Neither Core view nor fallback view found for: {$viewPath}");
        }
        
        // Extract data for view and include Core view
        extract($data);
        include $coreViewPath;
    }
}