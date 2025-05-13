<h1><?= __('app.welcome') ?></h1>
<p><?= __('app.welcome_message', ['name' => 'SocialCore']) ?></p>

<p>
    <?php if (!is_logged_in()): ?>
    <!-- Hier staan je registratie- en inlogknoppen -->
    <a href="<?= base_url('register') ?>" class="btn btn-primary">Registreren</a>
    <a href="<?= base_url('login') ?>" class="btn btn-secondary">Inloggen</a>
<?php endif; ?>
</p>

<?php include __DIR__ . '/../components/language_switcher.php'; ?>
