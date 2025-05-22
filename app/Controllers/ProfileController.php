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
    
    if ($requestedUsername) {
        // Haal het profiel op van de opgevraagde gebruiker
        // Later: $user = User::findByUsername($requestedUsername);
        $user = [
            'id' => $_SESSION['user_id'], // ← FIX: gebruik sessie user_id
            'username' => $_SESSION['username'] ?? 'gebruiker',
            'name' => 'Rudy',
            'bio' => 'Dit is een demonstratie profiel in Hyves-stijl voor SocialCore!',
            'location' => 'Nederland',
            'joined' => '10 mei 2025',
            'avatar' => 'avatars/2025/05/default-avatar.png',
            'interests' => ['Programmeren', 'Open Source', 'PHP', 'Webdesign', 'Nostalgie'],
            'favorite_quote' => 'Code is poëzie in een digitaal universum.'
        ];
    } else {
        // Geen gebruiker opgegeven, toon het profiel van de ingelogde gebruiker
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        // Later: $user = User::find($_SESSION['user_id']);
        $user = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'gebruiker',
            'name' => 'Rudy',
            'bio' => 'Welkom op mijn SocialCore profiel!',
            'location' => 'Nederland',
            'joined' => '16 mei 2025',
            'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
            'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
            'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
        ];
    }
    
    // Dummy data voor vrienden
    $friends = [
        ['id' => 3, 'name' => 'Daan de Vries', 'username' => 'daanvr', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ['id' => 4, 'name' => 'Sophie Bakker', 'username' => 'sophieb', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ['id' => 5, 'name' => 'Tim Jansen', 'username' => 'timj', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ['id' => 6, 'name' => 'Emma Visser', 'username' => 'emmav', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ['id' => 7, 'name' => 'Luuk Smit', 'username' => 'luuks', 'avatar' => 'avatars/2025/05/default-avatar.png'],
        ['id' => 8, 'name' => 'Isa van Dijk', 'username' => 'isad', 'avatar' => 'avatars/2025/05/default-avatar.png'],
    ];
    
    // Dummy data voor recente posts
    $posts = $this->getUserPosts($user['id']);
    
    // Laad specifieke data op basis van de geselecteerde tab
    $krabbels = [];
    $fotos = [];
    
    if ($activeTab === 'krabbels') {
        $krabbels = $this->getDummyKrabbels($user['id']);
    } elseif ($activeTab === 'fotos') {
        $fotos = $this->getDummyFotos($user['id']);
    }
    
    $data = [
        'title' => $user['name'] . ' - Profiel',
        'user' => $user,
        'friends' => $friends,
        'posts' => $posts,
        'krabbels' => $krabbels,
        'fotos' => $fotos,
        'viewer_is_owner' => isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user['id'],
        'active_tab' => $activeTab
    ];
    
    $this->view('profile/index', $data);
}

    /**
     * Haal dummy krabbels op (later te vervangen door echte database query)
     */
    private function getDummyKrabbels($userId)
{
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
     * Haal dummy foto's op (later te vervangen door echte database query)
     */
    private function getDummyFotos($userId)
{
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

    // Rest van de methoden blijven ongewijzigd
    public function edit()
    {
		$form = new \App\Helpers\FormHelper();
		
        // Ongewijzigd laten
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        // Later: $user = User::find($_SESSION['user_id']);
        $user = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'gebruiker',
            'name' => 'Huidige Gebruiker',
            'bio' => 'Welkom op mijn SocialCore profiel!',
            'location' => 'Nederland',
            'email' => 'gebruiker@example.com',
            'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
            'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
            'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
        ];
        
        $data = [
            'title' => 'Profiel bewerken',
            'user' => $user
        ];
        
        $this->view('profile/edit', [
        // Andere variabelen...
        'form' => $form
    ]);
    }

    public function update()
    {
        // Ongewijzigd laten
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

    public function updateAvatar() {
        // Ongewijzigd laten
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
    
    // Nieuwe methoden voor de tab-functionaliteit
    
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
    redirect('?route=profile&username=' . urlencode($receiverUsername) . '&tab=krabbels');
}
    public function viewProfile() {
    $userId = $_GET['id'] ?? null;
    
    if (!$userId) {
        // Redirect naar eigen profiel of toon een foutmelding
        return redirect('profile');
    }
    
    // Haal gebruikersgegevens op voor het opgegeven ID
    $userProfile = $this->getUserProfile($userId);
    
    if (!$userProfile) {
        // Gebruiker niet gevonden
        return $this->view('profile/not_found');
    }
    
    // Toon het profiel van de gebruiker
    return $this->view('profile/view', ['user' => $userProfile]);
}

    /**
     * Toont de privacy-instellingen pagina
     */
    public function privacy()
{
    $form = new \App\Helpers\FormHelper();
    
    // Controleer of de gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
        return;
    }
    
    // Later: $user = User::find($_SESSION['user_id']);
    $user = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'gebruiker',
        'name' => 'Huidige Gebruiker',
        'bio' => 'Welkom op mijn SocialCore profiel!',
        'location' => 'Nederland',
        'email' => 'gebruiker@example.com',
        'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
        'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
        'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
    ];
    
    $data = [
        'title' => __('settings.privacy'),
        'user' => $user,
        'activeTab' => 'privacy'
    ];
    
    $this->view('profile/edit', array_merge($data, ['form' => $form]));
}

    /**
     * Toont de notificatie-instellingen pagina
     */
    public function notifications()
{
    $form = new \App\Helpers\FormHelper();
    
    // Controleer of de gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
        return;
    }
    
    // Later: $user = User::find($_SESSION['user_id']);
    $user = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'gebruiker',
        'name' => 'Huidige Gebruiker',
        'bio' => 'Welkom op mijn SocialCore profiel!',
        'location' => 'Nederland',
        'email' => 'gebruiker@example.com',
        'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
        'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
        'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
    ];
    
    $data = [
        'title' => __('settings.notifications'),
        'user' => $user,
        'activeTab' => 'notifications'
    ];
    
    $this->view('profile/edit', array_merge($data, ['form' => $form]));
}

    /**
 * Haal echte posts op van een specifieke gebruiker
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
                p.likes_count,
                p.comments_count,
                u.id as user_id,
                u.username,
                COALESCE(up.display_name, u.username) as user_name
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
        
        // Format de data voor de view (zoals in FeedController)
        foreach ($posts as &$post) {
            $post['likes'] = $post['likes_count'];
            $post['comments'] = $post['comments_count'];
            $post['created_at'] = $this->formatDate($post['created_at']);
        }
        
        return $posts;
        
    } catch (Exception $e) {
        // Als er een fout is, return lege array zodat de pagina niet crasht
        error_log("Error getting user posts: " . $e->getMessage());
        return [];
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
     * Toont de beveiligingsinstellingen pagina
     */
    public function security()
{
    $form = new \App\Helpers\FormHelper();
    
    // Debug output
    //echo "<pre>";
    //echo "Debug in security() method:";
    //echo "\nactiveTab set to: 'security'";
    //echo "</pre>";
    
    // Controleer of de gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
        return;
    }
    
    // Later: $user = User::find($_SESSION['user_id']);
    $user = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'gebruiker',
        'name' => 'Huidige Gebruiker',
        'bio' => 'Welkom op mijn SocialCore profiel!',
        'location' => 'Nederland',
        'email' => 'gebruiker@example.com',
        'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
        'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
        'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
    ];
    
    $data = [
        'title' => __('settings.account_security'),
        'user' => $user,
        'activeTab' => 'security',
        'form' => $form
    ];
    
    $this->view('profile/edit', $data);
}
}