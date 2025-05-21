<?php
$availableLanguages = get_available_languages();
$currentLanguage = get_current_language();

// Verwerk taalaanpassing bij submit
if (isset($_POST['change_language']) && isset($_POST['language'])) {
    // Sla de taal op in een cookie voor 30 dagen
    set_language($_POST['language'], true, 30 * 24 * 60 * 60);
    
    // Redirect naar dezelfde pagina om de wijzigingen te tonen
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="language-switcher">
    <form method="post" action="" class="inline-form">
        <select name="language" onchange="this.form.submit()" class="lang-select">
            <?php foreach ($availableLanguages as $code => $name): ?>
                <option value="<?= $code ?>" <?= $code === $currentLanguage ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="change_language" value="1">
    </form>
</div>

<style>
.language-switcher {
    display: inline-block;
    margin: 0 10px;
}
.language-switcher .lang-select {
    border: 1px solid #ddd;
    padding: 4px 8px;
    border-radius: 4px;
    background-color: #fff;
}
.inline-form {
    display: inline;
}
</style>