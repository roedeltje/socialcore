<?php

namespace App\Controllers;

class ProfileController extends Controller
{
    /**
     * Toon de profielpagina
     */
    public function index()
    {
        // Later kun je hier de gebruikersgegevens ophalen op basis van ID
        // Voor nu houden we het simpel
        $this->view('profile/index');
    }

    public function updateAvatar() {
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
}