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
        
        // Voeg comments toe aan alle posts
        $posts = $this->getCommentsForPosts($posts);
        
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

        // Helper om de avatar van een gebruiker op te halen
        private function getUserAvatar($userId)
        {
            // Implementeer dit op basis van je gebruikersprofielsysteem
            // Bijvoorbeeld:
            try {
                $stmt = $this->db->prepare("
                    SELECT avatar FROM user_profiles 
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && !empty($result['avatar'])) {
                    return base_url('uploads/' . $result['avatar']);
                }
            } catch (Exception $e) {
                error_log('Fout bij ophalen avatar: ' . $e->getMessage());
            }
            
            // Fallback naar default avatar
            return base_url('theme-assets/default/images/default-avatar.png');
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
         * Haalt de huidige ingelogde gebruiker op uit de database
         * 
         * @param int $userId De gebruikers-ID om op te halen
         * @return array Array met gebruikersgegevens, met gegarandeerde sleutels
         */
        private function getCurrentUser($userId = null)
        {
            // Gebruik sessie user_id als geen specifieke ID is gegeven
            $userId = $userId ?? ($_SESSION['user_id'] ?? null);
            
            if (!$userId) {
                // Geen gebruiker gevonden, retourneer standaardwaarden
                return [
                    'id' => 0,
                    'name' => 'Gast',
                    'username' => 'gast',
                    'display_name' => 'Gast',
                    'post_count' => 0,
                    'following' => 0,
                    'followers' => 0
                ];
            }

            try {
                // Haal gebruikersgegevens op
                $stmt = $this->db->prepare("
                    SELECT u.*, up.avatar, up.bio, up.location, up.website
                    FROM users u
                    LEFT JOIN user_profiles up ON u.id = up.user_id
                    WHERE u.id = ?
                ");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    // Gebruiker niet gevonden in database
                    return [
                        'id' => 0,
                        'name' => 'Onbekende gebruiker',
                        'username' => 'onbekend',
                        'display_name' => 'Onbekende gebruiker',
                        'post_count' => 0,
                        'following' => 0,
                        'followers' => 0
                    ];
                }
                
                // Zorg ervoor dat alle benodigde sleutels bestaan
                $user['avatar_url'] = !empty($user['avatar']) 
                ? base_url('uploads/' . $user['avatar'])
                : base_url('theme-assets/default/images/default-avatar.png');
                    
                // Voeg 'name' sleutel toe (dit is waar de fout optreedt)
                $user['name'] = $user['display_name'] ?? $user['username'] ?? 'Gebruiker';
                
                // Zorg ervoor dat alle statistieken beschikbaar zijn
                $user['post_count'] = $this->getUserPostCount($userId);
                $user['following'] = $user['following'] ?? 0;
                $user['followers'] = $user['followers'] ?? 0;
                
                return $user;
            } catch (PDOException $e) {
                error_log('Fout bij ophalen huidige gebruiker: ' . $e->getMessage());
                // Fallback bij database fout
                return [
                    'id' => $userId,
                    'name' => 'Gebruiker',
                    'username' => 'gebruiker',
                    'display_name' => 'Gebruiker',
                    'post_count' => 0,
                    'following' => 0,
                    'followers' => 0
                ];
            }
        }

        /**
         * Hulpfunctie om het aantal posts van een gebruiker te tellen
         */
        private function getUserPostCount($userId)
        {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
                $stmt->execute([$userId]);
                return (int)$stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log('Fout bij tellen posts: ' . $e->getMessage());
                return 0;
            }
        }

        /**
         * Verwerk het POST request voor het maken van een nieuwe post
         */
        public function create()
        {
            // Controleer of gebruiker is ingelogd
            if (!isset($_SESSION['user_id'])) {
                if ($this->isJsonRequest()) {
                    $this->jsonResponse(['success' => false, 'message' => 'Niet ingelogd']);
                } else {
                    $_SESSION['error'] = 'Je moet ingelogd zijn om een bericht te plaatsen.';
                    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/');
                    exit;
                }
            }

            // Verwerk het formulier
            $result = $this->createPost();

            // Return de juiste response
            if ($this->isJsonRequest()) {
                // Voeg extra data toe voor AJAX requests
                if ($result['success'] && isset($result['post_id'])) {
                    // Haal de volledige post data op voor de frontend
                    $post = $this->getPostById($result['post_id']);
                    if ($post) {
                        $result['post'] = $post;
                    }
                }
                
                $this->jsonResponse($result);
            } else {
                // Voor reguliere form submits
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
                
                header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/');
                exit;
            }
        }

        // Helper om te controleren of het een JSON request is
        private function isJsonRequest()
        {
            return (isset($_SERVER['HTTP_ACCEPT']) && 
                    strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) || 
                (isset($_SERVER['CONTENT_TYPE']) && 
                    strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
        }

        // Helper om JSON response te sturen
        private function jsonResponse($data)
        {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }

        // Functie om een post op te halen op basis van ID
        private function getPostById($postId)
        {
            try {
                // Haal de post inclusief gebruikersdata op
                $stmt = $this->db->prepare("
                    SELECT p.*, u.username, u.display_name 
                    FROM posts p 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.id = ?
                ");
                $stmt->execute([$postId]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    return null;
                }
                
                // Voeg avatar en formatted date toe voor de frontend
                $post['avatar'] = $this->getUserAvatar($post['user_id']);
                $post['formatted_date'] = 'Zojuist geplaatst';
                
                // Als het een foto post is, haal de bijbehorende media op
                if ($post['type'] === 'photo') {
                    $stmt = $this->db->prepare("
                        SELECT * FROM post_media 
                        WHERE post_id = ? 
                        ORDER BY display_order ASC
                    ");
                    $stmt->execute([$postId]);
                    $media = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($media) {
                        $post['image_url'] = base_url('uploads/' . $media['file_path']);
                    }
                }
                
                return $post;
            } catch (Exception $e) {
                error_log('Fout bij ophalen post: ' . $e->getMessage());
                return null;
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
         * Verwijder een bericht
         * Deze methode kan via AJAX of via een normale request worden aangeroepen
         */
        public function delete()
        {
            // Controleer of gebruiker is ingelogd
            if (!isset($_SESSION['user_id'])) {
                // Bij AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Je moet ingelogd zijn om een bericht te verwijderen'
                    ]);
                    exit;
                }
                
                // Bij normale request
                set_flash_message('error', 'Je moet ingelogd zijn om een bericht te verwijderen');
                redirect('login');
                return;
            }
            
            // Haal post ID op
            $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
            
            if (!$postId) {
                // Bij AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ongeldig bericht ID'
                    ]);
                    exit;
                }
                
                // Bij normale request
                set_flash_message('error', 'Ongeldig bericht ID');
                redirect('feed');
                return;
            }
            
            try {
                // Controleer of de gebruiker eigenaar is van het bericht of een admin
                $userId = $_SESSION['user_id'];
                $userRole = $_SESSION['role'] ?? 'user';
                $isAdmin = ($userRole === 'admin');
                
                $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    throw new \Exception('Bericht niet gevonden');
                }
                
                $isOwner = ($post['user_id'] == $userId);
                
                // Alleen eigenaar of admin mag verwijderen
                if (!$isOwner && !$isAdmin) {
                    throw new \Exception('Je hebt geen toestemming om dit bericht te verwijderen');
                }
                
                // Start een transactie om gerelateerde records ook te verwijderen
                $this->db->beginTransaction();
                
                // We gebruiken soft delete (is_deleted vlag)
                $stmt = $this->db->prepare("UPDATE posts SET is_deleted = 1 WHERE id = ?");
                $success = $stmt->execute([$postId]);
                
                if (!$success) {
                    throw new \Exception('Fout bij het verwijderen van het bericht');
                }
                
                // Commit de transactie
                $this->db->commit();
                
                // Bij AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Bericht succesvol verwijderd'
                    ]);
                    exit;
                }
                
                // Bij normale request
                set_flash_message('success', 'Bericht succesvol verwijderd');
                
                // Redirect naar de juiste pagina (referer of fallback naar feed)
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
                    redirect($referer);
                } else {
                    redirect('feed');
                }
                
            } catch (\Exception $e) {
                // Bij een fout, rollback de transactie
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                
                // Bij AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                    exit;
                }
                
                // Bij normale request
                set_flash_message('error', $e->getMessage());
                redirect('feed');
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

    public function addComment()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om te reageren']);
            exit;
        }
        
        // Controleer of het een POST request is
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
            exit;
        }
        
        // Haal de gegevens op uit het formulier
        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['comment_content'] ?? '');
        $userId = $_SESSION['user_id'];
        
        // Validatie
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID is verplicht']);
            exit;
        }
        
        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Reactie mag niet leeg zijn']);
            exit;
        }
        
        if (strlen($content) > 500) {
            echo json_encode(['success' => false, 'message' => 'Reactie mag maximaal 500 karakters bevatten']);
            exit;
        }
        
        try {
            // Controleer of de post bestaat
            $stmt = $this->db->prepare("SELECT id FROM posts WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                echo json_encode(['success' => false, 'message' => 'Post niet gevonden']);
                exit;
            }
            
            // Voeg de comment toe aan de database
            $result = $this->saveComment($postId, $userId, $content);
            
            if ($result['success']) {
                // Haal de nieuwe comment op om terug te sturen
                $comment = $this->getCommentById($result['comment_id']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Reactie toegevoegd!',
                    'comment' => $comment
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
            
        } catch (Exception $e) {
            error_log('Fout bij toevoegen comment: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Er ging iets mis bij het toevoegen van je reactie']);
        }
        
        exit;
    }

    /**
     * Sla een comment op in de database
     */
    private function saveComment($postId, $userId, $content)
    {
        try {
            // Begin een transactie
            $this->db->beginTransaction();
            
            // Voeg comment toe
            $stmt = $this->db->prepare("
                INSERT INTO post_comments (post_id, user_id, content, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$postId, $userId, $content]);
            $commentId = $this->db->lastInsertId();
            
            // Update de comments_count in de posts tabel
            $stmt = $this->db->prepare("
                UPDATE posts 
                SET comments_count = comments_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$postId]);
            
            // Commit de transactie
            $this->db->commit();
            
            return [
                'success' => true,
                'comment_id' => $commentId
            ];
            
        } catch (Exception $e) {
            // Rollback bij fouten
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log('Database fout bij opslaan comment: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database fout: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Haal een comment op uit de database met gebruikersgegevens
     */
    private function getCommentById($commentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.content,
                    c.created_at,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE c.id = ? AND c.is_deleted = 0
            ");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($comment) {
                // Voeg avatar toe
                $comment['avatar'] = $this->getUserAvatar($comment['user_id']);
                
                // Formatteer de datum
                $comment['time_ago'] = $this->formatDate($comment['created_at']);
            }
            
            return $comment;
            
        } catch (Exception $e) {
            error_log('Fout bij ophalen comment: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Voeg deze methode toe aan je FeedController.php
     * Deze haalt alle comments op voor posts
     */
    /**
     * UPDATE: Verbeterde getCommentsForPosts met like status
     */
    private function getCommentsForPosts($posts)
    {
        if (empty($posts)) {
            return $posts;
        }
        
        // Haal alle post IDs op
        $postIds = array_column($posts, 'id');
        $placeholders = str_repeat('?,', count($postIds) - 1) . '?';
        $currentUserId = $_SESSION['user_id'] ?? 0;
        
        try {
            // Haal alle comments op voor deze posts MET like informatie
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.post_id,
                    c.content,
                    c.created_at,
                    c.likes_count,
                    c.user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    CASE WHEN cl.user_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN comment_likes cl ON c.id = cl.comment_id AND cl.user_id = ?
                WHERE c.post_id IN ($placeholders) 
                AND c.is_deleted = 0
                ORDER BY c.created_at ASC
            ");
            
            // Voeg current user ID toe aan het begin van de parameters
            $params = array_merge([$currentUserId], $postIds);
            $stmt->execute($params);
            $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            
            // Groepeer comments per post
            $commentsByPost = [];
            foreach ($allComments as $comment) {
                // Voeg avatar en geformatteerde datum toe
                $comment['avatar'] = $this->getUserAvatar($comment['user_id']);
                $comment['time_ago'] = $this->formatDate($comment['created_at']);
                
                $commentsByPost[$comment['post_id']][] = $comment;
            }
            
            // Voeg comments toe aan elke post
            foreach ($posts as &$post) {
                $post['comments_list'] = $commentsByPost[$post['id']] ?? [];
            }
            
            return $posts;
            
        } catch (Exception $e) {
            error_log('Fout bij ophalen comments: ' . $e->getMessage());
            
            // Bij fout, voeg lege comments array toe
            foreach ($posts as &$post) {
                $post['comments_list'] = [];
            }
            
            return $posts;
        }
    }

    /**
     * Toggle like op een comment (like/unlike)
     */
    public function toggleCommentLike()
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
        
        $commentId = $_POST['comment_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Comment ID is verplicht']);
            exit;
        }
        
        try {
            // Controleer of comment bestaat
            $stmt = $this->db->prepare("SELECT id FROM post_comments WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment) {
                echo json_encode(['success' => false, 'message' => 'Reactie niet gevonden']);
                exit;
            }
            
            // Controleer of gebruiker deze comment al heeft geliked
            $stmt = $this->db->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
            $stmt->execute([$commentId, $userId]);
            $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingLike) {
                // Unlike: verwijder de like
                $this->removeCommentLike($commentId, $userId);
                $action = 'unliked';
            } else {
                // Like: voeg like toe
                $this->addCommentLike($commentId, $userId);
                $action = 'liked';
            }
            
            // Haal nieuwe like count op
            $newLikeCount = $this->getCommentLikeCount($commentId);


            echo json_encode([
                'success' => true,
                'action' => $action,
                'like_count' => $newLikeCount,
                'debug' => $debugData  // Debug data in response
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()]);
        }
        
        exit;
    }

    /**
     * Voeg een comment like toe
     */
    private function addCommentLike($commentId, $userId)
    {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Voeg like toe aan comment_likes tabel
            $stmt = $this->db->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
            $stmt->execute([$commentId, $userId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Verwijder een comment like
     */
    private function removeCommentLike($commentId, $userId)
    {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Verwijder like uit comment_likes tabel
            $stmt = $this->db->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
            $stmt->execute([$commentId, $userId]);
            
            // Update likes_count in post_comments tabel (maar niet onder 0)
            $stmt = $this->db->prepare("UPDATE post_comments SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
            $stmt->execute([$commentId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Haal het aantal likes op voor een comment
     */
    private function getCommentLikeCount($commentId)
    {
        $stmt = $this->db->prepare("SELECT likes_count FROM post_comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['likes_count'] : 0;
    }

    /**
     * Verwijder een comment
     */
    public function deleteComment()
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
        
        $commentId = $_POST['comment_id'] ?? null;
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'] ?? 'user';
        
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Comment ID is verplicht']);
            exit;
        }
        
        try {
            // Haal comment op met eigenaar info
            $stmt = $this->db->prepare("SELECT user_id, post_id FROM post_comments WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment) {
                echo json_encode(['success' => false, 'message' => 'Reactie niet gevonden']);
                exit;
            }
            
            // Controleer toestemming (eigenaar of admin)
            $isOwner = ($comment['user_id'] == $userId);
            $isAdmin = ($userRole === 'admin');
            
            if (!$isOwner && !$isAdmin) {
                echo json_encode(['success' => false, 'message' => 'Je hebt geen toestemming om deze reactie te verwijderen']);
                exit;
            }
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // Soft delete de comment
            $stmt = $this->db->prepare("UPDATE post_comments SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$commentId]);
            
            // Update comment count in post
            $stmt = $this->db->prepare("UPDATE posts SET comments_count = GREATEST(0, comments_count - 1) WHERE id = ?");
            $stmt->execute([$comment['post_id']]);
            
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Reactie succesvol verwijderd'
            ]);
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            echo json_encode(['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()]);
        }
        
        exit;
    }

}