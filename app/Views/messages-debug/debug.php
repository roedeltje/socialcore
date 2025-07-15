<?php

namespace App\Handlers;

use App\Database\Database;
use PDO;
use Exception;

class MessageHandler 
{
    private $db;

    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Conversatie overzicht - toont alle gesprekken van de gebruiker
     */
    public function viewConversations() 
    {
        // Check of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUser = $this->getMessageCurrentUser();
        
        // Haal alle conversaties op voor deze gebruiker
        $conversations = $this->getUserConversations($currentUser['id']);

        // Data voorbereiden voor view
        $data = [
            'page_title' => 'Berichten',
            'conversations' => $conversations,
            'current_user' => $currentUser,
            'unread_count' => $this->getUnreadMessageCount($currentUser['id'])
        ];

        return $this->messageView('messages/index', $data);
    }

    /**
     * Specifieke conversatie weergeven
     */
    public function viewConversation() 
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUser = $this->getMessageCurrentUser();
        $friendId = $_GET['friend_id'] ?? null;

        if (!$friendId) {
            header('Location: ' . base_url('?route=messages'));
            exit;
        }

        // Haal vriend informatie op
        $friend = $this->getUserById($friendId);
        if (!$friend) {
            header('Location: ' . base_url('?route=messages'));
            exit;
        }

        // Haal berichten op tussen deze gebruikers
        $messages = $this->getConversationMessages($currentUser['id'], $friendId);
        
        // Markeer berichten als gelezen
        $this->markMessagesAsRead($currentUser['id'], $friendId);

        $data = [
            'page_title' => 'Gesprek met ' . htmlspecialchars($friend['display_name']),
            'friend' => $friend,
            'messages' => $messages,
            'current_user' => $currentUser
        ];

        return $this->messageView('messages/conversation', $data);
    }

    /**
     * Nieuw bericht verzenden
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
            $currentUser = $this->getMessageCurrentUser();
            $receiverId = $_POST['recipient_id'] ?? $_POST['receiver_id'] ?? null;
            $content = trim($_POST['content'] ?? '');
            $subject = trim($_POST['subject'] ?? '');

            // Validatie
            if (!$receiverId || !$content) {
                echo json_encode(['success' => false, 'message' => 'Ontvanger en bericht zijn verplicht']);
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

            // Bericht opslaan in database
            $messageId = $this->createMessage($currentUser['id'], $receiverId, $content, $subject);

            if ($messageId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Bericht verzonden',
                    'message_id' => $messageId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kon bericht niet verzenden']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server fout: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * DEBUG: Test methode
     */
    public function debugTest() 
    {
        $debug_data = ['method' => 'debugTest', 'timestamp' => date('Y-m-d H:i:s')];
        
        if (!isset($_SESSION['user_id'])) {
            $debug_data['user_status'] = 'NOT LOGGED IN';
        } else {
            $debug_data['user_status'] = 'LOGGED IN';
            $debug_data['user_id'] = $_SESSION['user_id'];
        }

        $data = ['debug_data' => $debug_data];
        return $this->messageView('messages/debug', $data);
    }

    /**
     * Nieuw bericht opstellen (compose pagina) - GEFIXED
     */
    public function composeMessage() 
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }

        $currentUser = $this->getMessageCurrentUser();
        $toUsername = $_GET['to_username'] ?? null;
        $toUser = null;

        // Debug logging
        error_log("Compose called - toUsername: " . var_export($toUsername, true));

        // Als er een specifieke ontvanger is opgegeven
        if ($toUsername) {
            $toUser = $this->getUserByUsername($toUsername);
            if (!$toUser) {
                error_log("User not found for username: $toUsername");
                $_SESSION['error_message'] = 'Gebruiker niet gevonden.';
                header('Location: ' . base_url('?route=messages'));
                exit;
            }
        }

        // Haal alle gebruikers op (voor debugging)
        $friends = $this->getUserFriends($currentUser['id']);
        error_log("Friends count: " . count($friends));
        
        if (empty($friends)) {
            error_log("No friends found, trying all users");
            // Extra fallback - haal gewoon alle users op
            $stmt = $this->db->prepare("
                SELECT u.id, u.username, 
                       COALESCE(up.display_name, u.username) as display_name,
                       up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id != ?
                ORDER BY display_name
                LIMIT 20
            ");
            $stmt->execute([$currentUser['id']]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($friends as &$friend) {
                $friend['avatar_url'] = $this->getMessageAvatarUrl($friend['avatar']);
            }
        }

        $data = [
            'page_title' => 'Nieuw bericht',
            'current_user' => $currentUser,
            'to_user' => $toUser,
            'friends' => $friends,
            'debug_friends_count' => count($friends),
            'debug_to_username' => $toUsername
        ];

        error_log("Compose data prepared, friends: " . count($friends));
        return $this->messageView('messages/compose', $data);
    }

    /**
     * Bericht beantwoorden in conversatie (AJAX)
     */
    public function replyMessage() 
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
            $currentUser = $this->getMessageCurrentUser();
            $receiverId = $_POST['recipient_id'] ?? $_POST['receiver_id'] ?? null;
            $content = trim($_POST['content'] ?? '');

            // Validatie
            if (!$receiverId || !$content) {
                echo json_encode(['success' => false, 'message' => 'Ontvanger en bericht zijn verplicht']);
                exit;
            }

            if (strlen($content) > 1000) {
                echo json_encode(['success' => false, 'message' => 'Bericht is te lang (max 1000 karakters)']);
                exit;
            }

            // Controleer foto upload als die er is
            $messageId = null;
            
            if (isset($_FILES['message_photo']) && !empty($_FILES['message_photo']['name'])) {
                // Eerst bericht aanmaken
                $messageId = $this->createMessage($currentUser['id'], $receiverId, $content);
                
                if ($messageId) {
                    // Dan foto uploaden en koppelen
                    $uploadResult = $this->handlePhotoUpload($_FILES['message_photo'], $messageId);
                    if (!$uploadResult['success']) {
                        echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                        exit;
                    }
                }
            } else {
                // Alleen tekst bericht
                $messageId = $this->createMessage($currentUser['id'], $receiverId, $content);
            }

            if ($messageId) {
                $message = $this->getMessageById($messageId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Bericht verzonden',
                    'message_data' => $message
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kon bericht niet verzenden']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server fout: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Berichten markeren als gelezen (AJAX)
     */
    public function markAsRead() 
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
            exit;
        }

        try {
            $currentUser = $this->getMessageCurrentUser();
            $friendId = $_POST['friend_id'] ?? null;

            if (!$friendId) {
                echo json_encode(['success' => false, 'message' => 'Vriend ID verplicht']);
                exit;
            }

            $this->markMessagesAsRead($currentUser['id'], $friendId);
            echo json_encode(['success' => true, 'message' => 'Berichten gemarkeerd als gelezen']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server fout: ' . $e->getMessage()]);
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
            $currentUser = $this->getMessageCurrentUser();
            $unreadCount = $this->getUnreadMessageCount($currentUser['id']);
            
            echo json_encode(['success' => true, 'count' => $unreadCount]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'count' => 0]);
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
            $currentUser = $this->getMessageCurrentUser();
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
     * Helper: Haal gebruiker op via username
     */
    private function getUserByUsername($username) 
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.email, 
                   COALESCE(up.display_name, u.username) as display_name,
                   up.avatar
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $user['avatar_url'] = $this->getMessageAvatarUrl($user['avatar']);
        }
        
        return $user;
    }

    /**
     * Helper: Haal vrienden van gebruiker op
     */
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
            $friend['avatar_url'] = $this->getMessageAvatarUrl($friend['avatar']);
        }
        
        return $friends;
    }

    /**
     * Helper: Photo upload afhandeling voor messages_media tabel
     */
    private function handlePhotoUpload($file, $messageId) 
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

        $uploadDir = 'public/uploads/messages/' . date('Y/m') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'message_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Voeg toe aan messages_media tabel
            $relativePath = str_replace('public/', '', $filepath);
            $this->createMessageMedia($messageId, 'image', $relativePath, $file['name'], $file['size']);
            
            return ['success' => true, 'path' => $relativePath];
        }

        return ['success' => false, 'message' => 'Kon bestand niet opslaan'];
    }

    /**
     * Database: Media record aanmaken in messages_media
     */
    private function createMessageMedia($messageId, $mediaType, $filePath, $fileName, $fileSize) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages_media (message_id, media_type, file_path, file_name, file_size, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$messageId, $mediaType, $filePath, $fileName, $fileSize]);
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
            $user['avatar_url'] = $this->getMessageAvatarUrl($user['avatar']);
        }
        
        return $user;
    }

    /**
     * Helper: Huidige gebruiker ophalen
     */
    private function getMessageCurrentUser() 
    {
        return $this->getUserById($_SESSION['user_id']);
    }

    /**
     * Helper: Avatar URL genereren (VERBETERD MET DEBUG)
     */
    private function getMessageAvatarUrl($avatar) 
    {
        // Debug logging
        error_log("Avatar input: " . var_export($avatar, true));
        
        if ($avatar && !empty($avatar)) {
            // Probeer verschillende pad combinaties
            $possiblePaths = [
                "public/uploads/avatars/" . $avatar,
                "uploads/avatars/" . $avatar,
                "public/" . $avatar,
                $avatar
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $url = base_url(str_replace('public/', '', $path));
                    error_log("Avatar found at: $path, URL: $url");
                    return $url;
                }
            }
            
            error_log("Avatar file not found in any path for: $avatar");
        }
        
        // Fallback naar default avatar
        $defaultUrl = base_url("theme-assets/default/images/default-avatar.png");
        error_log("Using default avatar: $defaultUrl");
        return $defaultUrl;
    }

    /**
     * Helper: View laden (GEFIXED - gebruik debug map)
     */
    private function messageView($viewPath, $data = []) 
    {
        // Extract data voor gebruik in view
        extract($data);
        
        // FORCE debug path - gebruik altijd debug map
        $debugViewPath = __DIR__ . "/../Views/messages-debug/" . str_replace('messages/', '', $viewPath) . ".php";
        
        if (file_exists($debugViewPath)) {
            // Laad thema header
            $headerPath = __DIR__ . "/../../themes/default/layouts/header.php";
            if (file_exists($headerPath)) {
                include $headerPath;
            }
            
            // Laad de debug view
            include $debugViewPath;
            
            // Laad thema footer
            $footerPath = __DIR__ . "/../../themes/default/layouts/footer.php";
            if (file_exists($footerPath)) {
                include $footerPath;
            }
        } else {
            // Error met absolute pad info
            echo "<h1>🚨 DEBUG VIEW NIET GEVONDEN</h1>";
            echo "<p><strong>Gezocht naar:</strong> " . htmlspecialchars($debugViewPath) . "</p>";
            echo "<p><strong>View path:</strong> " . htmlspecialchars($viewPath) . "</p>";
            echo "<p><strong>Bestaat:</strong> " . (file_exists($debugViewPath) ? 'JA' : 'NEE') . "</p>";
            echo "<p><strong>Directory exists:</strong> " . (is_dir(dirname($debugViewPath)) ? 'JA' : 'NEE') . "</p>";
            echo "<p><strong>__DIR__:</strong> " . __DIR__ . "</p>";
        }
        
        return true;
    }

    /**
     * Database: Haal alle conversaties op voor gebruiker (aangepast voor receiver_id)
     */
    private function getUserConversations($userId) 
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT
                CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END as friend_id,
                CASE WHEN m.sender_id = ? THEN recipient.display_name ELSE sender.display_name END as friend_name,
                CASE WHEN m.sender_id = ? THEN recipient.avatar ELSE sender.avatar END as friend_avatar,
                MAX(m.created_at) as last_message_time,
                m.content as last_message,
                COUNT(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 END) as unread_count
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            LEFT JOIN users recipient_u ON m.receiver_id = recipient_u.id  
            LEFT JOIN user_profiles recipient ON recipient_u.id = recipient.user_id
            WHERE m.sender_id = ? OR m.receiver_id = ?
            GROUP BY friend_id, friend_name, friend_avatar
            ORDER BY last_message_time DESC
        ");
        
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Avatar URLs toevoegen
        foreach ($conversations as &$conversation) {
            $conversation['friend_avatar_url'] = $this->getMessageAvatarUrl($conversation['friend_avatar']);
        }
        
        return $conversations;
    }

    /**
     * Database: Haal berichten op tussen twee gebruikers (aangepast voor receiver_id)
     */
    private function getConversationMessages($userId, $friendId) 
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   sender.display_name as sender_name,
                   sender.avatar as sender_avatar,
                   mm.file_path as media_path,
                   mm.media_type as media_type
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            LEFT JOIN messages_media mm ON m.id = mm.message_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        
        $stmt->execute([$userId, $friendId, $friendId, $userId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Avatar URLs en media URLs toevoegen
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getMessageAvatarUrl($message['sender_avatar']);
            if ($message['media_path']) {
                $message['media_url'] = base_url($message['media_path']);
            }
        }
        
        return $messages;
    }

    /**
     * Database: Nieuw bericht aanmaken (aangepast voor receiver_id en subject)
     */
    private function createMessage($senderId, $receiverId, $content, $subject = '') 
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, subject, content, type, created_at, is_read) 
            VALUES (?, ?, ?, ?, 'text', NOW(), 0)
        ");
        
        if ($stmt->execute([$senderId, $receiverId, $subject, $content])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Database: Markeer berichten als gelezen (aangepast voor receiver_id)
     */
    private function markMessagesAsRead($userId, $friendId) 
    {
        $stmt = $this->db->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$friendId, $userId]);
    }

    /**
     * Database: Tel ongelezen berichten (aangepast voor receiver_id)
     */
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

    /**
     * Database: Haal bericht op via ID (aangepast voor receiver_id en media)
     */
    private function getMessageById($messageId) 
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   sender.display_name as sender_name,
                   sender.avatar as sender_avatar,
                   mm.file_path as media_path,
                   mm.media_type as media_type
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            LEFT JOIN messages_media mm ON m.id = mm.message_id
            WHERE m.id = ?
        ");
        $stmt->execute([$messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message) {
            $message['sender_avatar_url'] = $this->getMessageAvatarUrl($message['sender_avatar']);
            if ($message['media_path']) {
                $message['media_url'] = base_url($message['media_path']);
            }
        }
        
        return $message;
    }

    /**
     * Database: Haal nieuwe berichten op na bepaald bericht ID (aangepast voor receiver_id)
     */
    private function getNewMessagesAfter($userId, $friendId, $lastMessageId) 
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   sender.display_name as sender_name,
                   sender.avatar as sender_avatar,
                   mm.file_path as media_path,
                   mm.media_type as media_type
            FROM messages m
            LEFT JOIN users sender_u ON m.sender_id = sender_u.id
            LEFT JOIN user_profiles sender ON sender_u.id = sender.user_id
            LEFT JOIN messages_media mm ON m.id = mm.message_id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?))
               AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        
        $stmt->execute([$userId, $friendId, $friendId, $userId, $lastMessageId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($messages as &$message) {
            $message['sender_avatar_url'] = $this->getMessageAvatarUrl($message['sender_avatar']);
            if ($message['media_path']) {
                $message['media_url'] = base_url($message['media_path']);
            }
        }
        
        return $messages;
    }
}