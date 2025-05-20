<?php

namespace App\Controllers;

use App\Database\Database;
use App\Helpers\FormHelper;
use App\Auth\Auth;

class SettingsController extends Controller
{
    protected $db;
    protected $currentUser;
    
    public function __construct()
{
    // Geen parent::__construct() omdat de base Controller geen constructor heeft
    
    // Database connectie via singleton pattern
    $this->db = Database::getInstance();
    
    // Debug database object type
    var_dump(get_class($this->db));
    
    // Debug een query en resultaat
    $debugResult = $this->db->query("SELECT 1");
    var_dump(get_class($debugResult));
    
    // Huidige gebruiker ophalen - aangepast aan de sessie structuur
    if (isset($_SESSION['user_id'])) {
        // Maak een basis gebruiker uit sessie
        $this->currentUser = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'unknown'
        ];
    } else {
        $this->currentUser = null;
    }
}
    
    public function index()
{
    // Debug in plaats van redirect
    echo "<h1>SettingsController::index()</h1>";
    echo "<p>currentUser: ";
    var_dump($this->currentUser);
    echo "</p>";
    
    // Sessie info
    echo "<p>SESSION: ";
    var_dump($_SESSION);
    echo "</p>";
    
    // Link naar profile
    echo "<p><a href='" . base_url('settings/profile') . "'>Ga naar profielpagina</a></p>";
    exit;
}
    
    public function profile()
    {
		// Debug sessie en currentUser
    echo "<pre>DEBUG INFO:";
    echo "\nSession data: ";
    print_r($_SESSION);
    echo "\nthis->currentUser: ";
    print_r($this->currentUser);
    echo "\nAuth::check() result: " . (\App\Auth\Auth::check() ? 'true' : 'false');
    echo "</pre>";
		
		// Controleer of gebruiker is ingelogd
    if (!$this->currentUser) {
        echo "Je moet ingelogd zijn om deze pagina te bekijken.";
        exit;
    }
		
        $userId = $this->currentUser['id'] ?? 0; // Default naar 0 als id niet bestaat
		
		// Veilige versie van getUserProfile
    try {
        $userProfile = $this->getUserProfile($userId);
    } catch (\Exception $e) {
        $userProfile = []; // Default leeg profiel
    }
        
        // Form helper voor validatie
        $form = new FormHelper();
        
        // Controleren of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valideren en verwerken
            $this->processProfileUpdate($form);
        }
        
        // Profielbewerking weergeven
        $this->view('profile/edit-profile/index', [
            'title' => __('settings.profile_settings'),
            'user' => $this->currentUser,
            'profile' => $userProfile,
            'form' => $form,
            'activeTab' => 'profile'
        ]);
    }
    
    public function account()
    {
        // Form helper voor validatie
        $form = new FormHelper();
        
        // Controleren of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valideren en verwerken
            $this->processAccountUpdate($form);
        }
        
        // Account-instellingen weergeven
        $this->view('profile/edit-profile/index', [
            'title' => __('settings.account_settings'),
            'user' => $this->currentUser,
            'form' => $form,
            'activeTab' => 'account'
        ]);
    }
    
    public function privacy()
    {
        // Privacy-instellingen ophalen
        $privacySettings = $this->getPrivacySettings($this->currentUser['id']);
        
        // Form helper voor validatie
        $form = new FormHelper();
        
        // Controleren of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valideren en verwerken
            $this->processPrivacyUpdate($form);
        }
        
        // Privacy-instellingen weergeven
        $this->view('profile/edit-profile/index', [
            'title' => __('settings.privacy_settings'),
            'user' => $this->currentUser,
            'privacy' => $privacySettings,
            'form' => $form,
            'activeTab' => 'privacy'
        ]);
    }
    
    public function notifications()
    {
        // Notificatie-instellingen ophalen
        $notificationSettings = $this->getNotificationSettings($this->currentUser['id']);
        
        // Form helper voor validatie
        $form = new FormHelper();
        
        // Controleren of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valideren en verwerken
            $this->processNotificationUpdate($form);
        }
        
        // Notificatie-instellingen weergeven
        $this->view('profile/edit-profile/index', [
            'title' => __('settings.notification_settings'),
            'user' => $this->currentUser,
            'notifications' => $notificationSettings,
            'form' => $form,
            'activeTab' => 'notifications'
        ]);
    }
    
    public function avatar()
    {
        // Form helper voor validatie
        $form = new FormHelper();
        
        // Controleer of er een verwijderverzoek is
        if (isset($_POST['remove_avatar'])) {
            $this->removeAvatar();
            header('Location: ' . base_url('settings/profile'));
            exit;
        }
        
        // Controleren of er een bestand is geüpload
        if (!empty($_FILES['avatar']['name'])) {
            $this->processAvatarUpload($form);
        } else {
            $_SESSION['error_message'] = __('settings.no_file_selected');
        }
        
        // Terug naar profielpagina
        header('Location: ' . base_url('settings/profile'));
        exit;
    }
    
    // Helper methods
    
    protected function getUserProfile($userId)
{
    $query = "SELECT * FROM user_profiles WHERE user_id = ?";
    $stmt = $this->db->query($query, [$userId]);
    
    // Voor PDO, fetch gebruiken om een rij op te halen
    $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($profile) {
        return $profile;
    }
    
    // Als er geen profiel is, retourneer een leeg profiel
    return [
        'user_id' => $userId,
        'display_name' => $this->currentUser['display_name'] ?? $this->currentUser['username'],
        'avatar' => '',
        'cover_photo' => '',
        'bio' => '',
        'location' => '',
        'website' => '',
        'date_of_birth' => null,
        'gender' => '',
        'phone' => ''
    ];
}
    
    protected function getPrivacySettings($userId)
    {
        // In een echte implementatie zou je deze uit een user_settings tabel halen
        // Voor nu retourneren we standaardinstellingen
        return [
            'profile_visibility' => 'public',
            'comment_permission' => 'everyone',
            'friend_requests' => 'everyone'
        ];
    }
    
    protected function getNotificationSettings($userId)
    {
        // In een echte implementatie zou je deze uit een user_settings tabel halen
        // Voor nu retourneren we standaardinstellingen
        return [
            'email_friend_requests' => 1,
            'email_messages' => 1,
            'email_comments' => 0,
            'notify_friend_requests' => 1,
            'notify_messages' => 1,
            'notify_comments' => 1,
            'notify_likes' => 1
        ];
    }
    
    protected function processProfileUpdate($form)
    {
        // Validatie regels
        $form->validate([
            'display_name' => 'required|min:3|max:50',
            'bio' => 'max:500',
            'location' => 'max:100',
            'website' => 'max:200'
        ]);
        
        // Als er validatiefouten zijn, stop dan
        if ($form->hasErrors()) {
            return false;
        }
        
        // Update profiel in database
        $userId = $this->currentUser['id'];
        $displayName = $_POST['display_name'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $location = $_POST['location'] ?? '';
        $website = $_POST['website'] ?? '';
        
        // Controleren of er al een profiel bestaat
        $existingProfile = $this->getUserProfile($userId);

if (isset($existingProfile['id'])) {
    // Update bestaand profiel
    $query = "UPDATE user_profiles SET 
        display_name = ?, bio = ?, location = ?, website = ?, updated_at = NOW()
        WHERE user_id = ?";
    $this->db->query($query, [$displayName, $bio, $location, $website, $userId]);
} else {
    // Nieuw profiel aanmaken
    $query = "INSERT INTO user_profiles (user_id, display_name, bio, location, website, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $this->db->query($query, [$userId, $displayName, $bio, $location, $website]);
}
        
        // Update ook display_name in users tabel
        $updateUserQuery = "UPDATE users SET display_name = ?, updated_at = NOW() WHERE id = ?";
        $this->db->query($updateUserQuery, [$displayName, $userId]);
        
        // Update sessie
        $_SESSION['user']['display_name'] = $displayName;
        
        $_SESSION['success_message'] = __('settings.profile_updated');
        return true;
    }
    
    protected function processAccountUpdate($form)
    {
        // Validatie regels
        $form->validate([
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'min:8', // Optioneel, alleen valideren als niet leeg
            'new_password_confirmation' => 'same:new_password'
        ]);
        
        // Als er validatiefouten zijn, stop dan
        if ($form->hasErrors()) {
            return false;
        }
        
        // Controleer huidige wachtwoord
        $userId = $this->currentUser['id'];
$currentPassword = $_POST['current_password'] ?? '';

$query = "SELECT password FROM users WHERE id = ?";
$stmt = $this->db->query($query, [$userId]);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$user || !password_verify($currentPassword, $user['password'])) {
    $form->addError('current_password', __('settings.current_password_incorrect'));
    return false;
}
        
        // Update e-mail
        $email = $_POST['email'] ?? '';
        
        // Controleer of e-mail beschikbaar is (als het is gewijzigd)
        if ($email !== $this->currentUser['email']) {
    $query = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $this->db->query($query, [$email, $userId]);
    $existingEmail = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($existingEmail) {
        $form->addError('email', __('settings.email_already_used'));
        return false;
    }
            
            // Update e-mail
            $updateEmailQuery = "UPDATE users SET email = ?, updated_at = NOW() WHERE id = ?";
            $this->db->query($updateEmailQuery, [$email, $userId]);
            
            // Update sessie
            $_SESSION['user']['email'] = $email;
        }
        
        // Update wachtwoord (als ingevuld)
        $newPassword = $_POST['new_password'] ?? '';
        
        if (!empty($newPassword)) {
            // Update wachtwoord
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            $this->db->query($updatePasswordQuery, [$hashedPassword, $userId]);
        }
        
        $_SESSION['success_message'] = __('settings.account_updated');
        return true;
    }
    
    protected function processPrivacyUpdate($form)
    {
        // Voor nu slaan we deze instellingen niet op
        // In een echte implementatie zou je deze in een user_settings tabel opslaan
        $_SESSION['success_message'] = __('settings.privacy_updated');
        return true;
    }
    
    protected function processNotificationUpdate($form)
    {
        // Voor nu slaan we deze instellingen niet op
        // In een echte implementatie zou je deze in een user_settings tabel opslaan
        $_SESSION['success_message'] = __('settings.notifications_updated');
        return true;
    }
    
    protected function processAvatarUpload($form)
    {
        $userId = $this->currentUser['id'];
        $file = $_FILES['avatar'];
        
        // Valideer het bestand
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_message'] = __('settings.upload_error');
            return false;
        }
        
        // Controleer bestandstype
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $_SESSION['error_message'] = __('settings.invalid_file_type');
            return false;
        }
        
        // Controleer bestandsgrootte (max 5 MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = __('settings.file_too_large');
            return false;
        }
        
        // Maak een unieke bestandsnaam
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        
        // Maak de uploadmap (als deze nog niet bestaat)
        $yearMonth = date('Y/m');
        $uploadDir = FCPATH . 'uploads/avatars/' . $yearMonth;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $targetPath = $uploadDir . '/' . $newFilename;
        
        // Verplaats het bestand
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['error_message'] = __('settings.upload_failed');
            return false;
        }
        
        // Update de database
        $avatarPath = $yearMonth . '/' . $newFilename;
        
        // Haal het huidige profiel op
        $currentProfile = $this->getUserProfile($userId);
        
        if (isset($currentProfile['id'])) {
            // Update bestaand profiel
            $query = "UPDATE user_profiles SET avatar = ?, updated_at = NOW() WHERE user_id = ?";
            $this->db->query($query, [$avatarPath, $userId]);
        } else {
            // Nieuw profiel aanmaken met avatar
            $query = "INSERT INTO user_profiles (user_id, avatar, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())";
            $this->db->query($query, [$userId, $avatarPath]);
        }
        
        // Verwijder de oude avatar (als die bestond)
        if (!empty($currentProfile['avatar']) && $currentProfile['avatar'] !== $avatarPath) {
            $oldAvatarPath = FCPATH . 'uploads/avatars/' . $currentProfile['avatar'];
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }
        
        $_SESSION['success_message'] = __('settings.avatar_updated');
        return true;
    }
    
    protected function removeAvatar()
    {
        $userId = $this->currentUser['id'];
        
        // Haal het huidige profiel op
        $currentProfile = $this->getUserProfile($userId);
        
        if (!empty($currentProfile['avatar'])) {
            // Verwijder het bestand
            $avatarPath = FCPATH . 'uploads/avatars/' . $currentProfile['avatar'];
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
            
            // Update de database
            $query = "UPDATE user_profiles SET avatar = '', updated_at = NOW() WHERE user_id = ?";
            $this->db->query($query, [$userId]);
            
            $_SESSION['success_message'] = __('settings.avatar_removed');
        }
        
        return true;
    }
}