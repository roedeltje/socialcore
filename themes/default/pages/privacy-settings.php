<?php
/**
 * Privacy Settings Template
 * /themes/default/pages/privacy-settings.php
 * WordPress-style thema template die eruitziet als Messages
 */

$pageTitle = $data['title'] ?? 'Privacy Instellingen';
$privacySettings = $data['privacySettings'] ?? [];
$success = $data['success'] ?? null;
$error = $data['error'] ?? null;
?>

<div class="privacy-settings-page">
    <!-- Header zoals Messages maar dan blauw voor Privacy -->
    <div class="page-header bg-blue-100 border-b-4 border-blue-400 rounded-t-lg p-4">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-blue-800 flex items-center gap-3">
                    <i class="fas fa-shield-alt"></i>
                    Privacy Instellingen
                </h1>
                <p class="text-blue-600">
                    Beheer wie jouw informatie en activiteiten kan zien
                </p>
            </div>
            
            <div class="flex gap-2">
                <a href="/?route=profile" class="hyves-button bg-blue-500 hover:bg-blue-600">
                    <i class="fas fa-arrow-left"></i>
                    Terug naar Profiel
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div class="hyves-alert hyves-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="hyves-alert hyves-alert-error mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Content Box zoals Messages -->
    <div class="privacy-content bg-white rounded-b-lg shadow-md">
        <form method="POST" action="/?route=privacy/update" class="privacy-form">
            
            <!-- Profiel Zichtbaarheid -->
            <div class="privacy-section border-b border-gray-200 p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Profiel Zichtbaarheid
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal wie jouw profielpagina kan bekijken
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-3 block">
                        Wie kan mijn profiel bekijken?
                    </label>
                    <div class="radio-group space-y-3">
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="profile_visibility" value="public" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'public' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Iedereen</div>
                                <div class="text-sm text-gray-600">Alle bezoekers kunnen je profiel zien</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="profile_visibility" value="friends" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'friends' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Alleen vrienden</div>
                                <div class="text-sm text-gray-600">Alleen geaccepteerde vrienden kunnen je profiel zien</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="profile_visibility" value="private" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'private' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Alleen ik</div>
                                <div class="text-sm text-gray-600">Niemand anders kan je profiel bekijken</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Foto's Zichtbaarheid -->
            <div class="privacy-section border-b border-gray-200 p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-images text-blue-600"></i>
                        Foto's en Media
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal wie jouw foto's en media kan zien
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-3 block">
                        Wie kan mijn foto's bekijken?
                    </label>
                    <div class="radio-group space-y-3">
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="photos_visibility" value="public" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'public' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Iedereen</div>
                                <div class="text-sm text-gray-600">Alle bezoekers kunnen je foto's zien</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="photos_visibility" value="friends" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'friends' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Alleen vrienden</div>
                                <div class="text-sm text-gray-600">Alleen geaccepteerde vrienden kunnen je foto's zien</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="photos_visibility" value="private" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'private' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Alleen ik</div>
                                <div class="text-sm text-gray-600">Niemand anders kan je foto's bekijken</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Berichten en Communicatie -->
            <div class="privacy-section border-b border-gray-200 p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-600"></i>
                        Berichten en Communicatie
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal wie jou berichten kan sturen
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-3 block">
                        Wie kan mij berichten sturen?
                    </label>
                    <div class="radio-group space-y-3">
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="messages_from" value="everyone" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'everyone' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Iedereen</div>
                                <div class="text-sm text-gray-600">Alle gebruikers kunnen je berichten sturen</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="messages_from" value="friends" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'friends' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Alleen vrienden</div>
                                <div class="text-sm text-gray-600">Alleen geaccepteerde vrienden kunnen je berichten sturen</div>
                            </div>
                        </label>
                        
                        <label class="radio-option flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="messages_from" value="nobody" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'nobody' ? 'checked' : '' ?>
                                class="mt-1">
                            <div class="option-content">
                                <div class="font-medium text-gray-800">Niemand</div>
                                <div class="text-sm text-gray-600">Geen nieuwe berichten van andere gebruikers</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Contact Informatie -->
            <div class="privacy-section border-b border-gray-200 p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-address-book text-blue-600"></i>
                        Contact Informatie
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal wie jouw contactgegevens kan zien
                    </p>
                </div>
                
                <div class="setting-group mb-4">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-2 block">
                        Wie kan mijn e-mailadres zien?
                    </label>
                    <select name="show_email" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="public" <?= ($privacySettings['show_email'] ?? 'private') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['show_email'] ?? 'private') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['show_email'] ?? 'private') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>

                <div class="setting-group">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-2 block">
                        Wie kan mijn telefoonnummer zien?
                    </label>
                    <select name="show_phone" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="public" <?= ($privacySettings['show_phone'] ?? 'private') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['show_phone'] ?? 'private') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['show_phone'] ?? 'private') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>
            </div>

            <!-- Zichtbaarheid en Zoeken -->
            <div class="privacy-section border-b border-gray-200 p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-search text-blue-600"></i>
                        Zoeken en Zichtbaarheid
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal hoe anderen je kunnen vinden
                    </p>
                </div>
                
                <div class="checkbox-group space-y-4">
                    <label class="checkbox-option flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="searchable" value="1" 
                            <?= ($privacySettings['searchable'] ?? 1) ? 'checked' : '' ?>
                            class="mt-1">
                        <div class="option-content">
                            <div class="font-medium text-gray-800">Zoekbaar maken</div>
                            <div class="text-sm text-gray-600">Anderen kunnen je vinden via de zoekfunctie</div>
                        </div>
                    </label>
                    
                    <label class="checkbox-option flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="show_online_status" value="1" 
                            <?= ($privacySettings['show_online_status'] ?? 1) ? 'checked' : '' ?>
                            class="mt-1">
                        <div class="option-content">
                            <div class="font-medium text-gray-800">Online status tonen</div>
                            <div class="text-sm text-gray-600">Laat anderen zien wanneer je online bent</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Posts Zichtbaarheid -->
            <div class="privacy-section p-6">
                <div class="section-header mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-edit text-blue-600"></i>
                        Posts en Activiteit
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Bepaal wie jouw posts en activiteiten kan zien
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label text-sm font-medium text-gray-700 mb-2 block">
                        Wie kan mijn posts bekijken?
                    </label>
                    <select name="posts_visibility" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="public" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>
            </div>

            <!-- Save Button -->
            <div class="form-actions bg-gray-50 p-6 rounded-b-lg flex gap-3">
                <button type="submit" class="hyves-button bg-blue-500 hover:bg-blue-600 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Privacy Instellingen Opslaan
                </button>
                
                <a href="/?route=profile" class="hyves-button bg-gray-500 hover:bg-gray-600 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Annuleren
                </a>
            </div>

        </form>
    </div>
</div>