<?php /* Socialcore default thema registratie pagina */
?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 py-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Registreren</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= base_url('register/process') ?>">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Kies een gebruikersnaam" 
                        pattern="[a-zA-Z0-9_-]+"
                        minlength="3" maxlength="30"
                        title="Alleen letters, cijfers, underscores en streepjes toegestaan"
                        required>
                    <small class="text-gray-500">3-30 tekens, alleen letters, cijfers, _ en -</small>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">E-mailadres</label>
                    <input type="email" id="email" name="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Voer je e-mailadres in" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Wachtwoord</label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Kies een wachtwoord" 
                        minlength="<?= $password_requirements['min_length'] ?? 8 ?>"
                        required>
                    
                    <!-- Password Requirements Display -->
                    <?php if (isset($password_requirements)): ?>
                    <div class="mt-2 text-xs text-gray-600">
                        <p class="font-medium mb-1">Wachtwoord vereisten:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Minimaal <?= $password_requirements['min_length'] ?> karakters</li>
                            <?php if ($password_requirements['require_uppercase']): ?>
                                <li>Minimaal één hoofdletter (A-Z)</li>
                            <?php endif; ?>
                            <?php if ($password_requirements['require_numbers']): ?>
                                <li>Minimaal één cijfer (0-9)</li>
                            <?php endif; ?>
                            <?php if ($password_requirements['require_special']): ?>
                                <li>Minimaal één speciaal teken (!@#$%^&*)</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label for="password_confirm" class="block text-gray-700 text-sm font-medium mb-2">Wachtwoord bevestigen</label>
                    <input type="password" id="password_confirm" name="password_confirm" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Herhaal je wachtwoord" required>
                    <small class="text-gray-500">Voer hetzelfde wachtwoord opnieuw in</small>
                </div>

                <?php if (isset($email_verification_required) && $email_verification_required): ?>
                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-600">
                                <strong>Email verificatie vereist:</strong> Na registratie moet je je email adres verifiëren voordat je kunt inloggen.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div>
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Registreren
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Heb je al een account? 
                    <a href="<?= base_url('login') ?>" class="font-medium text-blue-600 hover:underline">
                        Inloggen
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    function checkPasswordMatch() {
        if (passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Wachtwoorden komen niet overeen');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    passwordConfirm.addEventListener('input', checkPasswordMatch);
});
</script>