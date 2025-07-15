<!-- /app/Views/admin/settings/social.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Sociale Functies</h1>
        <p>Configureer welke sociale functies beschikbaar zijn op je platform.</p>
        <div class="page-actions">
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Instellingen
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="admin-form settings-form">
        <div class="settings-section">
            <h3><i class="fas fa-user-friends"></i> Vriendschap & Connecties</h3>
            <p class="section-description">Instellingen voor hoe gebruikers met elkaar kunnen verbinden.</p>
            
            <div class="social-features-grid">
                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_friend_requests" 
                           name="enable_friend_requests" 
                           class="form-check-input" 
                           <?= $settings['enable_friend_requests'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_friend_requests" class="form-check-label">
                        <strong>Vriendschapsverzoeken</strong>
                        <br><small>Sta gebruikers toe om vriendschapsverzoeken te verzenden en accepteren</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_messaging" 
                           name="enable_messaging" 
                           class="form-check-input" 
                           <?= $settings['enable_messaging'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_messaging" class="form-check-label">
                        <strong>Privéberichten</strong>
                        <br><small>Gebruikers kunnen privéberichten naar elkaar sturen</small>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="max_friends_limit">Maximum Aantal Vrienden</label>
                <input type="number" 
                       id="max_friends_limit" 
                       name="max_friends_limit" 
                       class="form-control" 
                       value="<?= $settings['max_friends_limit'] ?>"
                       min="1" 
                       max="10000" 
                       step="1">
                <small class="form-hint">0 betekent geen limiet. Aanbevolen: 1000-5000 voor prestaties</small>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-comments"></i> Chat Functies</h3>
            <p class="section-description">Configureer het chat systeem en gerelateerde functies.</p>
            
            <div class="form-group">
                <label for="chat_mode">Chat Systeem Modus</label>
                <select id="chat_mode" name="chat_mode" class="form-control">
                    <option value="auto" <?= $settings['chat_mode'] === 'auto' ? 'selected' : '' ?>>
                        Automatisch - Gebruik thema chat als beschikbaar, anders core chat
                    </option>
                    <option value="force_core" <?= $settings['chat_mode'] === 'force_core' ? 'selected' : '' ?>>
                        Altijd Core Chat - Gebruik altijd de standaard chat interface
                    </option>
                    <option value="force_theme" <?= $settings['chat_mode'] === 'force_theme' ? 'selected' : '' ?>>
                        Altijd Thema Chat - Gebruik altijd de thema-specifieke chat
                    </option>
                </select>
                <small class="form-hint">
                    Bepaalt welke chat interface wordt gebruikt. 'Automatisch' is aanbevolen voor meeste sites.
                </small>
            </div>
            
            <div class="social-features-grid">
                <div class="form-check">
                    <input type="checkbox" 
                           id="chat_features_emoji" 
                           name="chat_features_emoji" 
                           class="form-check-input" 
                           <?= $settings['chat_features_emoji'] === '1' ? 'checked' : '' ?>>
                    <label for="chat_features_emoji" class="form-check-label">
                        <strong>Emoji Picker</strong>
                        <br><small>Gebruikers kunnen emoji's toevoegen aan hun berichten</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="chat_features_file_upload" 
                           name="chat_features_file_upload" 
                           class="form-check-input" 
                           <?= $settings['chat_features_file_upload'] === '1' ? 'checked' : '' ?>>
                    <label for="chat_features_file_upload" class="form-check-label">
                        <strong>Bestanden Uploaden</strong>
                        <br><small>Gebruikers kunnen afbeeldingen en bestanden delen in chat</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="chat_features_real_time" 
                           name="chat_features_real_time" 
                           class="form-check-input" 
                           <?= $settings['chat_features_real_time'] === '1' ? 'checked' : '' ?>>
                    <label for="chat_features_real_time" class="form-check-label">
                        <strong>Real-time Updates</strong>
                        <br><small>Berichten verschijnen direct zonder pagina verversing (experimenteel)</small>
                    </label>
                </div>
            </div>

            <div class="chat-limits-grid">
                <div class="form-group">
                    <label for="chat_max_message_length">Maximale Berichtlengte</label>
                    <input type="number" 
                           id="chat_max_message_length" 
                           name="chat_max_message_length" 
                           class="form-control" 
                           value="<?= $settings['chat_max_message_length'] ?>"
                           min="100" 
                           max="5000" 
                           step="50">
                    <small class="form-hint">Aantal karakters (aanbevolen: 1000)</small>
                </div>

                <div class="form-group">
                    <label for="chat_max_file_size">Maximale Bestandsgrootte</label>
                    <input type="number" 
                           id="chat_max_file_size" 
                           name="chat_max_file_size" 
                           class="form-control" 
                           value="<?= $settings['chat_max_file_size'] ?>"
                           min="512" 
                           max="10240" 
                           step="256">
                    <small class="form-hint">In KB (aanbevolen: 2048 = 2MB)</small>
                </div>

                <div class="form-group">
                    <label for="chat_online_timeout">Online Status Timeout</label>
                    <input type="number" 
                           id="chat_online_timeout" 
                           name="chat_online_timeout" 
                           class="form-control" 
                           value="<?= $settings['chat_online_timeout'] ?>"
                           min="5" 
                           max="60" 
                           step="5">
                    <small class="form-hint">Minuten voordat gebruiker als offline wordt beschouwd</small>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-heart"></i> Interactie Functies</h3>
            <p class="section-description">Configureer hoe gebruikers kunnen reageren op content.</p>
            
            <div class="social-features-grid">
                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_likes" 
                           name="enable_likes" 
                           class="form-check-input" 
                           <?= $settings['enable_likes'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_likes" class="form-check-label">
                        <strong>Likes/Respect</strong>
                        <br><small>Gebruikers kunnen berichten en reacties liken</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_comments" 
                           name="enable_comments" 
                           class="form-check-input" 
                           <?= $settings['enable_comments'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_comments" class="form-check-label">
                        <strong>Reacties</strong>
                        <br><small>Gebruikers kunnen reacties plaatsen onder berichten</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_sharing" 
                           name="enable_sharing" 
                           class="form-check-input" 
                           <?= $settings['enable_sharing'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_sharing" class="form-check-label">
                        <strong>Delen van Berichten</strong>
                        <br><small>Gebruikers kunnen berichten delen op hun eigen tijdlijn</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_notifications" 
                           name="enable_notifications" 
                           class="form-check-input" 
                           <?= $settings['enable_notifications'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_notifications" class="form-check-label">
                        <strong>Notificaties</strong>
                        <br><small>Gebruikers ontvangen notificaties voor interacties</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-layer-group"></i> Community Functies</h3>
            <p class="section-description">Geavanceerde sociale functies voor community building.</p>
            
            <div class="social-features-grid">
                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_groups" 
                           name="enable_groups" 
                           class="form-check-input" 
                           <?= $settings['enable_groups'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_groups" class="form-check-label">
                        <strong>Groepen</strong>
                        <br><small>Gebruikers kunnen groepen aanmaken en beheren (Toekomstige functie)</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_events" 
                           name="enable_events" 
                           class="form-check-input" 
                           <?= $settings['enable_events'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_events" class="form-check-label">
                        <strong>Evenementen</strong>
                        <br><small>Gebruikers kunnen evenementen organiseren en bijwonen (Toekomstige functie)</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-shield-alt"></i> Content Moderatie</h3>
            <p class="section-description">Instellingen voor het modereren van gebruikerscontent.</p>
            
            <div class="social-features-grid">
                <div class="form-check">
                    <input type="checkbox" 
                           id="content_moderation_enabled" 
                           name="content_moderation_enabled" 
                           class="form-check-input" 
                           <?= $settings['content_moderation_enabled'] === '1' ? 'checked' : '' ?>>
                    <label for="content_moderation_enabled" class="form-check-label">
                        <strong>Content Moderatie</strong>
                        <br><small>Schakel moderatie tools in voor beheerders</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="auto_approve_posts" 
                           name="auto_approve_posts" 
                           class="form-check-input" 
                           <?= $settings['auto_approve_posts'] === '1' ? 'checked' : '' ?>>
                    <label for="auto_approve_posts" class="form-check-label">
                        <strong>Berichten Automatisch Goedkeuren</strong>
                        <br><small>Nieuwe berichten worden direct gepubliceerd zonder moderatie</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-chart-bar"></i> Sociale Statistieken</h3>
            <p class="section-description">Overzicht van sociale activiteit op je platform.</p>
            
            <div class="social-stats">
                <div class="stats-overview">
                    <div class="stat-block">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Vriendschappen</span>
                        </div>
                    </div>

                    <div class="stat-block">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Likes Totaal</span>
                        </div>
                    </div>

                    <div class="stat-block">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Reacties</span>
                        </div>
                    </div>

                    <div class="stat-block">
                        <div class="stat-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Notificaties</span>
                        </div>
                    </div>
                </div>

                <div class="engagement-tips">
                    <h4><i class="fas fa-lightbulb"></i> Tips voor Meer Engagement</h4>
                    <ul class="engagement-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Moedig gebruikers aan om complete profielen aan te maken</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Organiseer regelmatig community events of discussies</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Beloon actieve gebruikers met badges of erkenning</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Deel interessante content om discussie te stimuleren</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Sociale Instellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
        </div>
    </form>
</div>

<style>
.social-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.social-stats {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 20px;
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-block {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3em;
}

.stat-info {
    flex: 1;
}

.stat-number {
    display: block;
    font-size: 1.8em;
    font-weight: 700;
    color: var(--text-color);
    line-height: 1;
}

.stat-label {
    display: block;
    font-size: 0.9em;
    color: var(--text-muted);
    margin-top: 4px;
}

.engagement-tips {
    border-top: 1px solid var(--border-color);
    padding-top: 20px;
}

.engagement-tips h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    font-size: 1em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.engagement-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.engagement-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    margin-bottom: 8px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.engagement-list li i {
    color: var(--success-color);
    margin-top: 2px;
    flex-shrink: 0;
}

.engagement-list li span {
    color: var(--text-color);
    line-height: 1.4;
}

.chat-limits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.chat-limits-grid .form-group {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .chat-limits-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .social-features-grid, .stats-overview {
        grid-template-columns: 1fr;
    }
    
    .stat-block {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}
</style>