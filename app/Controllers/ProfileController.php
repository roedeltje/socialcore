<?php

namespace App\Controllers;

use App\Database\Database;
use App\Auth\Auth;
use PDO;

class ProfileController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon de profielpagina
     */
    public function index($userIdFromRoute = null, $usernameFromRoute = null)
{
    // Controleer eerst of gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
        return;
    }
    
    // Bepaal welke tab actief is
    $activeTab = $_GET['tab'] ?? 'over';
    
    // Bepaal de gebruiker wiens profiel wordt bekeken
    $targetUserId = null;
    $targetUsername = null;
    
    // Prioriteit: Route parameters > GET parameters > eigen profiel
    if ($userIdFromRoute !== null) {
        // User ID uit route (bijv. /profile/1)
        $targetUserId = $userIdFromRoute;
    } elseif ($usernameFromRoute !== null) {
        // Username uit route (bijv. /profile/johndoe)
        $targetUsername = $usernameFromRoute;
    } elseif (isset($_GET['username'])) {
        // Username uit query parameter (bijv. ?username=johndoe) - backward compatibility
        $targetUsername = $_GET['username'];
    } else {
        // Geen specifieke gebruiker, toon eigen profiel
        $targetUserId = $_SESSION['user_id'];
    }
    
    // Haal gebruikersgegevens op
    $user = $this->getTargetUser($targetUserId, $targetUsername);
    
    if (!$user) {
        // Gebruiker niet gevonden, redirect naar eigen profiel
        $_SESSION['error_message'] = 'Gebruiker niet gevonden.';
        redirect('profile');
        return;
    }
    
    // Bepaal of de kijker de eigenaar is
    $viewerIsOwner = $_SESSION['user_id'] == $user['id'];
    
    // Laad vrienden
    $friends = $this->getFriends($user['id']);
    
    // Laad posts
    $posts = $this->getUserPosts($user['id']);

    // Laadt comments
    $posts = $this->getCommentsForPosts($posts);
    
    // Laad specifieke data op basis van de geselecteerde tab
    $krabbels = [];
    $fotos = [];
    
    if ($activeTab === 'krabbels') {
        $krabbels = $this->getKrabbels($user['id']);
    } elseif ($activeTab === 'fotos') {
        $fotos = $this->getFotos($user['id']);
    }
    
    $data = [
        'title' => $user['name'] . ' - Profiel',
        'user' => $user,
        'friends' => $friends,
        'posts' => $posts,
        'krabbels' => $krabbels,
        'fotos' => $fotos,
        'viewer_is_owner' => $viewerIsOwner,
        'active_tab' => $activeTab
    ];
    
    $this->view('profile/index', $data);
}
    
    /**
     * Haal de doelgebruiker op (door ID of username)
     */
    private function getTargetUser($userId = null, $username = null)
    {
        try {
            if ($userId !== null) {
                // Zoek op user ID
                $stmt = $this->db->prepare("SELECT id, username FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $userBasic = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userBasic) {
                    return $this->getUserData($userBasic['id'], $userBasic['username']);
                }
            } elseif ($username !== null) {
                // Zoek op username
                $stmt = $this->db->prepare("SELECT id, username FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $userBasic = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userBasic) {
                    return $this->getUserData($userBasic['id'], $userBasic['username']);
                }
            } else {
                // Geen specifieke gebruiker, gebruik ingelogde gebruiker
                $userId = $_SESSION['user_id'];
                $username = $_SESSION['username'] ?? 'gebruiker';
                return $this->getUserData($userId, $username);
            }
        } catch (\Exception $e) {
            error_log("Error fetching target user: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Haal gebruikersgegevens op
     */
    private function getUserData($userId, $username)
    {
        try {
            // Haal gebruikersgegevens op uit de database
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.username, 
                    u.email,
                    u.created_at,
                    u.role,
                    COALESCE(up.display_name, u.username) as name,
                    up.display_name,
                    up.avatar,
                    up.cover_photo,
                    up.bio,
                    up.location,
                    up.website,
                    up.date_of_birth,
                    up.gender,
                    up.phone,
                    up.created_at as profile_created_at,
                    up.updated_at as profile_updated_at
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dbUser) {
                // Formateer de avatar path met de nieuwe helper
                $avatarPath = $this->getAvatarUrl($dbUser['avatar']);
                    
                // Formateer de join datum
                $joinDate = !empty($dbUser['created_at']) 
                    ? date('d M Y', strtotime($dbUser['created_at'])) 
                    : date('d M Y');
                
                // Return alle beschikbare velden
                $returnData = [
                    'id' => $dbUser['id'],
                    'username' => $dbUser['username'],
                    'email' => $dbUser['email'],
                    'name' => $dbUser['name'] ?: $dbUser['username'],
                    'display_name' => $dbUser['display_name'] ?: $dbUser['username'],
                    'bio' => $dbUser['bio'] ?: '',
                    'location' => $dbUser['location'] ?: '',
                    'website' => $dbUser['website'] ?: '',
                    'date_of_birth' => $dbUser['date_of_birth'] ?: '',
                    'gender' => $dbUser['gender'] ?: '',
                    'phone' => $dbUser['phone'] ?: '',
                    'avatar' => $dbUser['avatar'] ?: 'theme-assets/default/images/default-avatar.png',
                    'avatar_url' => $avatarPath, // Volledige URL voor weergave
                    'cover_photo' => $dbUser['cover_photo'] ?: '',
                    'joined' => $joinDate,
                    'role' => $dbUser['role'] ?: 'member',
                    // Dummy data voor nu - later uit database halen
                    'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
                    'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
                ];
                
                return $returnData;
            }
        } catch (\Exception $e) {
            error_log("Error getting user data: " . $e->getMessage());
        }
        
        // Fallback: minimale gebruikersgegevens
        return [
            'id' => $userId,
            'username' => $username,
            'email' => '',
            'name' => $username,
            'display_name' => $username,
            'bio' => '',
            'location' => '',
            'website' => '',
            'date_of_birth' => '',
            'gender' => '',
            'phone' => '',
            'avatar' => 'theme-assets/default/images/default-avatar.png',
            'avatar_url' => base_url('theme-assets/default/images/default-avatar.png'),
            'cover_photo' => '',
            'joined' => date('d M Y'),
            'role' => 'member',
            'interests' => [],
            'favorite_quote' => ''
        ];
    }

    /**
     * Haal vrienden op voor een gebruiker
     */
    private function getFriends($userId)
    {
        // Later vervangen door echte database query
        // In de tussentijd gebruiken we deze dummy data
        return [
            ['id' => 3, 'name' => 'Daan de Vries', 'username' => 'daanvr', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 4, 'name' => 'Sophie Bakker', 'username' => 'sophieb', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 5, 'name' => 'Tim Jansen', 'username' => 'timj', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 6, 'name' => 'Emma Visser', 'username' => 'emmav', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 7, 'name' => 'Luuk Smit', 'username' => 'luuks', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 8, 'name' => 'Isa van Dijk', 'username' => 'isad', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ];
    }

    /**
     * Haal krabbels op voor een gebruiker
     */
    private function getKrabbels($userId)
    {
        // Later vervangen door echte database query
        // In de tussentijd gebruiken we deze dummy data
        return [
            [
                'id' => 1,
                'sender_id' => 3,
                'sender_username' => 'daanvr',
                'sender_name' => 'Daan de Vries',
                'sender_avatar' => 'avatars/2025/05/default-avatar.png',
                'message' => 'Hey! Leuk profiel heb je. Zullen we eens afspreken voor koffie?',
                'created_at' => '2025-05-17 14:23:45'
            ],
            [
                'id' => 2,
                'sender_id' => 4,
                'sender_username' => 'sophieb',
                'sender_name' => 'Sophie Bakker',
                'sender_avatar' => 'avatars/2025/05/default-avatar.png',
                'message' => 'Bedankt voor het accepteren van mijn vriendschapsverzoek! Ik ben benieuwd naar je posts.',
                'created_at' => '2025-05-16 10:12:30'
            ],
            [
                'id' => 3,
                'sender_id' => 5,
                'sender_username' => 'timj',
                'sender_name' => 'Tim Jansen',
                'sender_avatar' => 'avatars/2025/05/default-avatar.png',
                'message' => 'Het was super gezellig gisteren! Moeten we vaker doen. Groetjes, Tim',
                'created_at' => '2025-05-15 22:08:17'
            ]
        ];
    }

    /**
     * Haal foto's op voor een gebruiker
     */
    private function getFotos($userId)
    {
        // Later vervangen door echte database query
        // In de tussentijd gebruiken we deze dummy data
        return [
            [
                'id' => 1,
                'filename' => 'default-avatar.png',
                'description' => 'Mijn profielfoto',
                'uploaded_at' => '2025-05-16 09:30:00'
            ],
            [
                'id' => 2,
                'filename' => 'default-avatar.png',
                'description' => 'Een leuke dag in het park',
                'uploaded_at' => '2025-05-15 15:45:22'
            ],
            [
                'id' => 3,
                'filename' => 'default-avatar.png',
                'description' => 'Vakantiekiekje van vorige zomer',
                'uploaded_at' => '2025-05-14 11:20:18'
            ]
        ];
    }

    /**
     * Haal posts op van een specifieke gebruiker
     */
    private function getUserPosts($userId, $limit = 10)
    {
        try {
            $query = "
                SELECT 
                    p.id,
                    p.content,
                    p.type,
                    p.created_at,
                    p.likes_count AS likes,
                    p.comments_count AS comments,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE p.user_id = ? AND p.is_deleted = 0
                ORDER BY p.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $limit]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format de data voor de view
            foreach ($posts as &$post) {
                $post['created_at'] = $this->formatDate($post['created_at']);
                
                // Controleer of gebruiker de post heeft geliked
                $post['is_liked'] = $this->hasUserLikedPost($post['id']);

                $post['avatar'] = $this->getUserAvatar($post['user_id']);
            }
            
            return $posts;
            
        } catch (\Exception $e) {
            // Bij een fout, return lege array
            error_log("Error getting user posts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Controleer of een gebruiker een post heeft geliked
     */
    private function hasUserLikedPost($postId)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking if user liked post: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format datetime voor weergave
     */
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

    /**
     * Toon de pagina voor profielbewerking
     */
    public function edit()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $form = new \App\Helpers\FormHelper();
        
        // Haal gebruikersgegevens op
        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? '';
        $user = $this->getUserData($userId, $username);
        
        $data = [
            'title' => 'Profiel bewerken',
            'user' => $user,
            'form' => $form
        ];
        
        $this->view('profile/edit', $data);
    }

    /**
     * Update profielgegevens
     */
    public function update()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $form = new \App\Helpers\FormHelper();
        
        // Validatie
        $errors = [];
        
        // Display name is verplicht
        if (empty($_POST['display_name'])) {
            $errors['display_name'] = 'Weergavenaam is verplicht';
        }
        
        // Website URL validatie (als ingevuld)
        if (!empty($_POST['website']) && !filter_var($_POST['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Voer een geldige website URL in';
        }
        
        // Telefoonnummer validatie (basis)
        if (!empty($_POST['phone']) && !preg_match('/^[\+\-\s\(\)\d]+$/', $_POST['phone'])) {
            $errors['phone'] = 'Voer een geldig telefoonnummer in';
        }
        
        // Geboortedatum validatie
        if (!empty($_POST['date_of_birth'])) {
        $birthDate = \DateTime::createFromFormat('Y-m-d', $_POST['date_of_birth']);
        if (!$birthDate || $birthDate > new \DateTime()) {
            $errors['date_of_birth'] = 'Voer een geldige geboortedatum in';
            }
        }
        
        // Als er fouten zijn, ga terug naar het formulier
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = 'Er zijn fouten in het formulier. Controleer je invoer.';
            redirect('profile/edit');
            return;
        }
        
        try {
            // Start een database transactie
            $this->db->beginTransaction();
            
            // Controleer of er al een profiel bestaat
            $stmt = $this->db->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profileExists = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profileExists) {
                // Update bestaand profiel
                $stmt = $this->db->prepare("
                    UPDATE user_profiles SET 
                        display_name = ?,
                        bio = ?,
                        location = ?,
                        website = ?,
                        phone = ?,
                        date_of_birth = ?,
                        gender = ?,
                        updated_at = NOW()
                    WHERE user_id = ?
                ");
                
                $stmt->execute([
                    $_POST['display_name'],
                    $_POST['bio'] ?? '',
                    $_POST['location'] ?? '',
                    $_POST['website'] ?? '',
                    $_POST['phone'] ?? '',
                    $_POST['date_of_birth'] ?? null,
                    $_POST['gender'] ?? '',
                    $userId
                ]);
            } else {
                // Maak nieuw profiel aan
                $stmt = $this->db->prepare("
                    INSERT INTO user_profiles (
                        user_id, display_name, bio, location, website, 
                        phone, date_of_birth, gender, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $userId,
                    $_POST['display_name'],
                    $_POST['bio'] ?? '',
                    $_POST['location'] ?? '',
                    $_POST['website'] ?? '',
                    $_POST['phone'] ?? '',
                    $_POST['date_of_birth'] ?? null,
                    $_POST['gender'] ?? ''
                ]);
            }
            
            // Commit de transactie
            $this->db->commit();
            
            // Update ook de sessie met de nieuwe display name
            $_SESSION['display_name'] = $_POST['display_name'];
            
            // Succes bericht
            $_SESSION['success_message'] = 'Je profiel is succesvol bijgewerkt!';
            redirect('profile');
            
        } catch (\Exception $e) {
            // Rollback bij fout
            $this->db->rollback();
            error_log("Error updating profile: " . $e->getMessage());
            
            $_SESSION['error_message'] = 'Er is een fout opgetreden bij het opslaan. Probeer het opnieuw.';
            redirect('profile/edit');
        }
    }

    /**
     * Update de profielfoto
     */
    public function updateAvatar() 
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            // Foutafhandeling
            set_flash_message('error', 'Er is geen geldige avatar geüpload');
            redirect('profile/edit');
            return;
        }
        
        // Toegestane bestandstypen voor avatars
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        
        // Maximale bestandsgrootte (2MB)
        $maxSize = 2 * 1024 * 1024;
        
        // Upload de avatar
        $uploadResult = upload_file(
            $_FILES['avatar'],
            'avatars',
            $allowedTypes,
            $maxSize,
            'avatar_' . $_SESSION['user_id'] . '_'
        );
        
        if ($uploadResult['success']) {
            // Update de gebruiker in de database met het nieuwe avatar pad
            $userId = $_SESSION['user_id'];
            $avatarPath = $uploadResult['path'];
            
            // Hier je database update code
            // $userModel->updateAvatar($userId, $avatarPath);
            
            // Update de sessie
            $_SESSION['avatar'] = $avatarPath;
            
            set_flash_message('success', 'Profielfoto succesvol bijgewerkt');
        } else {
            set_flash_message('error', $uploadResult['message']);
        }
        
        redirect('profile/edit');
    }
    
    /**
     * Verwerk een nieuwe krabbel
     */
    public function postKrabbel()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $senderId = $_SESSION['user_id'];
        $receiverId = $_POST['receiver_id'] ?? null;
        $receiverUsername = $_POST['receiver_username'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (!$receiverId || trim($message) === '') {
            set_flash_message('error', 'Alle velden zijn verplicht.');
            redirect('profile/' . $receiverUsername . '?tab=krabbels');
            return;
        }
        
        // Hier later de echte database implementatie
        // Voor nu simuleren we een succesvolle operatie
        $success = true;
        
        if ($success) {
            set_flash_message('success', 'Krabbel is geplaatst!');
        } else {
            set_flash_message('error', 'Er is iets misgegaan bij het plaatsen van de krabbel.');
        }
        
        // Redirect terug naar het profiel met de krabbels-tab
        redirect('?route=profile&username=' . urlencode($receiverUsername) . '&tab=krabbels');
    }
    
    /**
     * Verwerk een nieuwe foto-upload
     */
    public function uploadFoto()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $description = $_POST['description'] ?? '';
        
        // Controleer of er een bestand is geüpload
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            set_flash_message('error', 'Er is een fout opgetreden bij het uploaden van de foto.');
            redirect('profile?tab=fotos');
            return;
        }
        
        // Hier komen later de echte upload-logica en database-opslag
        // Voor nu simuleren we een succesvolle operatie
        $success = true;
        
        if ($success) {
            set_flash_message('success', 'Foto is succesvol geüpload!');
        } else {
            set_flash_message('error', 'Er is een fout opgetreden bij het uploaden van de foto.');
        }
        
        // Redirect terug naar het profiel met de foto's-tab
        redirect('profile?tab=fotos');
    }

    private function getCommentsForPosts($posts)
    {
        if (empty($posts)) {
            return $posts;
        }
        
        // Haal alle post IDs op
        $postIds = array_column($posts, 'id');
        $placeholders = str_repeat('?,', count($postIds) - 1) . '?';
        
        try {
            // Haal alle comments op voor deze posts
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.post_id,
                    c.content,
                    c.created_at,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE c.post_id IN ($placeholders) 
                AND c.is_deleted = 0
                ORDER BY c.created_at ASC
            ");
            $stmt->execute($postIds);
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
     * Helper om de avatar van een gebruiker op te halen
     */
    private function getUserAvatar($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT avatar FROM user_profiles 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['avatar'])) {
                return $this->getAvatarUrl($result['avatar']);
            }
        } catch (\Exception $e) {
            error_log('Fout bij ophalen avatar: ' . $e->getMessage());
        }
        
        // Fallback naar default avatar
        return base_url('theme-assets/default/images/default-avatar.png');
    }

    /**
     * Update de profielfoto via AJAX of form submission
     */
    public function uploadAvatar() 
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
                return;
            }
            redirect('login');
            return;
        }
        
        // Controleer of er een bestand is geüpload
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $message = 'Er is geen geldige avatar geüpload';
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $_SESSION['error_message'] = $message;
            redirect('profile/edit');
            return;
        }
        
        // Toegestane bestandstypen voor avatars
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        // Maximale bestandsgrootte (2MB)
        $maxSize = 2 * 1024 * 1024;
        
        // Upload de avatar met een prefix voor herkenning
        $uploadResult = upload_file(
            $_FILES['avatar'],
            'avatars',
            $allowedTypes,
            $maxSize,
            'avatar_' . $_SESSION['user_id'] . '_'
        );
        
        if ($uploadResult['success']) {
            try {
                // Update de database met het nieuwe avatar pad
                $userId = $_SESSION['user_id'];
                $avatarPath = $uploadResult['path'];
                
                // Start database transactie
                $this->db->beginTransaction();
                
                // Controleer of er al een profiel bestaat
                $stmt = $this->db->prepare("SELECT id, avatar FROM user_profiles WHERE user_id = ?");
                $stmt->execute([$userId]);
                $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existingProfile) {
                    // Verwijder oude avatar als deze anders is dan de default
                    if (!empty($existingProfile['avatar']) && 
                        !str_contains($existingProfile['avatar'], 'default-avatar') &&
                        !str_contains($existingProfile['avatar'], 'theme-assets')) {
                        delete_uploaded_file($existingProfile['avatar']);
                    }
                    
                    // Update bestaand profiel
                    $stmt = $this->db->prepare("
                        UPDATE user_profiles 
                        SET avatar = ?, updated_at = NOW()
                        WHERE user_id = ?
                    ");
                    $stmt->execute([$avatarPath, $userId]);
                } else {
                    // Maak nieuw profiel aan
                    $stmt = $this->db->prepare("
                        INSERT INTO user_profiles (user_id, avatar, created_at, updated_at) 
                        VALUES (?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([$userId, $avatarPath]);
                }
                
                // Commit transactie
                $this->db->commit();
                
                // Update sessie
                $_SESSION['avatar'] = $avatarPath;
                
                $message = 'Profielfoto succesvol bijgewerkt!';
                $avatarUrl = base_url('uploads/' . $avatarPath);
                
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => $message,
                        'avatar_url' => $avatarUrl,
                        'avatar_path' => $avatarPath
                    ]);
                    return;
                }
                
                $_SESSION['success_message'] = $message;
                
            } catch (\Exception $e) {
                // Rollback bij database fout
                $this->db->rollback();
                
                // Verwijder het geüploade bestand als database update mislukt
                delete_uploaded_file($uploadResult['path']);
                
                error_log("Avatar upload database error: " . $e->getMessage());
                $message = 'Er ging iets mis bij het opslaan van je profielfoto. Probeer het opnieuw.';
                
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $message]);
                    return;
                }
                
                $_SESSION['error_message'] = $message;
            }
        } else {
            $message = $uploadResult['message'];
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $_SESSION['error_message'] = $message;
        }
        
        redirect('profile/edit');
    }

    /**
     * Verwijder huidige avatar en zet terug naar default
     */
    public function removeAvatar()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
                return;
            }
            redirect('login');
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            // Haal huidige avatar op
            $stmt = $this->db->prepare("SELECT avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profile && !empty($profile['avatar'])) {
                // Verwijder alleen als het geen default avatar is
                if (!str_contains($profile['avatar'], 'default-avatar') &&
                    !str_contains($profile['avatar'], 'theme-assets')) {
                    delete_uploaded_file($profile['avatar']);
                }
                
                // Update database naar default avatar
                $defaultAvatar = 'theme-assets/default/images/default-avatar.png';
                $stmt = $this->db->prepare("
                    UPDATE user_profiles 
                    SET avatar = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->execute([$defaultAvatar, $userId]);
                
                // Update sessie
                $_SESSION['avatar'] = $defaultAvatar;
            }
            
            $message = 'Profielfoto verwijderd en teruggezet naar standaard';
            $defaultAvatar = 'theme-assets/default/images/default-avatar.png';
            $avatarUrl = base_url($defaultAvatar);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'avatar_url' => $avatarUrl
                ]);
                return;
            }
            
            $_SESSION['success_message'] = $message;
            
        } catch (\Exception $e) {
            error_log("Remove avatar error: " . $e->getMessage());
            $message = 'Er ging iets mis bij het verwijderen van je profielfoto';
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $_SESSION['error_message'] = $message;
        }
        
        redirect('profile/edit');
    }

    /**
     * Helper functie om de volledige avatar URL te krijgen
     */
    private function getAvatarUrl($avatarPath)
    {
        if (empty($avatarPath)) {
            return base_url('theme-assets/default/images/default-avatar.png');
        }
        
        // Als het pad al een volledige URL is
        if (str_starts_with($avatarPath, 'http')) {
            return $avatarPath;
        }
        
        // Als het een theme asset is
        if (str_starts_with($avatarPath, 'theme-assets')) {
            return base_url($avatarPath);
        }
        
        // Voor uploads: gebruik base_url zonder extra 'public'
        // Want de upload path bevat al de juiste structuur
        return base_url('uploads/' . $avatarPath);
    }


}