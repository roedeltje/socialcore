<?php

namespace App\Controllers;

use App\Database\Database;
use App\Auth\Auth;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;

class ProfileController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * 🔒 BIJGEWERKT: Toon de profielpagina MET privacy checks
     */
    public function index($userIdFromRoute = null, $usernameFromRoute = null)
    {
        // Controleer eerst of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        $viewerUserId = $_SESSION['user_id'];
        
        // Bepaal welke tab actief is
        $activeTab = $_GET['tab'] ?? 'over';
        
        // Bepaal de gebruiker wiens profiel wordt bekeken
        $targetUserId = null;
        $targetUsername = null;
        
        // Prioriteit: Route parameters > GET parameters > eigen profiel
        if ($userIdFromRoute !== null) {
            $targetUserId = $userIdFromRoute;
        } elseif ($usernameFromRoute !== null) {
            $targetUsername = $usernameFromRoute;
        } elseif (isset($_GET['username'])) {
            $targetUsername = $_GET['username'];
        } else {
            // Geen specifieke gebruiker, toon eigen profiel
            $targetUserId = $_SESSION['user_id'];
        }
        
        // Haal gebruikersgegevens op
        $user = $this->getTargetUser($targetUserId, $targetUsername);
        
        if (!$user) {
            $_SESSION['error_message'] = 'Gebruiker niet gevonden.';
            redirect('profile');
            return;
        }
        
        // 🔒 PRIVACY CHECK: Mag viewer dit profiel bekijken?
        if (!$this->canViewProfile($user['id'], $viewerUserId)) {
            $_SESSION['error_message'] = 'Je hebt geen toestemming om dit profiel te bekijken.';
            redirect('profile');
            return;
        }
        
        // Bepaal of de kijker de eigenaar is
        $viewerIsOwner = $viewerUserId == $user['id'];

        // 🔒 PRIVACY: Filter contactgegevens
        $user = $this->filterContactInfo($user, $viewerUserId);

        // Haal vriendschapsstatus op als het niet je eigen profiel is
        $friendshipStatus = null;
        $friendshipData = null;

        if (!$viewerIsOwner) {
            $friendsController = new \App\Controllers\FriendsController();
            $friendshipData = $friendsController->getFriendshipStatus($viewerUserId, $user['id']);
            
            if ($friendshipData) {
                $friendshipStatus = $friendshipData['status'];
                $friendshipDirection = ($friendshipData['user_id'] == $viewerUserId) ? 'sent' : 'received';
            } else {
                $friendshipStatus = 'none';
                $friendshipDirection = null;
            }
        }
        
        // 🔒 PRIVACY: Check wat viewer mag zien en doen
        $canViewPhotos = $this->canViewPhotos($user['id'], $viewerUserId);
        $canSendMessage = $this->canSendMessage($user['id'], $viewerUserId);
        
        // Laad vrienden (altijd - privacy wordt niet toegepast op vriendenlijst voor nu)
        $friends = $this->getFriends($user['id']);
        
        // Laad posts (altijd - privacy wordt later toegepast op posts zelf)
        $posts = $this->getUserPosts($user['id']);
        $posts = $this->getCommentsForPosts($posts);
        
        // 🔒 PRIVACY: Laad foto's alleen als toegestaan
        $fotos = [];
        if ($activeTab === 'fotos' && $canViewPhotos) {
            $fotos = $this->getFotos($user['id']);
        } elseif ($activeTab === 'fotos' && !$canViewPhotos) {
            // Zet een bericht dat foto's niet zichtbaar zijn
            $_SESSION['info_message'] = 'Foto\'s zijn alleen zichtbaar voor vrienden.';
        }
        
        $data = [
            'title' => $user['name'] . ' - Profiel',
            'user' => $user,
            'friends' => $friends,
            'posts' => $posts,
            'krabbels' => [],
            'fotos' => $fotos,
            'viewer_is_owner' => $viewerIsOwner,
            'active_tab' => $activeTab,
            'friendship_status' => $friendshipStatus,
            'friendship_direction' => $friendshipDirection ?? null,
            'friendship_data' => $friendshipData,
            // 🔒 NIEUWE PRIVACY DATA
            'can_view_photos' => $canViewPhotos,
            'can_send_message' => $canSendMessage,
            'privacy_blocked_photos' => !$canViewPhotos && $activeTab === 'fotos'
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
                // ✅ Gebruik de verbeterde avatar URL functie
                $avatarPath = get_avatar_url($dbUser['avatar']);
                    
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
                    'avatar' => $dbUser['avatar'] ?: '',  // Behoud originele pad voor database operaties
                    'avatar_url' => $avatarPath,         // Volledige URL voor weergave
                    'cover_photo' => $dbUser['cover_photo'] ?: '',
                    'created_at' => $dbUser['created_at'],
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
            'avatar' => '',
            'avatar_url' => base_url('theme-assets/default/images/default-avatar.png'),
            'cover_photo' => '',
            'created_at' => date('Y-m-d H:i:s'),
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
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar,
                    f.created_at as friend_since
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
                ORDER BY up.display_name ASC, u.username ASC
            ");
            
            $stmt->execute([$userId, $userId, $userId]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Voeg avatar URLs toe
            foreach ($friends as &$friend) {
                $friend['avatar_url'] = $this->getAvatarUrl($friend['avatar']);
                $friend['friend_since_formatted'] = $this->formatDate($friend['friend_since']);
            }
            
            return $friends;
            
        } catch (Exception $e) {
            error_log("Error getting friends: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal foto's op voor een gebruiker
     */
    private function getFotos($userId)
    {
        try {
            // Haal alle foto's op die deze gebruiker heeft geüpload via posts
            $stmt = $this->db->prepare("
                SELECT 
                    pm.id,
                    pm.file_path,
                    pm.file_name,
                    pm.created_at,
                    p.content as description,
                    p.id as post_id,
                    p.created_at as uploaded_at
                FROM post_media pm
                JOIN posts p ON pm.post_id = p.id
                WHERE p.user_id = ? 
                AND pm.media_type = 'image'
                AND p.is_deleted = 0
                ORDER BY pm.created_at DESC
            ");
            
            $stmt->execute([$userId]);
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format de foto's voor weergave
            $formattedPhotos = [];
            foreach ($photos as $photo) {
                $formattedPhotos[] = [
                    'id' => $photo['id'],
                    'filename' => $photo['file_path'], // Volledige path voor weergave
                    'original_filename' => $photo['file_name'],
                    'description' => $photo['description'] ?: 'Geen beschrijving',
                    'uploaded_at' => $photo['uploaded_at'],
                    'post_id' => $photo['post_id'],
                    'full_url' => base_url('uploads/' . $photo['file_path'])
                ];
            }
            
            return $formattedPhotos;
            
        } catch (\Exception $e) {
            error_log("Error getting user photos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal posts op van een specifieke gebruiker
     */
    private function getUserPosts($userId, $limit = 20)
    {
        try {
            $query = "
                SELECT 
                    p.id,
                    p.content,
                    p.type,
                    p.post_type,
                    p.target_user_id,
                    p.created_at,
                    p.likes_count AS likes,
                    p.comments_count AS comments,
                    p.link_preview_id,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    target_user.username as target_username,
                    COALESCE(target_profile.display_name, target_user.username) as target_name,
                    (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path,
                    -- ✨ TOEGEVOEGD: Link preview data (net als FeedController)
                    lp.url as preview_url,
                    lp.title as preview_title,
                    lp.description as preview_description,
                    lp.image_url as preview_image,
                    lp.domain as preview_domain
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN users target_user ON p.target_user_id = target_user.id
                LEFT JOIN user_profiles target_profile ON target_user.id = target_profile.user_id
                LEFT JOIN link_previews lp ON p.link_preview_id = lp.id
                WHERE (p.user_id = ? OR p.target_user_id = ?) 
                AND p.is_deleted = 0
                ORDER BY p.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $userId, $limit]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format de data voor de view
            foreach ($posts as &$post) {
                $post['created_at'] = $this->formatDate($post['created_at']);
                $post['is_liked'] = $this->hasUserLikedPost($post['id']);
                $post['avatar'] = $this->getUserAvatar($post['user_id']);
                
                // Bepaal of dit een krabbel is
                $post['is_wall_message'] = ($post['post_type'] === 'wall_message');
                
                // Voor wall messages: maak sender -> receiver string
                if ($post['is_wall_message'] && !empty($post['target_name'])) {
                    $post['wall_message_header'] = $post['user_name'] . ' → ' . $post['target_name'];
                }
                
                // NIEUW: Process content voor klikbare hashtags
                $post['content_formatted'] = $this->processProfilePostContent($post['content']);
            }
            
            return $posts;
            
        } catch (\Exception $e) {
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
    
    // NIEUWE CODE: Zorg ervoor dat we altijd het eigen profiel bewerken
    // Haal direct de eigen gebruikersgegevens op
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? '';
    
    try {
        $user = $this->getUserData($userId, $username);
        
        if (!$user) {
            $_SESSION['error_message'] = 'Kon je profielgegevens niet ophalen.';
            redirect('profile');
            return;
        }
    } catch (\Exception $e) {
        error_log("Error in profile edit: " . $e->getMessage());
        $_SESSION['error_message'] = 'Er ging iets mis bij het laden van je profiel.';
        redirect('profile');
        return;
    }
    
    $form = new \App\Helpers\FormHelper();
    
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
        
        // 🔒 SECURITY: Check profile update rate limiting
        if (!$this->checkProfileUpdateRateLimit($userId)) {
            $_SESSION['error_message'] = 'Je kunt je profiel maar 5 keer per uur bijwerken. Probeer het later opnieuw.';
            redirect('profile/edit');
            return;
        }
        
        $form = new \App\Helpers\FormHelper();
        
        // 🔒 SECURITY: Sanitize en valideer alle input
        $sanitizedData = $this->sanitizeProfileInput($_POST);
        $errors = $this->validateProfileData($sanitizedData);
        
        // Als er fouten zijn, ga terug naar het formulier
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $sanitizedData; // Gebruik gesanitizeerde data
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
                    $sanitizedData['display_name'],
                    $sanitizedData['bio'],
                    $sanitizedData['location'],
                    $sanitizedData['website'],
                    $sanitizedData['phone'],
                    $sanitizedData['date_of_birth'],
                    $sanitizedData['gender'],
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
                    $sanitizedData['display_name'],
                    $sanitizedData['bio'],
                    $sanitizedData['location'],
                    $sanitizedData['website'],
                    $sanitizedData['phone'],
                    $sanitizedData['date_of_birth'],
                    $sanitizedData['gender']
                ]);
            }
            
            // 🔒 SECURITY: Log profile update activiteit
            $this->logProfileUpdate($userId);
            
            // Commit de transactie
            $this->db->commit();
            
            // Update ook de sessie met de nieuwe display name
            $_SESSION['display_name'] = $sanitizedData['display_name'];
            
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
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Je moet ingelogd zijn om een krabbel te plaatsen.';
            redirect('login');
            return;
        }

        // Controleer POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('home');
            return;
        }

        $senderId = $_SESSION['user_id'];
        $receiverId = $_POST['receiver_id'] ?? null;
        $receiverUsername = $_POST['receiver_username'] ?? null;
        $message = trim($_POST['message'] ?? '');

        // Validatie
        if (empty($receiverId) || empty($message)) {
            $_SESSION['error'] = 'Alle velden zijn verplicht.';
            redirect('?route=profile&username=' . $receiverUsername);
            return;
        }

        if (strlen($message) > 500) {
            $_SESSION['error'] = 'Krabbel mag maximaal 500 karakters bevatten.';
            redirect('?route=profile&username=' . $receiverUsername);
            return;
        }

        try {
            // NIEUWE CODE: Sla krabbel op in posts tabel
            $stmt = $this->db->prepare("
                INSERT INTO posts (user_id, content, post_type, target_user_id, created_at)
                VALUES (?, ?, 'wall_message', ?, NOW())
            ");
            
            $stmt->execute([$senderId, $message, $receiverId]);
            
            $_SESSION['success'] = 'Krabbel succesvol geplaatst!';
            
        } catch (Exception $e) {
            error_log("Error posting krabbel: " . $e->getMessage());
            $_SESSION['error'] = 'Er ging iets mis bij het plaatsen van de krabbel.';
        }

        // Redirect terug naar profiel
        redirect('?route=profile&username=' . $receiverUsername . '&tab=krabbels');
    }
    
    /**
     * Verwerk een nieuwe foto-upload
     */
    public function uploadFoto()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Je moet ingelogd zijn om foto\'s te uploaden.';
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $description = $_POST['description'] ?? '';
        
        // Controleer of er een bestand is geüpload
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_message'] = 'Er is een fout opgetreden bij het uploaden van de foto.';
            redirect('profile?tab=fotos');
            return;
        }
        
        try {
            // Gebruik de bestaande upload_file functie met correcte parameter volgorde:
            // upload_file($file, $type, $allowedTypes, $maxSize, $prefix)
            $allowedTypes = [
                'image/jpeg',
                'image/png', 
                'image/gif',
                'image/webp'
            ];
            
            $maxSize = 5 * 1024 * 1024; // 5MB voor foto's
            
            $uploadResult = upload_file(
                $_FILES['photo'],    // $file
                'posts',            // $type - Upload naar posts directory
                $allowedTypes,      // $allowedTypes
                $maxSize,           // $maxSize
                'post_'             // $prefix voor bestandsnaam
            );
            
            if ($uploadResult['success']) {
                // Start database transactie
                $this->db->beginTransaction();
                
                // Maak eerst een post aan voor de foto
                $stmt = $this->db->prepare("
                    INSERT INTO posts (user_id, content, type, privacy, created_at) 
                    VALUES (?, ?, 'photo', 'public', NOW())
                ");
                $stmt->execute([$userId, $description]);
                $postId = $this->db->lastInsertId();
                
                // Voeg de foto toe aan post_media
                $stmt = $this->db->prepare("
                    INSERT INTO post_media (
                        post_id, media_type, file_path, file_name, 
                        file_size, alt_text, created_at
                    ) VALUES (?, 'image', ?, ?, ?, ?, NOW())
                ");
                
                $stmt->execute([
                    $postId,
                    $uploadResult['path'],      // Dit is het relatieve pad
                    $_FILES['photo']['name'],
                    $_FILES['photo']['size'],
                    $description
                ]);
                
                // Commit transactie
                $this->db->commit();
                
                $_SESSION['success_message'] = 'Foto is succesvol geüpload!';
                
            } else {
                $_SESSION['error_message'] = $uploadResult['message'];
            }
            
        } catch (\Exception $e) {
            // Rollback bij fout
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            // Verwijder geüploade bestand als database update mislukt
            if (isset($uploadResult['path'])) {
                delete_uploaded_file($uploadResult['path']);
            }
            
            error_log("Photo upload error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Er is een fout opgetreden bij het opslaan van de foto. Probeer het opnieuw.';
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
                // ✅ FIXED: Gebruik globale get_avatar_url() functie
                return get_avatar_url($result['avatar']);
            }
        } catch (\Exception $e) {
            error_log('Fout bij ophalen avatar: ' . $e->getMessage());
        }
        
        // Fallback naar default avatar
        return get_avatar_url(null);
    }

    
    /**
     * Update de profielfoto via AJAX of form submission
     * 🔒 SECURITY: Met rate limiting en logging
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
        
        $userId = $_SESSION['user_id'];
        
        // 🔒 SECURITY: Check upload rate limiting (NU WEL ACTIEF!)
        if (!$this->checkAvatarUploadRateLimit($userId)) {
            $message = 'Je kunt maar 1 profielfoto per uur uploaden. Probeer het later opnieuw.';
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $_SESSION['error_message'] = $message;
            redirect('profile/edit');
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
        
        // 🔒 SECURITY: Gebruik configureerbare upload instellingen
        if (class_exists('App\Helpers\SecuritySettings')) {
            try {
                $allowedTypes = SecuritySettings::getAllowedImageFormats();
                $maxSize = SecuritySettings::get('max_avatar_size', 2 * 1024 * 1024);
            } catch (\Exception $e) {
                // Fallback bij SecuritySettings fout
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxSize = 2 * 1024 * 1024;
            }
        } else {
            // Fallback zonder SecuritySettings
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024;
        }
        
        // 🔒 SECURITY: Extra bestandsvalidatie
        $securityCheck = $this->validateUploadedFile($_FILES['avatar'], $allowedTypes, $maxSize);
        if (!$securityCheck['valid']) {
            $message = $securityCheck['message'];
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $_SESSION['error_message'] = $message;
            redirect('profile/edit');
            return;
        }
        
        // Upload de avatar
        $uploadResult = upload_file(
            $_FILES['avatar'],
            'avatars',
            $allowedTypes,
            $maxSize,
            'avatar_' . $userId . '_'
        );
        
        if ($uploadResult['success']) {
            try {
                // Update de database met het nieuwe avatar pad
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
                
                // 🔒 SECURITY: Log avatar upload activiteit (NU WEL ACTIEF!)
                $this->logAvatarUpload($userId);
                
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
     * 🔒 SECURITY: Valideer geüploade bestanden tegen malicious content
     */
    private function validateUploadedFile($file, $allowedTypes, $maxSize)
    {
        $result = ['valid' => false, 'message' => ''];
        
        // Check bestandsgrootte
        if ($file['size'] > $maxSize) {
            $result['message'] = 'Bestand is te groot. Maximaal ' . round($maxSize / (1024*1024), 1) . 'MB toegestaan.';
            return $result;
        }
        
        // Check MIME type
        if (!in_array($file['type'], $allowedTypes)) {
            $result['message'] = 'Bestandstype niet toegestaan. Alleen ' . implode(', ', $allowedTypes) . ' zijn toegestaan.';
            return $result;
        }
        
        // Check file extension tegen MIME type mismatch
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $result['message'] = 'Bestandsextensie niet toegestaan.';
            return $result;
        }
        
        // Basic header check voor images
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $result['message'] = 'Bestand is geen geldige afbeelding.';
            return $result;
        }
        
        // Check of MIME type en getimagesize overeenkomen
        $imageMimeTypes = [
            IMAGETYPE_JPEG => 'image/jpeg',
            IMAGETYPE_PNG => 'image/png', 
            IMAGETYPE_GIF => 'image/gif',
            IMAGETYPE_WEBP => 'image/webp'
        ];
        
        if (!isset($imageMimeTypes[$imageInfo[2]]) || $imageMimeTypes[$imageInfo[2]] !== $file['type']) {
            $result['message'] = 'Bestandstype komt niet overeen met bestandsinhoud.';
            return $result;
        }
        
        $result['valid'] = true;
        return $result;
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
     * Toon beveiligingsinstellingen pagina
     */
    public function security()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $data = [
            'title' => 'Beveiligingsinstellingen',
            'user' => $this->getCurrentUserProfile()
        ];

        $this->view('profile/security', $data);
    }

    /**
     * Toon privacy instellingen pagina (redirect naar nieuwe PrivacyController)
     */
    public function privacy()
    {
        // Redirect naar de nieuwe privacy controller
        header('Location: /?route=privacy');
        exit;
    }

    /**
     * Toon notificatie voorkeuren pagina
     */
    public function notifications()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $data = [
            'title' => 'Notificatie voorkeuren',
            'user' => $this->getCurrentUserProfile()
        ];

        $this->view('profile/notifications', $data);
    }

    /**
     * Helper: Haal huidige gebruiker profiel data op
     */
    private function getCurrentUserProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->getUserData($_SESSION['user_id'], $_SESSION['username'] ?? '');
    }

    /**
     * 🔒 PRIVACY: Check of viewer het profiel mag bekijken
     */
    private function canViewProfile($profileUserId, $viewerUserId)
    {
        // Eigenaar kan altijd eigen profiel zien
        if ($profileUserId == $viewerUserId) {
            return true;
        }

        // Haal privacy instellingen op
        $privacySettings = $this->getPrivacySettings($profileUserId);
        
        if (!$privacySettings) {
            // Geen privacy instellingen = openbaar (backwards compatibility)
            return true;
        }

        switch ($privacySettings['profile_visibility']) {
            case 'public':
                return true;
                
            case 'private':
                return false;
                
            case 'friends':
                return $this->areFriends($profileUserId, $viewerUserId);
                
            default:
                return true; // Fallback
        }
    }

    /**
     * 🔒 PRIVACY: Check of viewer de foto's mag bekijken
     */
    private function canViewPhotos($profileUserId, $viewerUserId)
    {
        // Eigenaar kan altijd eigen foto's zien
        if ($profileUserId == $viewerUserId) {
            return true;
        }

        // Haal privacy instellingen op
        $privacySettings = $this->getPrivacySettings($profileUserId);
        
        if (!$privacySettings) {
            // Geen privacy instellingen = openbaar
            return true;
        }

        switch ($privacySettings['photos_visibility']) {
            case 'public':
                return true;
                
            case 'private':
                return false;
                
            case 'friends':
                return $this->areFriends($profileUserId, $viewerUserId);
                
            default:
                return true; // Fallback
        }
    }

    /**
     * 🔒 PRIVACY: Check of viewer berichten mag sturen
     */
    private function canSendMessage($profileUserId, $viewerUserId)
    {
        // Kan niet naar jezelf sturen (technisch wel mogelijk, maar onzinnig)
        if ($profileUserId == $viewerUserId) {
            return false;
        }

        // Haal privacy instellingen op
        $privacySettings = $this->getPrivacySettings($profileUserId);
        
        if (!$privacySettings) {
            // Geen privacy instellingen = iedereen mag berichten sturen
            return true;
        }

        switch ($privacySettings['messages_from']) {
            case 'everyone':
                return true;
                
            case 'nobody':
                return false;
                
            case 'friends':
                return $this->areFriends($profileUserId, $viewerUserId);
                
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

    /**
     * 🔒 PRIVACY: Filter contactgegevens op basis van privacy instellingen
     */
    private function filterContactInfo($userData, $viewerUserId)
    {
        // Eigenaar ziet altijd alles
        if ($userData['id'] == $viewerUserId) {
            return $userData;
        }

        $privacySettings = $this->getPrivacySettings($userData['id']);
        
        if (!$privacySettings) {
            // Geen privacy instellingen, alles tonen
            return $userData;
        }

        $areFriends = $this->areFriends($userData['id'], $viewerUserId);

        // Filter e-mail
        switch ($privacySettings['show_email']) {
            case 'private':
                $userData['email'] = '';
                break;
            case 'friends':
                if (!$areFriends) {
                    $userData['email'] = '';
                }
                break;
            // 'public' case: laat email zien
        }

        // Filter telefoon
        switch ($privacySettings['show_phone']) {
            case 'private':
                $userData['phone'] = '';
                break;
            case 'friends':
                if (!$areFriends) {
                    $userData['phone'] = '';
                }
                break;
            // 'public' case: laat telefoon zien
        }

        return $userData;
    }

    /**
     * 🔒 SECURITY: Check avatar upload rate limiting (1 per uur)
     */
    private function checkAvatarUploadRateLimit($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'avatar_upload' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$userId]);
            $recentUploads = $stmt->fetchColumn();
            
            return $recentUploads < 1;
        } catch (\Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return true; // Bij fout, sta upload toe
        }
    }

    /**
     * 🔒 SECURITY: Check profile update rate limiting (5 per uur)
     */
    private function checkProfileUpdateRateLimit($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'profile_update' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$userId]);
            $recentUpdates = $stmt->fetchColumn();
            
            $maxUpdatesPerHour = SecuritySettings::get('max_profile_updates_per_hour', 5);
            return $recentUpdates < $maxUpdatesPerHour;
        } catch (\Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return true; // Bij fout, sta update toe
        }
    }


    /**
     * 🔒 SECURITY: Log avatar upload activity
     */
    private function logAvatarUpload($userId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'avatar_upload', ?, ?, ?, NOW())
            ");
            
            $details = json_encode([
                'type' => 'avatar_upload',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([
                $userId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            error_log("Activity logging error: " . $e->getMessage());
        }
    }


    /**
     * 🔒 SECURITY: Sanitize profile input data
     */
    private function sanitizeProfileInput($data)
    {
        return [
            'display_name' => htmlspecialchars(trim($data['display_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'bio' => htmlspecialchars(trim($data['bio'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'location' => htmlspecialchars(trim($data['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'website' => filter_var(trim($data['website'] ?? ''), FILTER_SANITIZE_URL),
            'phone' => preg_replace('/[^+\-\s\(\)\d]/', '', trim($data['phone'] ?? '')),
            'date_of_birth' => trim($data['date_of_birth'] ?? ''),
            'gender' => in_array($data['gender'] ?? '', ['male', 'female', 'other', '']) ? $data['gender'] : ''
        ];
    }

    /**
     * 🔒 SECURITY: Validate profile data met configureerbare limits
     */
    private function validateProfileData($data)
    {
        $errors = [];
        
        // Display name is verplicht
        if (empty($data['display_name'])) {
            $errors['display_name'] = 'Weergavenaam is verplicht';
        } elseif (strlen($data['display_name']) > 50) {
            $errors['display_name'] = 'Weergavenaam mag maximaal 50 karakters bevatten';
        }
        
        // Bio length check
        $maxBioLength = SecuritySettings::get('max_bio_length', 500);
        if (strlen($data['bio']) > $maxBioLength) {
            $errors['bio'] = "Bio mag maximaal {$maxBioLength} karakters bevatten";
        }
        
        // Website URL validatie (als ingevuld)
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Voer een geldige website URL in';
        }
        
        // Telefoonnummer validatie (basis)
        if (!empty($data['phone']) && !preg_match('/^[\+\-\s\(\)\d]+$/', $data['phone'])) {
            $errors['phone'] = 'Voer een geldig telefoonnummer in';
        }
        
        // Geboortedatum validatie
        if (!empty($data['date_of_birth'])) {
            $birthDate = \DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
            if (!$birthDate || $birthDate > new \DateTime()) {
                $errors['date_of_birth'] = 'Voer een geldige geboortedatum in';
            }
            
            // Check minimum leeftijd (13 jaar - COPPA compliance)
            $minAge = new \DateTime('-13 years');
            if ($birthDate > $minAge) {
                $errors['date_of_birth'] = 'Je moet minimaal 13 jaar oud zijn om dit platform te gebruiken';
            }
        }
        
        return $errors;
    }

    /**
     * 🔒 SECURITY: Check foto upload rate limiting (5 per uur)
     */
    private function checkPhotoUploadRateLimit($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'photo_upload' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$userId]);
            $recentUploads = $stmt->fetchColumn();
            
            $maxUploadsPerHour = SecuritySettings::get('max_photo_uploads_per_hour', 5);
            return $recentUploads < $maxUploadsPerHour;
        } catch (\Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return true; // Bij fout, sta upload toe
        }
    }

    /**
     * 🔒 SECURITY: Log foto upload activity
     */
    private function logPhotoUpload($userId, $postId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'photo_upload', ?, ?, ?, NOW())
            ");
            
            $details = json_encode([
                'type' => 'photo_upload',
                'post_id' => $postId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([
                $userId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            error_log("Activity logging error: " . $e->getMessage());
        }
    }

    /**
         * 🔒 SECURITY: Process en filter post content voor veiligheid
         */
        private function processProfilePostContent($content)
        {
            // Apply profanity filter als ingeschakeld
            $filtered = SecuritySettings::filterProfanity($content);
            
            // Maak hashtags klikbaar (bestaande functionaliteit)
            $filtered = preg_replace('/#([a-zA-Z0-9_]+)/', '<a href="/?route=search&q=%23$1" class="hashtag">#$1</a>', $filtered);
            
            // Maak @mentions klikbaar
            $filtered = preg_replace('/@([a-zA-Z0-9_]+)/', '<a href="/?route=profile&username=$1" class="mention">@$1</a>', $filtered);
            
            return $filtered;
        }

        /**
         * 🔒 SECURITY: Check krabbels rate limiting (10 per uur)
         */
        private function checkKrabbelsRateLimit($userId)
        {
            try {
                $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'krabbel_post' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
                $stmt->execute([$userId]);
                $recentKrabbels = $stmt->fetchColumn();

                $maxKrabbelsPerHour = SecuritySettings::get('max_krabbels_per_hour', 10);
                return $recentKrabbels < $maxKrabbelsPerHour;
            } catch (\Exception $e) {
                error_log("Rate limit check error: " . $e->getMessage());
                return true; // Bij fout, sta actie toe
            }
        }

        /**
         * 🔒 SECURITY: Log krabbel activity
         */
        private function logKrabbel($senderId, $receiverId)
        {
            try {
                $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'krabbel_post', ?, ?, ?, NOW())
            ");

                $details = json_encode([
                    'type' => 'krabbel_post',
                    'receiver_id' => $receiverId,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);

                $stmt->execute([
                    $senderId,
                    $details,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
            } catch (\Exception $e) {
                error_log("Activity logging error: " . $e->getMessage());
            }
        }

        /**
         * 🔒 SECURITY: Check general rate limit voor verschillende acties
         */
        private function checkGeneralRateLimit($userId, $action, $maxPerHour = 10)
        {
            try {
                $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
                $stmt->execute([$userId, $action]);
                $recentActions = $stmt->fetchColumn();

                return $recentActions < $maxPerHour;
            } catch (\Exception $e) {
                error_log("Rate limit check error: " . $e->getMessage());
                return true; // Bij fout, sta actie toe
            }
        }

        /**
         * 🔒 SECURITY: Check IP-based rate limiting
         */
        private function checkIpRateLimit($action, $maxPerHour = 20)
        {
            try {
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

                $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE ip_address = ? 
                AND action = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
                $stmt->execute([$ipAddress, $action]);
                $recentActions = $stmt->fetchColumn();

                return $recentActions < $maxPerHour;
            } catch (\Exception $e) {
                error_log("IP rate limit check error: " . $e->getMessage());
                return true; // Bij fout, sta actie toe
            }
        }

        /**
         * 🔒 SECURITY: Comprehensive security check voor alle profile acties
         */
        private function performSecurityChecks($userId, $action, $content = '', $fileData = null)
        {
            $result = ['passed' => true, 'message' => ''];

            // Rate limiting check
            $rateLimits = [
                'profile_update' => 5,
                'avatar_upload' => 1,
                'photo_upload' => 5,
                'krabbel_post' => 10
            ];

            $maxPerHour = $rateLimits[$action] ?? 10;
            if (!$this->checkGeneralRateLimit($userId, $action, $maxPerHour)) {
                $result['passed'] = false;
                $result['message'] = "Te veel {$action} pogingen. Probeer het later opnieuw.";
                return $result;
            }

            // IP rate limiting
            if (!$this->checkIpRateLimit($action, 20)) {
                $result['passed'] = false;
                $result['message'] = 'Te veel activiteit vanaf dit IP-adres. Probeer het later opnieuw.';
                return $result;
            }

            // Content validatie (als content aanwezig is)
            if (!empty($content)) {
                $contentCheck = $this->validateContentSecurity($content, $action);
                if (!$contentCheck['valid']) {
                    $result['passed'] = false;
                    $result['message'] = $contentCheck['message'];
                    return $result;
                }
            }

            // File validatie (als bestand aanwezig is)
            if ($fileData !== null) {
                $uploadType = in_array($action, ['avatar_upload']) ? 'avatar' : 'general';
                $validationResult = SecuritySettings::validateUploadSettings(
                    $fileData['size'],
                    $fileData['type'],
                    $uploadType
                );

                if (!$validationResult['valid']) {
                    $result['passed'] = false;
                    $result['message'] = $validationResult['message'];
                    return $result;
                }
            }

            return $result;
        }

        /**
     * 🔒 SECURITY: Log profile update activity
     */
    private function logProfileUpdate($userId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'profile_update', ?, ?, ?, NOW())
            ");
            
            $details = json_encode([
                'type' => 'profile_update',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([
                $userId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            error_log("Activity logging error: " . $e->getMessage());
        }
    }

    /**
     * 🔒 SECURITY: Validate content tegen spam en misbruik
     */
    private function validateContentSecurity($content, $type = 'post')
    {
        $result = ['valid' => true, 'message' => ''];
        
        // Check lengte limits
        $limits = SecuritySettings::getContentLimits();
        $maxLength = $limits["max_{$type}_length"] ?? 1000;
        
        if (strlen($content) > $maxLength) {
            $result['valid'] = false;
            $result['message'] = "Content mag maximaal {$maxLength} karakters bevatten.";
            return $result;
        }
        
        // Check voor verdachte patronen (spam)
        $suspiciousPatterns = [
            '/(.)\1{10,}/', // Herhaalde karakters (meer dan 10x)
            '/https?:\/\/[^\s]{50,}/', // Zeer lange URLs
            '/\b(viagra|casino|lottery|winner)\b/i', // Veelgebruikte spam woorden
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $result['valid'] = false;
                $result['message'] = 'Content bevat verdachte patronen en kan niet worden geplaatst.';
                return $result;
            }
        }
        
        return $result;
    }

    /**
     * Toon avatar beheer pagina (aparte pagina voor avatar upload)
     */
    public function avatar()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->getUserData($userId, $_SESSION['username'] ?? '');

        $data = [
            'title' => 'Profielfoto beheren',
            'user' => $user
        ];

        $this->view('profile/avatar', $data);
    }


}