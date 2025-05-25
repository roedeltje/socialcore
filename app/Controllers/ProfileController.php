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
    public function index($usernameFromUrl = null)
    {
        // Controleer of er een specifieke gebruiker is opgevraagd
        $requestedUsername = $_GET['username'] ?? null;
        // Bepaal welke tab actief is
        $activeTab = $_GET['tab'] ?? 'over';
        
        // Bepaal de gebruiker wiens profiel wordt bekeken
        $userId = null;
        $username = null;
        
        // Als een username is meegegeven, probeer de gebruiker op te halen
        if ($requestedUsername) {
            try {
                $stmt = $this->db->prepare("SELECT id, username FROM users WHERE username = ?");
                $stmt->execute([$requestedUsername]);
                $userBasic = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userBasic) {
                    $userId = $userBasic['id'];
                    $username = $userBasic['username'];
                }
            } catch (\Exception $e) {
                // Bij een fout, gebruik de huidige gebruiker
                error_log("Error fetching user by username: " . $e->getMessage());
            }
        }
        
        // Als geen gebruiker is gevonden of meegegeven, gebruik de ingelogde gebruiker
        if (!$userId) {
            if (!isset($_SESSION['user_id'])) {
                redirect('login');
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $username = $_SESSION['username'] ?? 'gebruiker';
        }
        
        // Haal gebruikersgegevens op
        $user = $this->getUserData($userId, $username);
        
        // Bepaal of de kijker de eigenaar is
        $viewerIsOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId;
        
        // Laad vrienden
        $friends = $this->getFriends($userId);
        
        // Laad posts
        $posts = $this->getUserPosts($userId);
        
        // Laad specifieke data op basis van de geselecteerde tab
        $krabbels = [];
        $fotos = [];
        
        if ($activeTab === 'krabbels') {
            $krabbels = $this->getKrabbels($userId);
        } elseif ($activeTab === 'fotos') {
            $fotos = $this->getFotos($userId);
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
     * Haal gebruikersgegevens op
     */
    private function getUserData($userId, $username)
    {
        // Later vervangen door echte database query
        // In de tussentijd gebruiken we deze dummy data
        
        try {
            // Probeer eerst uit de database te halen
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.username, 
                    u.email,
                    u.created_at,
                    COALESCE(up.display_name, u.username) as name,
                    up.bio,
                    up.location,
                    up.favorite_quote,
                    up.avatar_path
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dbUser) {
                // Formateer de data
                return [
                    'id' => $dbUser['id'],
                    'username' => $dbUser['username'],
                    'name' => $dbUser['name'] ?? $dbUser['username'],
                    'bio' => $dbUser['bio'] ?? 'Welkom op mijn SocialCore profiel!',
                    'location' => $dbUser['location'] ?? 'Nederland',
                    'joined' => date('d M Y', strtotime($dbUser['created_at'] ?? 'now')),
                    'avatar' => $dbUser['avatar_path'] ?? 'avatars/2025/05/default-avatar.png',
                    'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'], // Nog geen tabel voor interesses
                    'favorite_quote' => $dbUser['favorite_quote'] ?? 'De beste manier om de toekomst te voorspellen is haar te creëren.'
                ];
            }
        } catch (\Exception $e) {
            error_log("Error getting user data: " . $e->getMessage());
            // Bij een fout, gebruik dummy data
        }
        
        // Fallback naar dummy data
        return [
            'id' => $userId,
            'username' => $username,
            'name' => 'Gebruiker', 
            'bio' => 'Welkom op mijn SocialCore profiel!',
            'location' => 'Nederland',
            'joined' => '16 mei 2025',
            'avatar' => 'avatars/2025/05/default-avatar.png',
            'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
            'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
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
        $form = new \App\Helpers\FormHelper();
        
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        // Haal gebruikersgegevens op
        $userId = $_SESSION['user_id'];
        $user = $this->getUserData($userId, $_SESSION['username'] ?? '');
        
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
        
        // Valideer en verwerk het formulier
        // Later: echte database implementatie
        
        set_flash_message('success', 'Je profiel is bijgewerkt!');
        redirect('profile');
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
}