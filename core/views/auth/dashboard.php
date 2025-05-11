<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2>Welkom <?= htmlspecialchars($user['username']) ?>!</h2>

<p>Je bent succesvol ingelogd op SocialCore 🎉</p>

<p><a href="/logout">Uitloggen</a></p>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
