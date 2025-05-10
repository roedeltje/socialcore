<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';

if (!Auth::check()) {
    redirect('/login'); // ✅ nette route
}

$user = Auth::user();
?>

<h2>Welkom <?= htmlspecialchars($user['username']) ?>!</h2>

<p>Je bent succesvol ingelogd op SocialCore 🎉</p>

<p><a href="/logout">Uitloggen</a></p>
