<?php
/**
 * Security Settings Template
 * Rood kleurenschema voor beveiligingsinstellingen
 */

// Include messages partial voor success/error berichten
include __DIR__ . '/../partials/messages.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-lg mb-8 p-8 text-white">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">🔒 Beveiligingsinstellingen</h1>
                    <p class="text-red-100 mt-2">Beheer je account beveiliging en privacy instellingen</p>
                </div>
            </div>
        </div>

        <form action="/?route=security/update" method="POST" class="space-y-8">
            
            <!-- Wachtwoord Wijzigen -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-800 flex items-center">
                        <span class="mr-2">🔑</span> Wachtwoord Wijzigen
                    </h2>
                    <p class="text-red-600 text-sm mt-1">Verander je wachtwoord voor extra beveiliging</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Huidig wachtwoord
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="Voer je huidige wachtwoord in">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Nieuw wachtwoord
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="Minimaal 8 karakters">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Bevestig nieuw wachtwoord
                            </label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="Herhaal je nieuwe wachtwoord">
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-md p-3">
                        <p class="text-sm text-red-700">
                            <strong>Tip:</strong> Gebruik een sterk wachtwoord met minimaal 8 karakters, inclusief letters, cijfers en speciale tekens.
                        </p>
                    </div>
                </div>
            </div>

            <!-- E-mail Notificaties -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-800 flex items-center">
                        <span class="mr-2">📧</span> E-mail Notificaties
                    </h2>
                    <p class="text-red-600 text-sm mt-1">Stel in wanneer je e-mail notificaties wilt ontvangen</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Login waarschuwingen</h3>
                            <p class="text-sm text-gray-600">Ontvang een e-mail bij elke nieuwe login</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_login_alerts" 
                                   value="1"
                                   class="sr-only peer"
                                   <?= ($security_settings['email_login_alerts'] ?? 1) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Wachtwoord wijzigingen</h3>
                            <p class="text-sm text-gray-600">Ontvang een e-mail bij wachtwoord wijzigingen</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_password_changes" 
                                   value="1"
                                   class="sr-only peer"
                                   <?= ($security_settings['email_password_changes'] ?? 1) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Beveiligingsmeldingen</h3>
                            <p class="text-sm text-gray-600">Ontvang e-mails over verdachte activiteiten</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_security_alerts" 
                                   value="1"
                                   class="sr-only peer"
                                   <?= ($security_settings['email_security_alerts'] ?? 1) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Account Herstel -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-800 flex items-center">
                        <span class="mr-2">🔄</span> Account Herstel
                    </h2>
                    <p class="text-red-600 text-sm mt-1">Stel herstel opties in voor als je je wachtwoord vergeet</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="recovery_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Herstel e-mailadres
                        </label>
                        <input type="email" 
                               id="recovery_email" 
                               name="recovery_email"
                               value="<?= htmlspecialchars($security_settings['recovery_email'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="alternatief@email.com">
                        <p class="text-sm text-gray-500 mt-1">Een alternatief e-mailadres voor wachtwoord herstel</p>
                    </div>
                    
                    <div>
                        <label for="recovery_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Herstel telefoonnummer
                        </label>
                        <input type="tel" 
                               id="recovery_phone" 
                               name="recovery_phone"
                               value="<?= htmlspecialchars($security_settings['recovery_phone'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="+31 6 12345678">
                        <p class="text-sm text-gray-500 mt-1">Je mobiele nummer voor SMS verificatie</p>
                    </div>
                </div>
            </div>

            <!-- Geavanceerde Beveiliging -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-800 flex items-center">
                        <span class="mr-2">🛡️</span> Geavanceerde Beveiliging
                    </h2>
                    <p class="text-red-600 text-sm mt-1">Extra beveiligingslagen voor je account</p>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Twee-factor authenticatie (2FA)</h3>
                            <p class="text-sm text-gray-600">Voeg een extra beveiligingslaag toe met je telefoon</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                Binnenkort beschikbaar
                            </span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer opacity-50">
                            <input type="checkbox" 
                                   name="enable_2fa" 
                                   value="1"
                                   disabled
                                   class="sr-only peer"
                                   <?= ($security_settings['enable_2fa'] ?? 0) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center bg-white rounded-lg shadow-md p-6">
                <a href="/?route=profile/<?= $user['username'] ?>" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Terug naar profiel
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Instellingen Opslaan
                </button>
            </div>
        </form>

        <!-- Security Tips -->
        <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-red-800 mb-4 flex items-center">
                <span class="mr-2">💡</span> Beveiligingstips
            </h3>
            <ul class="space-y-2 text-sm text-red-700">
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Gebruik een uniek wachtwoord dat je nergens anders gebruikt</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Verander je wachtwoord regelmatig, vooral na verdachte activiteit</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Houd je herstel e-mailadres en telefoonnummer up-to-date</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Log altijd uit op gedeelde computers en apparaten</span>
                </li>
            </ul>
        </div>
    </div>
</div>