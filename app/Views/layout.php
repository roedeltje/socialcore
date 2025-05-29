<?php
echo '<div style="background: red; color: white; padding: 10px; font-weight: bold;">DEBUG: Core layout.php wordt geladen!</div>';
?>

<?php
// app/Views/layout.php

// Variabelen die in de header/footer worden gebruikt zijn hier beschikbaar
// omdat we extract($data) hebben aangeroepen in de controller

// Header inladen
include __DIR__ . '/layout/header.php';

// Berichten weergeven (thema-onafhankelijk)
?>
<div class="messages-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success_message']) ?></span>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error_message']) ?></span>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>
<?php

// Hier voegen we de dynamische content toe
echo $content;

// Footer inladen
include __DIR__ . '/layout/footer.php';