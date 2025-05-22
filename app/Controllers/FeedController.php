<?php

namespace App\Controllers;

use App\Database\Database;
use PDO;           // Deze was missing!
use Exception;     // Deze ook!
use PDOException;

class FeedController extends Controller
{
    private $db;
    
    public function __construct()
    {
        // Correcte manier - gebruik getPdo()
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon de hoofdpagina van de nieuwsfeed
     */
    public function index()
{
    // Controleer of gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        header('Location: /auth/login');
        exit;
    }

    try {
        // Haal echte posts op uit de database
        $posts = $this->getAllPosts();
        
        // Haal gebruikersinfo op
        $currentUser = $this->getCurrentUser($_SESSION['user_id']);
        
        // Dummy data voor nu (later vervangen we dit ook)
        $onlineFriends = $this->getOnlineFriends();
        $trendingHashtags = $this->getTrendingHashtags();
        $suggestedUsers = $this->getSuggestedUsers();
        
        // Data doorsturen naar de view
        $data = [
            'posts' => $posts,
            'current_user' => $currentUser,
            'online_friends' => $onlineFriends,
            'trending_hashtags' => $trendingHashtags,
            'suggested_users' => $suggestedUsers
        ];
        
        $this->view('feed/index', $data);
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Er ging iets mis bij het laden van de feed: ' . $e->getMessage();
        $this->view('feed/index', [
            'posts' => [],
            'current_user' => ['name' => 'Gebruiker', 'username' => 'user'],
            'online_friends' => [],
            'trending_hashtags' => [],
            'suggested_users' => []
        ]);
    }
}

        private function getAllPosts($limit = 20)
{
    $query = "
        SELECT 
            p.id,
            p.content,
            p.type,
            p.created_at,
            p.likes_count,
            p.comments_count,
            u.id as user_id,
            u.username,
            COALESCE(up.display_name, u.username) as user_name
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE p.is_deleted = 0
        ORDER BY p.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$limit]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format de data voor de view
    foreach ($posts as &$post) {
        $post['likes'] = $post['likes_count'];
        $post['comments'] = $post['comments_count'];
        $post['created_at'] = $this->formatDate($post['created_at']);
    }
    
    return $posts;
}

    private function getCurrentUser($userId)
{
    $query = "
        SELECT 
            u.id,
            u.username,
            COALESCE(up.display_name, u.username) as name,
            COUNT(DISTINCT p.id) as post_count
        FROM users u
        LEFT JOIN user_profiles up ON u.id = up.user_id
        LEFT JOIN posts p ON u.id = p.user_id AND p.is_deleted = 0
        WHERE u.id = ?
        GROUP BY u.id
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Voeg dummy data toe voor nu
        $user['following'] = 42;
        $user['followers'] = 127;
        return $user;
    }
    
    // Fallback als user niet gevonden
    return [
        'id' => $userId,
        'name' => 'Gebruiker',
        'username' => 'user',
        'post_count' => 0,
        'following' => 0,
        'followers' => 0
    ];
}

    private function formatDate($datetime)
{
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
}

// Tijdelijke dummy data functies (later vervangen we deze)
    private function getOnlineFriends()
{
    return [
        ['id' => 1, 'name' => 'Lucas van der Berg'],
        ['id' => 2, 'name' => 'Emma Janssen'],
        ['id' => 3, 'name' => 'Sophie de Vries']
    ];
}

    private function getTrendingHashtags()
{
    return [
        ['tag' => 'socialcore', 'count' => 234],
        ['tag' => 'nederland', 'count' => 189],
        ['tag' => 'opensource', 'count' => 156]
    ];
}

    private function getSuggestedUsers()
{
    return [
        ['id' => 4, 'name' => 'Tim Bakker'],
        ['id' => 5, 'name' => 'Nina Peters'],
        ['id' => 6, 'name' => 'Robin de Jong'],
        ['id' => 7, 'name' => 'Laura Smit']
    ];
}

        public function create() 
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Je moet ingelogd zijn om te posten';
            header('Location: /auth/login');
            exit;
        }
        
        // Als het een GET request is: redirect naar feed
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Location: /feed');
            exit;
        }
        
        // Als het een POST request is: verwerk het formulier
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validatie
                if (empty($_POST['content'])) {
                    throw new Exception('Bericht mag niet leeg zijn');
                }
                
                if (strlen($_POST['content']) > 1000) {
                    throw new Exception('Bericht mag maximaal 1000 karakters bevatten');
                }
                
                // Bepaal het type bericht (voor nu alleen tekst)
                $type = 'text';
                
                // Roep de business logic functie aan
                $postId = $this->createPost($_SESSION['user_id'], $_POST['content'], $type);
                
                // Success message
                $_SESSION['success_message'] = 'Bericht succesvol geplaatst!';
                
            } catch (Exception $e) {
                // Error message
                $_SESSION['error_message'] = $e->getMessage();
                $_SESSION['old_content'] = $_POST['content'] ?? '';
            }
            
            // Redirect terug naar feed
            header('Location: /feed');
            exit;
        }
    }
    
    public function createPost($userId, $content, $type = 'text') 
    {
        try {
            // Controleer of user bestaat
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception("Gebruiker bestaat niet");
            }
            
            // Maak de post aan
            $stmt = $this->db->prepare("INSERT INTO posts (user_id, content, type) VALUES (?, ?, ?)");
            $success = $stmt->execute([$userId, $content, $type]);
            
            if ($success) {
                return $this->db->lastInsertId(); // Geef het nieuwe post ID terug
            } else {
                throw new Exception("Kon bericht niet opslaan");
            }
            
        } catch (PDOException $e) {
            throw new Exception("Database fout: " . $e->getMessage());
        }
    }

        /**
     * Toggle like op een post (like/unlike)
     */
    public function toggleLike()
{
    // Controleer of gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
        exit;
    }
    
    // Controleer of het een POST request is
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
        exit;
    }
    
    $postId = $_POST['post_id'] ?? null;
    $userId = $_SESSION['user_id'];
    
    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID is verplicht']);
        exit;
    }
    
    try {
        // Controleer of post bestaat
        $stmt = $this->db->prepare("SELECT id FROM posts WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post niet gevonden']);
            exit;
        }
        
        // Controleer of gebruiker deze post al heeft geliked
        $stmt = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingLike) {
            // Unlike: verwijder de like
            $this->removeLike($postId, $userId);
            $action = 'unliked';
        } else {
            // Like: voeg like toe
            $this->addLike($postId, $userId);
            $action = 'liked';
        }
        
        // Haal nieuwe like count op
        $newLikeCount = $this->getLikeCount($postId);
        
        echo json_encode([
            'success' => true,
            'action' => $action,
            'like_count' => $newLikeCount
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()]);
    }
    
    exit;
}

    /**
     * Voeg een like toe
     */
    private function addLike($postId, $userId)
{
    // Begin transaction
    $this->db->beginTransaction();
    
    try {
        // Voeg like toe aan post_likes tabel
        $stmt = $this->db->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $userId]);
        
        // Update likes_count in posts tabel
        $stmt = $this->db->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?");
        $stmt->execute([$postId]);
        
        $this->db->commit();
        
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

    /**
     * Verwijder een like
     */
    private function removeLike($postId, $userId)
{
    // Begin transaction
    $this->db->beginTransaction();
    
    try {
        // Verwijder like uit post_likes tabel
        $stmt = $this->db->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        
        // Update likes_count in posts tabel (maar niet onder 0)
        $stmt = $this->db->prepare("UPDATE posts SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
        $stmt->execute([$postId]);
        
        $this->db->commit();
        
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

    /**
     * Haal het aantal likes op voor een post
     */
    private function getLikeCount($postId)
{
    $stmt = $this->db->prepare("SELECT likes_count FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['likes_count'] : 0;
}
    
    /**
     * Een methode voor het ophalen van meer posts (bijv. voor oneindige scroll)
     */
    public function loadMore()
    {
        // Functionaliteit voor het laden van meer posts
        // Komt in een latere fase
    }
}