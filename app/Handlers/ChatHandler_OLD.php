<?php

namespace App\Handlers;

use App\Controllers\Controller;
use App\Database\Database;
use App\Helpers\ChatSettings;
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
     * Chat overzicht - met intelligente thema/core keuze
     */
    public function index() 
    {
        
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
            'online_friends' => $this->getOnlineFriends()
        ];

        // SMART CHAT SYSTEM - bepaal welke view te gebruiken
        if ($this->shouldUseThemeChat()) {
            
            include BASE_PATH . '/themes/default/pages/chatservice/index.php';
            return; // Stop hier
            
        } else {
            // Laad core chat
            //echo "<div style='background: red; color: white; padding: 10px; margin: 10px;'>LOADING CORE CHAT!</div>";
            return $this->loadChatView('index', $data);
        }
    }

    /**
     * Conversatie weergeven
     */
    public function conversation()
    {
        $currentUserId = $_SESSION['user_id'];
        $friendId = $_GET['with'] ?? null;
        
        if (!$friendId) {
            header('Location: /?route=chat&error=no_friend_specified');
            exit;
        }
        
        try {
            $db = Database::getInstance()->getPdo();
            
            // Haal vriend informatie op
            $stmt = $db->prepare("
                SELECT u.id, u.username, up.display_name, up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$friendId]);
            $friend = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$friend) {
                header('Location: /?route=chat&error=friend_not_found');
                exit;
            }
            
            // Voeg avatar URL toe
            $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
            
            // Haal berichten op tussen current user en friend
            $stmt = $db->prepare("
                SELECT m.*, u.username as sender_username, up.display_name as sender_display_name, up.avatar as sender_avatar,
                    m.attachment_path, m.attachment_type
                FROM messages m
                LEFT JOIN users u ON m.sender_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                OR (m.sender_id = ? AND m.receiver_id = ?)
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([$currentUserId, $friendId, $friendId, $currentUserId]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Voeg avatar URLs toe aan berichten
            // foreach ($messages as &$message) {
            //     $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
            // }

            foreach ($messages as &$message) {
                $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
                // NIEUW: Voeg media URL toe voor afbeeldingen
                if ($message['attachment_path']) {
                    $message['media_url'] = base_url($message['attachment_path']);
                    $message['media_type'] = $message['attachment_type'];
                }
            }
            
            // SMART CHAT SYSTEM - bepaal welke view te gebruiken
            if ($this->shouldUseThemeChat()) {
                // Laad Hyves thema conversation
                $title = 'Krabbels met ' . htmlspecialchars($friend['display_name'] ?: $friend['username']);
                $pageTitle = $title;
                
                include BASE_PATH . '/themes/default/pages/chatservice/conversation.php';
            } else {
                // Laad core conversation
                $this->view('chatservice/conversation', [
                    'friend' => $friend,
                    'messages' => $messages,
                    'currentUserId' => $currentUserId
                ]);
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
        $currentUserId = $_SESSION['user_id'];
        
        try {
            $db = Database::getInstance()->getPdo();
            
            // Haal alle vrienden op met hun profielgegevens
            $stmt = $db->prepare("
                SELECT DISTINCT
                    u.id,
                    u.username,
                    up.display_name,
                    up.avatar,
                    CASE 
                        WHEN u.last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE) THEN 1
                        ELSE 0
                    END as is_online
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                INNER JOIN friendships f ON (
                    (f.user_id = ? AND f.friend_id = u.id) OR 
                    (f.friend_id = ? AND f.user_id = u.id)
                )
                WHERE f.status = 'accepted' 
                AND u.id != ?
                ORDER BY is_online DESC, up.display_name ASC
            ");

            $stmt->execute([$currentUserId, $currentUserId, $currentUserId]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Voeg avatar URLs toe
            foreach ($friends as &$friend) {
                $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
            }
            
            // SMART CHAT SYSTEM - bepaal welke view te gebruiken  
            if ($this->shouldUseThemeChat()) {
                // Laad Hyves thema compose
                $title = 'Nieuwe krabbel versturen';
                $pageTitle = $title;
                
                include BASE_PATH . '/themes/default/pages/chatservice/compose.php';
            } else {
                // Laad core compose
                $this->view('chatservice/compose', ['friends' => $friends]);
            }
            


        } catch (Exception $e) {
            error_log("Compose error: " . $e->getMessage());
            header('Location: /?route=chat&error=compose_failed');
            exit;
        }
    }

    /**
     * Laad core compose (volledige HTML document)
     */
    private function loadCoreCompose($data)
    {
        // Extract data voor de view
        extract($data);
        
        // Laad de core compose view direct
        include BASE_PATH . '/app/Views/chatservice/compose.php';
    }

    private function shouldUseThemeChat()
    {
        return ChatSettings::shouldUseThemeChat();
    }

    /**
     * SMART CHAT VIEW LOADER
     * Bepaalt automatisch of thema-chat of core-chat gebruikt moet worden
     */
    private function loadChatView($viewName, $data = [])
    {
        // 1. Check admin instelling (als geïmplementeerd)
        $chatMode = $this->getChatMode();
        
        // 2. Check of thema een chat implementatie heeft
        $themeHasChat = $this->themeHasChatView($viewName);
        
        // 3. Beslissingslogica
        if ($chatMode === 'force_core') {
            // Admin forceert core chat
            return $this->loadCoreChat($viewName, $data);
        } 
        elseif ($chatMode === 'force_theme') {
            // Admin forceert thema chat
            return $this->loadThemeChat($viewName, $data);
        } 
        elseif ($themeHasChat) {
            // Auto: thema heeft chat, gebruik thema
            return $this->loadThemeChat($viewName, $data);
        } 
        else {
            // Auto: thema heeft geen chat, gebruik core
            return $this->loadCoreChat($viewName, $data);
        }
    }

    /**
     * Laad thema-specifieke chat view
     */
    private function loadThemeChat($viewName, $data = [])
    {
        // Gebruik normale thema system
        $data['chat_mode'] = 'theme';
        $data['chat_features'] = $this->getThemeChatFeatures();
        
        return $this->view("chatservice/{$viewName}", $data);
    }

    /**
     * Laad core chat view (fallback)
     */
    private function loadCoreChat($viewName, $data = [])
    {
        // Gebruik direct core views
        $data['chat_mode'] = 'core';
        $data['chat_features'] = $this->getCoreChatFeatures();
        
        // Extract data om variabelen beschikbaar te maken
        extract($data);
        
        // Laad core view direct
        $viewPath = __DIR__ . "/../Views/chatservice/{$viewName}.php";
        
        if (file_exists($viewPath)) {
            // Buffer de view content
            ob_start();
            include $viewPath;
            $content = ob_get_clean();
            
            // Wrap in basis thema layout
            $data['content'] = $content;
            extract($data);
            
            // Laad thema header/footer rondom core content
            get_header($data);
            echo $content;
            get_footer($data);
            
            return true;
        } else {
            // Fallback naar thema
            return $this->loadThemeChat($viewName, $data);
        }
    }

    /**
     * Check of thema een chat view heeft
     */
    private function themeHasChatView($viewName)
    {
        $themePath = get_theme_path("pages/chatservice/{$viewName}.php");
        return file_exists($themePath);
    }

    /**
     * Haal chat mode op uit admin instellingen
     */
    private function getChatMode()
    {
        // Check database setting (later implementeren)
        try {
            $stmt = $this->db->prepare("
                SELECT setting_value 
                FROM site_settings 
                WHERE setting_name = 'chat_mode'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['setting_value']; // 'auto', 'force_theme', 'force_core'
            }
        } catch (Exception $e) {
            // Fallback bij database fout
        }
        
        return 'auto'; // Default: automatisch detecteren
    }

    /**
     * Thema-specifieke chat features
     */
    private function getThemeChatFeatures()
    {
        $theme = get_active_theme();
        
        $features = [
            'emoji_reactions' => true,
            'file_sharing' => true,
            'voice_messages' => false,
            'video_calls' => false
        ];
        
        // Thema-specifieke features
        if ($theme === 'default') {
            $features['hyves_respectjes'] = true;
            $features['krabbel_mode'] = true;
            $features['nostalgic_sounds'] = true;
        }
        
        return $features;
    }

    /**
     * Core chat features (basis functionaliteit)
     */
    private function getCoreChatFeatures()
    {
        return [
            'emoji_reactions' => true,
            'file_sharing' => true,
            'voice_messages' => false,
            'video_calls' => false,
            'real_time' => true,
            'modern_ui' => true
        ];
    }

    /**
     * Bericht verzenden (AJAX) - met foto ondersteuning
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
            $currentUser = $this->getCurrentUser();
            $receiverId = $_POST['receiver_id'] ?? null;
            $content = trim($_POST['content'] ?? '');

            // Validatie
            if (!$receiverId) {
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

            if (strlen($content) > 1000) {
                echo json_encode(['success' => false, 'message' => 'Bericht is te lang (max 1000 karakters)']);
                exit;
            }

            // Controleer of ontvanger bestaat
            $recipient = $this->getUserById($receiverId);
            if (!$recipient) {
                echo json_encode(['success' => false, 'message' => 'Ontvanger niet gevonden']);
                exit;
            }

            // EERST foto upload verwerken (als aanwezig)
            $attachmentPath = null;
            $attachmentType = null;

            if ($hasPhoto) {
                $uploadResult = $this->handlePhotoUpload($_FILES['message_photo']);
                if (!$uploadResult['success']) {
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
                $attachmentPath = $uploadResult['path'];
                $attachmentType = $uploadResult['type'];
            }

            // DAN bericht opslaan in database MET attachment info (SLECHTS ÉÉN KEER)
            $messageId = $this->createMessage($currentUser['id'], $receiverId, $content, '', $attachmentPath, $attachmentType);

            if (!$messageId) {
                echo json_encode(['success' => false, 'message' => 'Kon bericht niet aanmaken']);
                exit;
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Bericht verzonden',
                'message_id' => $messageId
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server fout: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Helper: Photo upload afhandeling - GEFIXED voor messages tabel
     */
    private function handlePhotoUpload($file, $messageId = null) 
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload fout'];
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Alleen JPG, PNG, GIF en WebP bestanden toegestaan'];
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            return ['success' => false, 'message' => 'Bestand te groot (max 2MB)'];
        }

        // Upload directory
        $uploadDir = __DIR__ . '/../../public/uploads/messages/' . date('Y/m') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'message_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Relatieve pad voor database
            $relativePath = 'uploads/messages/' . date('Y/m') . '/' . $filename;
            
            return ['success' => true, 'path' => $relativePath, 'type' => 'photo'];
        }

        return ['success' => false, 'message' => 'Kon bestand niet opslaan'];
    }

    /**
     * Database: Media record aanmaken in messages_media
     */
    // private function createMessageMedia($messageId, $mediaType, $filePath, $fileName, $fileSize) 
    // {
    //     $stmt = $this->db->prepare("
    //         INSERT INTO messages_media (message_id, media_type, file_path, file_name, file_size, created_at) 
    //         VALUES (?, ?, ?, ?, ?, NOW())
    //     ");
    //     return $stmt->execute([$messageId, $mediaType, $filePath, $fileName, $fileSize]);
    // }

    /**
     * Helper: Verwijder bericht bij fout
     */
    private function deleteMessage($messageId) 
    {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$messageId]);
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
            $currentUser = $this->getCurrentUser();
            $friendId = $_GET['friend_id'] ?? null;
            $lastMessageId = $_GET['last_message_id'] ?? 0;

            if (!$friendId) {
                echo json_encode(['success' => false, 'messages' => []]);
                exit;
            }

            $newMessages = $this->getNewMessagesAfter($currentUser['id'], $friendId, $lastMessageId);
            
            echo json_encode(['success' => true, 'messages' => $newMessages]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'messages' => []]);
        }
        exit;
    }

    /**
     * Controleren op nieuwe berichten (AJAX)
     */
    public function checkNewMessages() 
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'count' => 0]);
            exit;
        }

        try {
            $currentUser = $this->getCurrentUser();
            $unreadCount = $this->getUnreadMessageCount($currentUser['id']);
            
            echo json_encode(['success' => true, 'count' => $unreadCount]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'count' => 0]);
        }
        exit;
    }

    /**
     * DEBUG: Test methode
     */
    public function debugTest() 
    {
        $debug_data = [
            'handler' => 'ChatHandler',
            'method' => 'debugTest', 
            'timestamp' => date('Y-m-d H:i:s'),
            'database_connected' => $this->db ? 'YES' : 'NO',
            'chat_mode' => $this->getChatMode(),
            'theme_has_chat' => [
                'index' => $this->themeHasChatView('index'),
                'conversation' => $this->themeHasChatView('conversation'),
                'compose' => $this->themeHasChatView('compose')
            ]
        ];
        
        if (!isset($_SESSION['user_id'])) {
            $debug_data['user_status'] = 'NOT LOGGED IN';
        } else {
            $debug_data['user_status'] = 'LOGGED IN';
            $debug_data['user_id'] = $_SESSION['user_id'];
            
            $currentUser = $this->getCurrentUser();
            $debug_data['current_user'] = $currentUser;
            
            if ($currentUser && isset($currentUser['avatar'])) {
                $debug_data['avatar_input'] = $currentUser['avatar'];
                $debug_data['avatar_url'] = $this->getAvatarUrl($currentUser['avatar']);
            }
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM messages");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $debug_data['messages_count'] = $result['count'];
        } catch (Exception $e) {
            $debug_data['database_error'] = $e->getMessage();
        }

        $data = [
            'page_title' => 'ChatHandler Debug Test',
            'debug_data' => $debug_data
        ];
        
        return $this->view('chatservice/debug', $data);
    }

    // ... (rest van de methods blijven hetzelfde)
    
    /**
     * Database: Nieuw bericht aanmaken - GEFIXED voor juiste tabel structuur
     */
    private function createMessage($senderId, $receiverId, $content, $subject = '', $attachmentPath = null, $attachmentType = null) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, subject, content, type, attachment_path, attachment_type, created_at, is_read) 
            VALUES (?, ?, ?, ?, 'text', ?, ?, NOW(), 0)
        ");
        
        if ($stmt->execute([$senderId, $receiverId, $subject, $content, $attachmentPath, $attachmentType])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Database: Haal nieuwe berichten op na bepaald bericht ID
     */
    private function getNewMessagesAfter($userId, $friendId, $lastMessageId) 
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                sender.display_name as sender_name,
                sender.avatar as sender_avatar,
                m.attachment_path,
                m.attachment_type
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
            OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        
        $stmt->execute([$userId, $friendId, $friendId, $userId, $lastMessageId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
            if ($message['attachment_path']) {
                $message['media_url'] = base_url($message['attachment_path']);
                $message['media_type'] = $message['attachment_type'];
            }
        }
        
        return $messages;
    }

    private function getUserConversations($userId)
    {
        try {
            $db = Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                SELECT DISTINCT
                    friend_user.id as friend_id,
                    COALESCE(NULLIF(friend_profile.display_name, ''), friend_user.username) as friend_name,
                    friend_profile.avatar as friend_avatar,
                    latest.created_at as last_message_time,
                    latest.content as last_message,
                    latest.sender_id,
                    COALESCE(unread.unread_count, 0) as unread_count
                FROM (
                    SELECT 
                        CASE 
                            WHEN sender_id = ? THEN receiver_id 
                            ELSE sender_id 
                        END as friend_id,
                        MAX(created_at) as last_message_time
                    FROM messages 
                    WHERE sender_id = ? OR receiver_id = ?
                    GROUP BY friend_id
                ) conversations
                JOIN users friend_user ON friend_user.id = conversations.friend_id
                LEFT JOIN user_profiles friend_profile ON friend_profile.user_id = friend_user.id
                JOIN messages latest ON (
                    (latest.sender_id = ? AND latest.receiver_id = conversations.friend_id) OR
                    (latest.sender_id = conversations.friend_id AND latest.receiver_id = ?)
                ) AND latest.created_at = conversations.last_message_time
                LEFT JOIN (
                    SELECT 
                        sender_id as friend_id, 
                        COUNT(*) as unread_count
                    FROM messages 
                    WHERE receiver_id = ? AND is_read = 0
                    GROUP BY sender_id
                ) unread ON unread.friend_id = conversations.friend_id
                ORDER BY conversations.last_message_time DESC
            ");
            
            $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Voeg avatar URLs toe
            foreach ($conversations as &$conversation) {
                $conversation['friend_avatar_url'] = $this->getAvatarUrl($conversation['friend_avatar']);
            }
            
            return $conversations;
            
        } catch (Exception $e) {
            error_log("Error getting conversations: " . $e->getMessage());
            return [];
        }
    }

    private function getUserFriends($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, 
                   COALESCE(up.display_name, u.username) as display_name,
                   up.avatar
            FROM friendships f
            JOIN users u ON (f.friend_id = u.id OR f.user_id = u.id) AND u.id != ?
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE (f.user_id = ? OR f.friend_id = ?) AND f.status = 'accepted'
        ");
        $stmt->execute([$userId, $userId, $userId]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($friends as &$friend) {
            $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
        }
        
        return $friends;
    }

    private function getUnreadMessageCount($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM messages 
            WHERE receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    private function getUserById($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.email, 
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

    private function getConversationMessages($userId, $friendId) 
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                sender.display_name as sender_name,
                sender.avatar as sender_avatar,
                m.attachment_path,
                m.attachment_type
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
            OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        
        $stmt->execute([$userId, $friendId, $friendId, $userId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getAvatarUrl($message['sender_avatar']);
            if ($message['attachment_path']) {
                $message['media_url'] = base_url($message['attachment_path']);
                $message['media_type'] = $message['attachment_type'];
            }
        }
        
        return $messages;
    }

    private function markMessagesAsRead($userId, $friendId) 
    {
        $stmt = $this->db->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$friendId, $userId]);
    }

    
}