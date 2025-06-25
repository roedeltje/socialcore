<?php

namespace App\Controllers;

use App\Database\Database;
use App\Auth\Auth;
use PDO;
use Exception;

class FriendsController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * Verstuur vriendschapsverzoek
     */
    public function add()
{
    // Controleer of gebruiker is ingelogd
    if (!Auth::check()) {
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Je moet ingelogd zijn']);
        }
        header('Location: /auth/login');
        exit;
    }
    
    // Haal username uit URL of POST data
    $username = $_GET['user'] ?? $_POST['user'] ?? null;
    if (!$username) {
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Gebruiker niet gevonden']);
        }
        $_SESSION['error'] = 'Gebruiker niet gevonden';
        header('Location: /');
        exit;
    }
    
    // Zoek de gebruiker op
    $friend = $this->getUserByUsername($username);
    if (!$friend) {
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Gebruiker niet gevonden']);
        }
        $_SESSION['error'] = 'Gebruiker niet gevonden';
        header('Location: /');
        exit;
    }
    
    $currentUserId = $_SESSION['user_id'];
    $friendId = $friend['id'];
    
    // Controleer of je niet jezelf probeert toe te voegen
    if ($currentUserId == $friendId) {
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Je kunt jezelf niet als vriend toevoegen']);
        }
        $_SESSION['error'] = 'Je kunt jezelf niet als vriend toevoegen';
        header("Location: /profile?user=$username");
        exit;
    }
    
    // Controleer of er al een vriendschap bestaat
    if ($this->friendshipExists($currentUserId, $friendId)) {
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Vriendschapsverzoek al verstuurd of jullie zijn al vrienden']);
        }
        $_SESSION['error'] = 'Vriendschapsverzoek al verstuurd of jullie zijn al vrienden';
        header("Location: /profile?user=$username");
        exit;
    }
    
    // Verstuur vriendschapsverzoek
    try {
        $stmt = $this->db->prepare("
            INSERT INTO friendships (user_id, friend_id, status, created_at, updated_at) 
            VALUES (?, ?, 'pending', NOW(), NOW())
        ");
        $stmt->execute([$currentUserId, $friendId]);
        
        $successMessage = "Vriendschapsverzoek verstuurd naar {$friend['display_name']}!";
        
        if ($this->isAjaxRequest()) {
            $this->jsonResponse([
                'success' => true, 
                'message' => $successMessage,
                'friend_name' => $friend['display_name']
            ]);
        }
        
        $_SESSION['success'] = $successMessage;
        
    } catch (Exception $e) {
        error_log('Friendship request error: ' . $e->getMessage());
        
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Er ging iets mis bij het versturen van het verzoek']);
        }
        
        $_SESSION['error'] = 'Er ging iets mis bij het versturen van het verzoek';
    }
    
    header("Location: /profile?user=$username");
    exit;
}

/**
 * Controleer of het een AJAX request is
 */
private function isAjaxRequest()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Stuur JSON response en stop uitvoering
 */
private function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
    
    /**
     * Accepteer vriendschapsverzoek
     */
    public function accept()
    {
        if (!Auth::check()) {
            header('Location: /auth/login');
            exit;
        }
        
        $friendshipId = $_POST['friendship_id'] ?? null;
        if (!$friendshipId) {
            $_SESSION['error'] = 'Ongeldig verzoek';
            header('Location: /friends/requests');
            exit;
        }
        
        $currentUserId = $_SESSION['user_id'];
        
        try {
            // Update de status naar 'accepted'
            $stmt = $this->db->prepare("
                UPDATE friendships 
                SET status = 'accepted', updated_at = NOW() 
                WHERE id = ? AND friend_id = ? AND status = 'pending'
            ");
            $stmt->execute([$friendshipId, $currentUserId]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Vriendschapsverzoek geaccepteerd!';
            } else {
                $_SESSION['error'] = 'Verzoek niet gevonden';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Er ging iets mis';
        }
        
        header('Location: /friends/requests');
        exit;
    }
    
    /**
     * Weiger vriendschapsverzoek
     */
    public function decline()
    {
        if (!Auth::check()) {
            header('Location: /auth/login');
            exit;
        }
        
        $friendshipId = $_POST['friendship_id'] ?? null;
        if (!$friendshipId) {
            $_SESSION['error'] = 'Ongeldig verzoek';
            header('Location: /friends/requests');
            exit;
        }
        
        $currentUserId = $_SESSION['user_id'];
        
        try {
            // Verwijder het verzoek (of update naar 'declined')
            $stmt = $this->db->prepare("
                DELETE FROM friendships 
                WHERE id = ? AND friend_id = ? AND status = 'pending'
            ");
            $stmt->execute([$friendshipId, $currentUserId]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Vriendschapsverzoek geweigerd';
            } else {
                $_SESSION['error'] = 'Verzoek niet gevonden';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Er ging iets mis';
        }
        
        header('Location: /friends/requests');
        exit;
    }
    
    /**
     * Toon vriendschapsverzoeken pagina
     */
    public function requests()
    {
        if (!Auth::check()) {
            header('Location: /auth/login');
            exit;
        }
        
        $currentUserId = $_SESSION['user_id'];
        
        // Haal pending verzoeken op (waar current user de ontvanger is)
        $stmt = $this->db->prepare("
            SELECT 
                f.id as friendship_id,
                f.created_at,
                u.id as user_id,
                u.username,
                up.display_name,
                up.avatar
            FROM friendships f
            JOIN users u ON f.user_id = u.id
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE f.friend_id = ? AND f.status = 'pending'
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$currentUserId]);
        $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fix de avatar URLs voor elke request
        foreach ($pendingRequests as &$request) {
            $request['avatar_url'] = $this->getAvatarUrl($request['avatar']);
            
            // Format ook de display name als fallback
            if (empty($request['display_name'])) {
                $request['display_name'] = $request['username'];
            }
        }
        
        $this->view('friends/requests', [
            'pendingRequests' => $pendingRequests,
            'title' => 'Vriendschapsverzoeken'
        ]);
    }
    
    /**
     * Toon alle vrienden van current user
     */
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /auth/login');
            exit;
        }
        
        $currentUserId = $_SESSION['user_id']; // Aangepast van Auth::user()['id']
        
        // Haal alle vrienden op (waar status = 'accepted')
        $stmt = $this->db->prepare("
            SELECT 
                u.id as user_id,
                u.username,
                up.display_name,
                up.avatar,
                f.created_at as friends_since
            FROM friendships f
            JOIN users u ON (
                CASE 
                    WHEN f.user_id = ? THEN u.id = f.friend_id
                    ELSE u.id = f.user_id
                END
            )
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE (f.user_id = ? OR f.friend_id = ?) 
            AND f.status = 'accepted'
            ORDER BY up.display_name ASC
        ");
        $stmt->execute([$currentUserId, $currentUserId, $currentUserId]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fix de avatar URLs voor elke vriend
        foreach ($friends as &$friend) {
            $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
            
            // Format ook de display name als fallback
            if (empty($friend['display_name'])) {
                $friend['display_name'] = $friend['username'];
            }
        }
        
        $this->view('friends/index', [
            'friends' => $friends,
            'friendCount' => count($friends),
            'title' => 'Mijn Vrienden'
        ]);
    }
    
    /**
     * Helper: Zoek gebruiker op username
     */
    private function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, up.display_name 
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Helper: Controleer of vriendschap al bestaat
     */
    private function friendshipExists($userId1, $userId2)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM friendships 
            WHERE (user_id = ? AND friend_id = ?) 
            OR (user_id = ? AND friend_id = ?)
        ");
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Helper: Haal vriendschapsstatus op
     */
    public function getFriendshipStatus($userId1, $userId2)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, status, user_id, friend_id, created_at, updated_at
                FROM friendships 
                WHERE (user_id = ? AND friend_id = ?) 
                OR (user_id = ? AND friend_id = ?)
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: false;
        } catch (\Exception $e) {
            error_log("Error getting friendship status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper functie om de volledige avatar URL te krijgen
     * Verplaatst naar Controller.php
     */
}