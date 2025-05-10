<!-- Taalschakelaar Component -->
<div class="language-switcher">
    <form action="<?= base_url('set-language') ?>" method="POST" class="flex space-x-2">
        <label for="language-select" class="sr-only"><?= __('app.language') ?></label>
        <select id="language-select" name="locale" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                onchange="this.form.submit()">
            <?php foreach (get_available_languages() as $code => $name): ?>
                <option value="<?= $code ?>" <?= get_current_language() === $code ? 'selected' : '' ?>>
                    <?= $name ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="remember" value="1">
        <noscript>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                <?= __('app.save') ?>
            </button>
        </noscript>
    </form>
</div>