<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2>Inloggen</h2>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="post" action="<?= base_url('auth/login') ?>">
    <label>Gebruikersnaam of e-mail:</label><br>
    <input type="text" name="username" required><br><br>
    
    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br><br>
    
    <button type="submit">Inloggen</button>
</form>

<p>Nog geen account? <a href="<?= base_url('register') ?>">Registreer hier</a></p>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>