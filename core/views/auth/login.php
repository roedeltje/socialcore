<h2>Inloggen</h2>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="/login.php">
    <label>Gebruikersnaam of e-mail:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Inloggen</button>
</form>

<p>Nog geen account? <a href="/register.php">Registreer hier</a></p>
