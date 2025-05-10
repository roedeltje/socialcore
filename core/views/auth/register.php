<h2>Registreren</h2>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="/register.php">
    <label>Gebruikersnaam:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Registreren</button>
</form>

<p>Heb je al een account? <a href="/login.php">Inloggen</a></p>
