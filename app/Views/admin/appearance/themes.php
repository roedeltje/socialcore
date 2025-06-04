<div class="admin-content-wrapper">
    <div class="page-header">
        <h1>Thema's beheren</h1>
        <p>Beheer het uiterlijk van je SocialCore site door thema's te activeren, installeren of aanpassen.</p>
    </div>

    <!-- Success/Error berichten -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Actie knoppen -->
    <div class="page-actions">
        <a href="<?= base_url('?route=admin/appearance/install-theme') ?>" class="button">
            ➕ Nieuw thema installeren
        </a>
        <a href="<?= base_url('?route=admin/appearance/customize') ?>" class="button button-secondary">
            🎨 Actief thema aanpassen
        </a>
    </div>

    <!-- Thema's grid -->
    <div class="themes-grid">
        <?php if (empty($themes)): ?>
            <div class="no-themes">
                <h3>Geen thema's gevonden</h3>
                <p>Er zijn momenteel geen thema's geïnstalleerd.</p>
            </div>
        <?php else: ?>
            <?php foreach ($themes as $themeSlug => $theme): ?>
                <div class="theme-card <?= $theme['is_active'] ? 'active-theme' : '' ?>">
                    <!-- Screenshot -->
                    <div class="theme-screenshot">
                        <div class="theme-screenshot-placeholder">🎨</div>
                        
                        <?php if ($theme['is_active']): ?>
                            <div class="active-badge">
                                ✅ Actief
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Thema informatie -->
                    <div class="theme-info">
                        <h3 class="theme-title"><?= htmlspecialchars($theme['name']) ?></h3>
                        <p class="theme-description"><?= htmlspecialchars($theme['description'] ?? 'Geen beschrijving beschikbaar') ?></p>
                        
                        <div class="theme-meta">
                            <span class="theme-version">Versie <?= htmlspecialchars($theme['version'] ?? '1.0.0') ?></span>
                            <?php if (!empty($theme['author'])): ?>
                                <span class="theme-author">
                                    Door <?= htmlspecialchars($theme['author']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Thema features -->
                        <?php if (!empty($theme['supports'])): ?>
                            <div class="theme-supports">
                                <?php foreach ($theme['supports'] as $feature => $supported): ?>
                                    <?php if ($supported): ?>
                                        <span class="support-tag">
                                            ✅ <?= ucfirst($feature) ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acties -->
                    <div class="theme-actions">
                        <?php if ($theme['is_active']): ?>
                            <span class="button button-active disabled">
                                ✅ Actief thema
                            </span>
                            <a href="<?= base_url('?route=admin/appearance/customize') ?>" class="button button-secondary">
                                ⚙️ Aanpassen
                            </a>
                        <?php else: ?>
                            <form method="POST" action="<?= base_url('?route=admin/appearance/activate-theme') ?>" style="display: inline;">
                                <input type="hidden" name="theme" value="<?= htmlspecialchars($themeSlug) ?>">
                                <button type="submit" class="button" onclick="return confirm('Weet je zeker dat je dit thema wilt activeren?')">
                                    🔄 Activeren
                                </button>
                            </form>
                            
                            <a href="<?= base_url('?route=home&preview_theme=' . urlencode($themeSlug)) ?>" class="button button-secondary" target="_blank">
                                👁️ Preview
                            </a>
                            
                            <?php if ($themeSlug !== 'default'): ?>
                                <form method="POST" action="<?= base_url('?route=admin/appearance/delete-theme') ?>" style="display: inline;">
                                    <input type="hidden" name="theme" value="<?= htmlspecialchars($themeSlug) ?>">
                                    <button type="submit" class="button button-danger" onclick="return confirm('Weet je zeker dat je dit thema wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')">
                                        🗑️ Verwijderen
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer info -->
    <div class="themes-footer">
        <p>
            <strong>Tip:</strong> Je kunt nieuwe thema's installeren door een ZIP-bestand te uploaden, 
            of door thema's te downloaden van de SocialCore community.
        </p>
    </div>
</div>