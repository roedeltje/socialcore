<?php
// /app/Views/home/index.php

// Controleren of gebruiker al is ingelogd
$isLoggedIn = isset($_SESSION['user_id']) ?? false;

// Als gebruiker is ingelogd, doorverwijzen naar dashboard
if ($isLoggedIn) {
    header('Location: ' . base_url('dashboard'));
    exit;
}

// Titel van de pagina instellen voor in header
$pageTitle = "Welkom bij SocialCore";
?>

<!-- Header laden vanuit thema -->
<?php include_once THEME_PATH . '/layouts/header.php'; ?>

<div class="home-container">
    <!-- Hier komt de inhoud van de startpagina -->
    <div class="home-welcome-section">
        <!-- Welkomsttekst en intro -->
    </div>
    
    <div class="home-login-section">
        <!-- Inlogformulier -->
    </div>
</div>

<!-- Footer laden vanuit thema -->
<?php include_once THEME_PATH . '/layouts/footer.php'; ?>

<div class="home-welcome-section">
    <div class="welcome-content">
        <h2>Welkom bij SocialCore</h2>
        <h1>Maak nieuwe vrienden, deel je herinneringen en ontdek content</h1>
        <p>Het open source sociale platform gebouwd in Nederland</p>
        
        <div class="welcome-image">
            <img src="<?= base_url('public/assets/images/friends-group.jpg') ?>" alt="Vrienden delen herinneringen">
        </div>
        
        <div class="welcome-cta">
            <a href="<?= base_url('register') ?>" class="btn btn-primary">Registreer nu</a>
            <a href="#about" class="btn btn-outline">Meer informatie</a>
        </div>
    </div>
</div>

<div class="home-login-section">
    <div class="login-container">
        <h2>Log in op je account</h2>
        <p>Welkom terug! Vul je gegevens in</p>
        
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['login_error'] ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('login/authenticate') ?>" method="post">
            <div class="form-group">
                <label for="email">
                    <i class="icon-email"></i>
                </label>
                <input type="text" id="email" name="email" placeholder="E-mailadres of Gebruikersnaam" required>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="icon-lock"></i>
                </label>
                <input type="password" id="password" name="password" placeholder="Wachtwoord" required>
            </div>
            
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Onthouden</label>
                <a href="<?= base_url('password/reset') ?>" class="forgot-link">Wachtwoord vergeten?</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
        </form>
        
        <div class="login-footer">
            <p>Heb je geen account? <a href="<?= base_url('register') ?>">Creëer je account</a></p>
        </div>
    </div>
</div>