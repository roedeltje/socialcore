<?php

namespace App\Controllers;

use App\Database\Database;
use PDO;           // Deze was missing!
use Exception;     // Deze ook!
use PDOException;

require_once __DIR__ . '/../../core/helpers/upload.php';

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

        // Debug huidige gebruiker
        //$currentUser = $this->getCurrentUser($_SESSION['user_id']);
        //var_dump($currentUser);

        
        
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
            COALESCE(up.display_name, u.username) as user_name,
            (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path
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
        
        // Bewaar de originele created_at
        $original_date = $post['created_at'];
        
        // Formatteer de datum correct
        $post['created_at'] = $this->formatDate($original_date);
        
        // Gebruik dezelfde geformatteerde datum voor time_ago
        $post['time_ago'] = $post['created_at'];
        
        // Voeg avatar toe
        $post['avatar'] = $this->getUserAvatar($post['user_id']);
        
        // Controleer of de huidige gebruiker de post heeft geliked
        $post['is_liked'] = $this->hasUserLikedPost($post['id']);
    }
    
    return $posts;
    }

    /**
     * Controleer of de huidige gebruiker een post heeft geliked
     */
    private function hasUserLikedPost($postId)
    {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    
    return $stmt->fetchColumn() > 0;
    }

    /**
     * Haal de avatar URL op voor een gebruiker
     */
    private function getUserAvatar($userId)
    {
    // Je kunt deze functie later uitbreiden om echte avatars op te halen
    // Voor nu gebruiken we een default avatar
    return 'public/assets/images/default-avatar.png';
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

    /**
     * Formatteer een datetime naar een leesbare weergave
     * 
     * @param string $datetime Een SQL datetime string (Y-m-d H:i:s)
     * @return string Geformatteerde datum/tijd
     */
    private function formatDate($datetime) 
    {
    // Controleer of input geldig is, anders geef een veilige fallback
    if (empty($datetime) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime)) {
        return 'onbekende tijd';
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
        // Bij fouten, geef een veilige fallback
        return 'onbekende tijd';
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

        /**
         * Verwerk het POST request voor het maken van een nieuwe post
         */
        public function create() 
        {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' === 'XMLHttpRequest') {
                // Als het een AJAX request is, stuur JSON terug
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om te posten']);
                exit;
            } else {
                // Anders redirect naar login
                $_SESSION['error_message'] = 'Je moet ingelogd zijn om te posten';
                header('Location: /auth/login');
                exit;
            }
        }
        
        // Als het een GET request is: redirect naar feed
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Location: /feed');
            exit;
        }
        
        // Als het een POST request is: verwerk het formulier
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->createPost();
            
            // Controleer of het een AJAX request is
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' === 'XMLHttpRequest') {
                // Als het een AJAX request is, stuur JSON terug
                header('Content-Type: application/json');
                echo json_encode($result);
                exit;
            } else {
                // Anders, sla het resultaat op in de sessie en redirect
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = $result['message'];
                    $_SESSION['old_content'] = $_POST['content'] ?? '';
                }
                
                // Redirect terug naar feed
                header('Location: /feed');
                exit;
            }
        }
    }
    
    public function createPost($userId = null, $content = null, $type = 'text') 
    {
        // Gebruik sessie user_id als deze niet expliciet is doorgegeven
        $userId = $userId ?? ($_SESSION['user_id'] ?? 0);
        
        // Haal content uit POST als deze niet expliciet is doorgegeven
        $content = $content ?? trim($_POST['content'] ?? '');
        
        // Validatie
        if (empty($content) && empty($_FILES['image']['name'])) {
            // Bericht kan niet leeg zijn als er ook geen afbeelding is
            return [
                'success' => false,
                'message' => 'Voeg tekst of een afbeelding toe aan je bericht.'
            ];
        }
        
        if (strlen($content) > 1000) {
            return [
                'success' => false,
                'message' => 'Je bericht mag maximaal 1000 tekens bevatten.'
            ];
        }
        
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Je moet ingelogd zijn om een bericht te plaatsen.'
            ];
        }
        
        // Controleer of user bestaat
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => "Gebruiker bestaat niet"
            ];
        }
        
        // Bepaal het type post (text of image)
        $post_type = $type;  // Default waarde uit de parameter (meestal 'text')
        $image_path = null;

        // Controleer of er een afbeelding is geüpload
        if (!empty($_FILES['image']['name'])) {
            error_log('Afbeelding upload poging: ' . $_FILES['image']['name']);
            error_log('Bestandsgrootte: ' . $_FILES['image']['size']);
            error_log('Temp bestand: ' . $_FILES['image']['tmp_name']);
            error_log('Error code: ' . $_FILES['image']['error']);
            
            // Controleer of temp bestand bestaat
            if (file_exists($_FILES['image']['tmp_name'])) {
                error_log('Temp bestand bestaat');
            } else {
                error_log('Temp bestand bestaat NIET');
            }
            
            // Eenvoudige directe upload zonder helper
            $year = date('Y');
            $month = date('m');
            $upload_dir = BASE_PATH . '/public/uploads/posts/' . $year . '/' . $month;
            
            // Maak directory als deze niet bestaat
            if (!is_dir($upload_dir)) {
                error_log('Maak directory: ' . $upload_dir);
                mkdir($upload_dir, 0755, true);
            }
            
            // Genereer unieke bestandsnaam
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = 'post_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . '/' . $file_name;
            
            error_log('Upload pad: ' . $upload_path);
            
            // Verplaats het bestand
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                error_log('Bestand succesvol geüpload');
                $image_path = 'posts/' . $year . '/' . $month . '/' . $file_name;
                $post_type = 'photo';  // Gebruik 'photo' (een van de toegestane ENUM waarden)
            } else {
                $error = error_get_last();
                error_log('Upload fout: ' . ($error ? $error['message'] : 'Onbekende fout'));
                return [
                    'success' => false, 
                    'message' => 'Fout bij het uploaden van de afbeelding: ' . 
                                ($error ? $error['message'] : 'Onbekende fout')
                ];
            }
        }

        // Bij het maken van de post, gebruik de correcte ENUM waarde
        try {
            // Begin een transactie
            $this->db->beginTransaction();
            
            // Maak eerst de post aan met de juiste ENUM waarde
            $stmt = $this->db->prepare("INSERT INTO posts (user_id, content, type, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $content, $post_type]);
            $post_id = $this->db->lastInsertId();
            
            // Als er een afbeelding is, sla deze op in de post_media tabel
            if ($post_type === 'photo' && $image_path) {
            // Zorg ervoor dat post_media tabel bestaat
            $this->ensurePostMediaTable();
            
            // Bepaal het juiste media_type op basis van MIME type
            $mime_type = $_FILES['image']['type'];
            $media_type = 'image'; // Standaard voor afbeeldingen
            
            if (strpos($mime_type, 'video/') === 0) {
                $media_type = 'video';
            } elseif (strpos($mime_type, 'audio/') === 0) {
                $media_type = 'audio';
            }
            
            // Vul alle verplichte kolommen in
            $stmt = $this->db->prepare("
                INSERT INTO post_media (
                    post_id, 
                    file_path, 
                    media_type,
                    file_name,
                    file_size,
                    alt_text,
                    thumbnail_path,
                    display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $post_id,                   // post_id
                $image_path,                // file_path
                $media_type,                // media_type (nu een van de toegestane ENUM waarden)
                $_FILES['image']['name'],   // file_name
                $_FILES['image']['size'],   // file_size
                '',                         // alt_text (leeg, maar vereist)
                '',                         // thumbnail_path (leeg, maar vereist)
                0                           // display_order
            ]);
            }
            
            // Commit de transactie
            $this->db->commit();
            
            error_log('Post succesvol aangemaakt: ID=' . $post_id);
            
            return [
                'success' => true,
                'message' => 'Je bericht is geplaatst!',
                'post_id' => $post_id
            ];
        } catch (PDOException $e) {
            // Rollback bij fouten
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log('Database fout: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Database fout: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            // Rollback bij fouten
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log('Algemene fout: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Zorg ervoor dat de post_media tabel bestaat
     */
    private function ensurePostMediaTable() 
    {
        try {
            // Controleer of post_media tabel bestaat
            $stmt = $this->db->query("SHOW TABLES LIKE 'post_media'");
            if ($stmt->rowCount() == 0) {
                // Maak post_media tabel aan
                $createMediaTable = "CREATE TABLE `post_media` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `post_id` INT NOT NULL,
                    `file_path` VARCHAR(255) NOT NULL,
                    `file_type` VARCHAR(50) NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE
                )";
                $this->db->query($createMediaTable);
            }
            
            // Controleer of 'type' kolom bestaat in posts tabel
            $stmt = $this->db->query("SHOW COLUMNS FROM `posts` LIKE 'type'");
            if ($stmt->rowCount() == 0) {
                // Voeg type kolom toe als deze niet bestaat
                $this->db->query("ALTER TABLE `posts` ADD COLUMN `type` VARCHAR(20) DEFAULT 'text' AFTER `content`");
            }
        } catch (Exception $e) {
            // Log de fout maar gooi hem niet opnieuw, zodat we verder kunnen
            error_log('Fout bij het controleren/aanmaken van post_media tabel: ' . $e->getMessage());
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