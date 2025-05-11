<h1><?= __('app.welcome') ?></h1>
<p><?= __('app.welcome_message', ['name' => 'SocialCore']) ?></p>

<p>
    <a href="/register" class="button">Gratis Registreren</a> of 
    <a href="/login">Inloggen</a> als je al een account hebt.
</p>

<?php include __DIR__ . '/../components/language_switcher.php'; ?>
