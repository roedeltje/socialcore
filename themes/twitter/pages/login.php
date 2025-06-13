<?php
// Twitter Theme - Login Page
?>

<div class="twitter-auth-container" style="min-height: 100vh; display: flex; background-color: var(--bg-primary);">
    <!-- Linker helft - Hero sectie -->
    <div class="twitter-auth-hero" style="flex: 1; background-color: var(--bg-primary); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
        <div style="text-align: center; max-width: 380px; padding: 40px;">
            <!-- Groot Twitter-stijl logo -->
            <svg style="width: 300px; height: 245px; margin-bottom: 48px; color: var(--twitter-blue);" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            
            <h1 style="font-size: 64px; font-weight: 800; margin-bottom: 32px; color: var(--text-primary); line-height: 1.1;">
                Er gebeurt nu iets
            </h1>
            
            <h2 style="font-size: 31px; font-weight: 400; margin-bottom: 40px; color: var(--text-primary);">
                Word vandaag nog lid van SocialCore.
            </h2>
        </div>
    </div>

    <!-- Rechter helft - Login formulier -->
    <div class="twitter-auth-form" style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px;">
        <div style="width: 100%; max-width: 440px;">
            <!-- Logo bovenaan formulier -->
            <svg style="width: 40px; height: 40px; margin-bottom: 36px; color: var(--text-primary);" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>

            <h2 style="font-size: 31px; font-weight: 800; margin-bottom: 32px; color: var(--text-primary);">
                Inloggen bij SocialCore
            </h2>

            <!-- Error berichten -->
            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 15px;">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Success berichten -->
            <?php if (isset($_SESSION['success'])): ?>
                <div style="background-color: #efe; border: 1px solid #cfc; color: #363; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 15px;">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Login formulier -->
            <form method="POST" action="<?= base_url('?route=auth/login') ?>" style="width: 100%;">
                <!-- Email/Username veld -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 15px; font-weight: 400; color: var(--text-primary); margin-bottom: 8px;">
                        E-mail of gebruikersnaam
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        required
                        style="width: 100%; padding: 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 17px; background-color: var(--bg-primary); color: var(--text-primary); transition: border-color 0.2s ease;"
                        placeholder="E-mail of gebruikersnaam"
                        onfocus="this.style.borderColor='var(--twitter-blue)'; this.style.boxShadow='0 0 0 1px var(--twitter-blue)'"
                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                    >
                </div>

                <!-- Wachtwoord veld -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 15px; font-weight: 400; color: var(--text-primary); margin-bottom: 8px;">
                        Wachtwoord
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        required
                        style="width: 100%; padding: 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 17px; background-color: var(--bg-primary); color: var(--text-primary); transition: border-color 0.2s ease;"
                        placeholder="Wachtwoord"
                        onfocus="this.style.borderColor='var(--twitter-blue)'; this.style.boxShadow='0 0 0 1px var(--twitter-blue)'"
                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                    >
                </div>

                <!-- Remember me checkbox -->
                <div style="margin-bottom: 32px; display: flex; align-items: center;">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        style="width: 18px; height: 18px; margin-right: 12px; accent-color: var(--twitter-blue);"
                    >
                    <label for="remember" style="font-size: 15px; color: var(--text-primary); cursor: pointer;">
                        Ingelogd blijven
                    </label>
                </div>

                <!-- Login button -->
                <button 
                    type="submit" 
                    class="twitter-btn"
                    style="width: 100%; padding: 16px; font-size: 17px; margin-bottom: 20px;"
                >
                    Inloggen
                </button>

                <!-- Wachtwoord vergeten link -->
                <div style="text-align: center; margin-bottom: 40px;">
                    <a href="<?= base_url('?route=auth/forgot-password') ?>" 
                       style="color: var(--twitter-blue); text-decoration: none; font-size: 15px;">
                        Wachtwoord vergeten?
                    </a>
                </div>

                <!-- Divider -->
                <div style="position: relative; margin: 40px 0; text-align: center;">
                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0;">
                    <span style="background-color: var(--bg-primary); padding: 0 20px; color: var(--text-secondary); font-size: 15px; position: absolute; top: -10px; left: 50%; transform: translateX(-50%);">
                        of
                    </span>
                </div>

                <!-- Registreren sectie -->
                <div style="text-align: center;">
                    <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 15px;">
                        Heb je nog geen account?
                    </p>
                    <a href="<?= base_url('?route=auth/register') ?>" 
                       class="twitter-btn-outline"
                       style="display: inline-block; text-decoration: none; padding: 12px 24px; font-size: 15px; font-weight: 700;">
                        Account aanmaken
                    </a>
                </div>
            </form>

            <!-- Terug naar home link -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?= base_url() ?>" 
                   style="color: var(--twitter-blue); text-decoration: none; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.414 13l5.043 5.04-1.414 1.42L3.586 12l7.457-7.46 1.414 1.42L7.414 11H21v2H7.414z"/>
                    </svg>
                    Terug naar home
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Responsive styling -->
<style>
@media (max-width: 1024px) {
    .twitter-auth-hero {
        display: none !important;
    }
    
    .twitter-auth-form {
        flex: none !important;
        width: 100% !important;
    }
}

@media (max-width: 480px) {
    .twitter-auth-form {
        padding: 20px !important;
    }
    
    .twitter-auth-form h2 {
        font-size: 24px !important;
    }
}
</style>