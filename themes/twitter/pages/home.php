<?php
// // DEBUG: Thema informatie
// echo "<!-- DEBUG INFO: ";
// echo "Current theme: " . ($_SESSION['theme'] ?? 'not set') . " | ";
// echo "Is Twitter theme: " . (($_SESSION['theme'] ?? 'default') === 'twitter' ? 'YES' : 'NO') . " | ";
// echo "Route: " . ($_GET['route'] ?? 'home');
// echo " -->";
?>

<?php
// Twitter Theme - Home Page
// Controle of gebruiker is ingelogd
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = $isLoggedIn ? $_SESSION : null;
?>

<div class="twitter-container">
    <!-- Linker sidebar - Navigatie -->
    <aside class="twitter-sidebar">
        <!-- Logo -->
        <div class="twitter-logo-container">
            <svg class="twitter-logo" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </div>

        <!-- Navigatie menu -->
        <nav class="twitter-nav">
            <ul>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=home') ?>" class="twitter-nav-link active">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1.696L.622 8.807l1.06 1.696L3 9.679V19.5C3 20.881 4.119 22 5.5 22h13c1.381 0 2.5-1.119 2.5-2.5V9.679l1.318.824 1.06-1.696L12 1.696zM12 16.5c-1.933 0-3.5-1.567-3.5-3.5s1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5-1.567 3.5-3.5 3.5z"/>
                        </svg>
                        <span class="twitter-nav-text">Startpagina</span>
                    </a>
                </li>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=feed') ?>" class="twitter-nav-link">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.25 3.75c-3.59 0-6.5 2.91-6.5 6.5s2.91 6.5 6.5 6.5c1.795 0 3.419-.726 4.596-1.904 1.178-1.177 1.904-2.801 1.904-4.596 0-3.59-2.91-6.5-6.5-6.5z"/>
                        </svg>
                        <span class="twitter-nav-text">Verkennen</span>
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=notifications') ?>" class="twitter-nav-link">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.993 9.042C19.48 5.017 16.054 2 11.996 2s-7.49 3.021-7.999 7.051L2.866 18H7.1c.463 2.282 2.481 4 4.9 4s4.437-1.718 4.9-4h4.234l-1.141-8.958zM12 20c-1.306 0-2.417-.835-2.829-2h5.658c-.412 1.165-1.523 2-2.829 2z"/>
                        </svg>
                        <span class="twitter-nav-text">Meldingen</span>
                    </a>
                </li>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=messages') ?>" class="twitter-nav-link">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M1.998 5.5c0-1.381 1.119-2.5 2.5-2.5h15c1.381 0 2.5 1.119 2.5 2.5v13c0 1.381-1.119 2.5-2.5 2.5h-15c-1.381 0-2.5-1.119-2.5-2.5v-13zm2.5-.5c-.276 0-.5.224-.5.5v.511l8 6.032 8-6.032V5.5c0-.276-.224-.5-.5-.5h-15zm15.5 5.108l-8 6.032-8-6.032V18.5c0 .276.224.5.5.5h15c.276 0 .5-.224.5-.5v-8.392z"/>
                        </svg>
                        <span class="twitter-nav-text">Berichten</span>
                    </a>
                </li>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=profile/' . $currentUser['username']) ?>" class="twitter-nav-link">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.651 19h12.698c-.337-1.8-1.023-3.21-1.945-4.19C15.318 13.65 13.838 13 12 13s-3.317.65-4.404 1.81c-.922.98-1.608 2.39-1.945 4.19zm.486-5.56C7.627 11.85 9.648 11 12 11s4.373.85 5.863 2.44c1.477 1.58 2.366 3.8 2.632 6.46l.11 1.1H3.395l.11-1.1c.266-2.66 1.155-4.88 2.632-6.46zM12 4c-1.105 0-2 .9-2 2s.895 2 2 2 2-.9 2-2-.895-2-2-2zM8 6c0-2.21 1.791-4 4-4s4 1.79 4 4-1.791 4-4 4-4-1.79-4-4z"/>
                        </svg>
                        <span class="twitter-nav-text">Profiel</span>
                    </a>
                </li>
                <li class="twitter-nav-item">
                    <a href="<?= base_url('?route=settings') ?>" class="twitter-nav-link">
                        <svg class="twitter-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.54 1.75h2.92l1.57 2.36c.11.17.32.25.53.21l2.53-.59 2.17 2.17-.58 2.54c-.05.2.04.41.21.53l2.36 1.57v2.92l-2.36 1.57c-.17.12-.26.33-.21.53l.58 2.54-2.17 2.17-2.53-.59c-.21-.04-.42.04-.53.21l-1.57 2.36h-2.92l-1.58-2.36c-.11-.17-.32-.25-.52-.21l-2.54.59-2.17-2.17.58-2.54c.05-.2-.03-.41-.21-.53l-2.35-1.57v-2.92L4.1 8.97c.18-.12.26-.33.21-.53L3.73 5.9 5.9 3.73l2.54.59c.2.04.41-.04.52-.21l1.58-2.36zm1.07 7.25c-.966 0-1.75.784-1.75 1.75s.784 1.75 1.75 1.75 1.75-.784 1.75-1.75-.784-1.75-1.75-1.75z"/>
                        </svg>
                        <span class="twitter-nav-text">Meer</span>
                    </a>
                </li>
                </ul>
            </nav>

            <!-- Tweet button -->
            <button class="twitter-tweet-btn" onclick="window.location.href='<?= base_url('?route=feed') ?>'">
                Posten
            </button>

            <!-- User profile at bottom -->
            <div class="twitter-user-profile" style="margin-top: auto; padding-top: 20px;">
                <div style="display: flex; align-items: center; padding: 12px; border-radius: 9999px; cursor: pointer;" onmouseover="this.style.backgroundColor='rgba(15,20,25,0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                    <img src="<?= $currentUser['avatar'] ? base_url('uploads/avatars/' . $currentUser['avatar']) : base_url('public/assets/images/avatars/default-avatar.png') ?>" 
                         alt="Profiel" class="twitter-avatar">
                    <div style="margin-left: 12px;">
                        <div style="font-weight: 700; font-size: 15px;"><?= htmlspecialchars($currentUser['display_name'] ?? $currentUser['username']) ?></div>
                        <div style="color: var(--text-secondary); font-size: 15px;">@<?= htmlspecialchars($currentUser['username']) ?></div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            </ul>
        </nav>
        
        <!-- Login/Register buttons voor niet-ingelogde gebruikers -->
        <div class="twitter-auth-buttons" style="margin-top: auto; padding-top: 20px;">
            <a href="<?= base_url('?route=login') ?>" class="twitter-btn" style="display: block; text-align: center; text-decoration: none; margin-bottom: 12px;">
                Inloggen
            </a>
            <a href="<?= base_url('?route=register') ?>" class="twitter-btn-outline" style="display: block; text-align: center; text-decoration: none; padding: 8px 24px; border-radius: 9999px; border: 1px solid var(--border-dark);">
                Registreren
            </a>
        </div>
        <?php endif; ?>
    </aside>

    <!-- Hoofdcontent area -->
    <main class="twitter-main">
        <!-- Header -->
        <header class="twitter-header">
            <h1>Voor jou</h1>
        </header>

        <?php if ($isLoggedIn): ?>
        <!-- Tweet compose box -->
        <div class="twitter-post" style="border-bottom: 10px solid var(--bg-secondary);">
            <div style="display: flex; padding: 16px;">
                <img src="<?= $currentUser['avatar'] ? base_url('uploads/avatars/' . $currentUser['avatar']) : base_url('public/assets/images/avatars/default-avatar.png') ?>" 
                     alt="Jouw profiel" class="twitter-avatar">
                <div style="flex: 1; margin-left: 16px;">
                    <textarea class="twitter-textarea" placeholder="Wat houdt je bezig?" style="border: none; font-size: 20px; min-height: 80px;"></textarea>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px;">
                        <div style="display: flex; gap: 16px;">
                            <!-- Media buttons -->
                            <button type="button" style="background: none; border: none; color: var(--twitter-blue); cursor: pointer; padding: 8px; border-radius: 50%;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 5.5C3 4.119 4.119 3 5.5 3h13C19.881 3 21 4.119 21 5.5v13c0 1.381-1.119 2.5-2.5 2.5h-13C4.119 21 3 19.881 3 18.5v-13zM5.5 5c-.276 0-.5.224-.5.5v9.086l3-3 3 3 5-5 3 3V5.5c0-.276-.224-.5-.5-.5h-13zM19 15.414l-3-3-5 5-3-3-3 3V18.5c0 .276.224.5.5.5h13c.276 0 .5-.224.5-.5v-3.086zM9.75 7C8.784 7 8 7.784 8 8.75s.784 1.75 1.75 1.75 1.75-.784 1.75-1.75S10.716 7 9.75 7z"/>
                                </svg>
                            </button>
                            <button type="button" style="background: none; border: none; color: var(--twitter-blue); cursor: pointer; padding: 8px; border-radius: 50%;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 5.5C3 4.119 4.119 3 5.5 3h13C19.881 3 21 4.119 21 5.5v13c0 1.381-1.119 2.5-2.5 2.5h-13C4.119 21 3 19.881 3 18.5v-13zM5.5 5c-.276 0-.5.224-.5.5v13c0 .276.224.5.5.5h13c.276 0 .5-.224.5-.5v-13c0-.276-.224-.5-.5-.5h-13z"/>
                                </svg>
                            </button>
                        </div>
                        <button class="twitter-btn" style="padding: 8px 24px;" disabled>
                            Posten
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Timeline/Feed content -->
        <div class="twitter-timeline">
            <?php if (!$isLoggedIn): ?>
                <!-- Welcome content voor niet-ingelogde gebruikers -->
                <div class="twitter-post">
                    <div style="text-align: center; padding: 40px 20px;">
                        <h2 style="font-size: 31px; font-weight: 800; margin-bottom: 16px;">
                            Welkom bij SocialCore
                        </h2>
                        <p style="font-size: 17px; color: var(--text-secondary); margin-bottom: 32px; line-height: 1.5;">
                            Verbind met vrienden, deel je gedachten en ontdek wat er gebeurt in jouw wereld.
                        </p>
                        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                            <a href="<?= base_url('?route=register') ?>" class="twitter-btn" style="text-decoration: none;">
                                Account aanmaken
                            </a>
                            <a href="<?= base_url('?route=login') ?>" class="twitter-btn-outline" style="text-decoration: none;">
                                Inloggen
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Demo posts voor niet-ingelogde gebruikers -->
                <div class="twitter-post">
                    <div class="twitter-post-header">
                        <img src="<?= base_url('public/assets/images/avatars/default-avatar.png') ?>" alt="Demo gebruiker" class="twitter-avatar">
                        <div>
                            <div class="twitter-post-user">
                                <span class="twitter-username">SocialCore</span>
                                <span class="twitter-handle">@socialcore</span>
                                <span>·</span>
                                <span class="twitter-timestamp">2u</span>
                            </div>
                        </div>
                    </div>
                    <div class="twitter-post-content">
                        Welkom bij SocialCore! 🎉 Een moderne sociale platform gebouwd met liefde voor privacy en gebruikerservaring. #SocialCore #OpenSource
                    </div>
                    <div class="twitter-actions">
                        <button class="twitter-action-btn">
                            <svg class="twitter-action-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M1.751 10c0-4.42 3.584-8 8.005-8h4.366c4.49 0 8.129 3.64 8.129 8.13 0 2.96-1.607 5.68-4.196 7.11l-8.054 4.46v-3.69h-.067c-4.49.1-8.183-3.51-8.183-8.01zm8.005-6c-3.317 0-6.005 2.69-6.005 6 0 3.37 2.77 6.08 6.138 6.01l.351-.01h1.761v2.3l5.087-2.81c1.951-1.08 3.163-3.13 3.163-5.36 0-3.39-2.744-6.13-6.129-6.13H9.756z"/>
                            </svg>
                            <span>12</span>
                        </button>
                        <button class="twitter-action-btn">
                            <svg class="twitter-action-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4.5 3.88l4.432 4.14-1.364 1.46L5.5 7.55V16c0 1.1.896 2 2 2H13v2H7.5c-2.209 0-4-1.79-4-4V7.55L1.432 9.48.068 8.02 4.5 3.88zM16.5 6H11V4h5.5c2.209 0 4 1.79 4 4v8.45l2.068-1.93 1.364 1.46-4.432 4.14-4.432-4.14 1.364-1.46L18.5 16.45V8c0-1.1-.896-2-2-2z"/>
                            </svg>
                            <span>5</span>
                        </button>
                        <button class="twitter-action-btn">
                            <svg class="twitter-action-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"/>
                            </svg>
                            <span>42</span>
                        </button>
                        <button class="twitter-action-btn">
                            <svg class="twitter-action-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.59l5.7 5.7-1.41 1.42L13 6.41V16h-2V6.41l-3.3 3.3-1.41-1.42L12 2.59zM21 15l-.02 3.51c0 1.38-1.12 2.49-2.5 2.49H5.5C4.11 21 3 19.88 3 18.5V15h2v3.5c0 .28.22.5.5.5h12.98c.28 0 .5-.22.5-.5L19 15h2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Hier komen later de echte posts van gevolgde gebruikers -->
                <div class="twitter-post">
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
                        <p>Je timeline is leeg. Begin met het volgen van mensen om hun posts te zien!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Rechter sidebar - Widgets -->
    <aside class="twitter-widgets">
        <!-- Upgrade widget -->
        <div class="twitter-widget">
            <div class="twitter-widget-content">
                <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 8px;">
                    Upgraden naar Premium+
                </h3>
                <p style="color: var(--text-secondary); margin-bottom: 16px; font-size: 15px;">
                    Profiteer van extra voordelen, geen advertenties en de meeste voorrang bij antwoorden.
                </p>
                <button class="twitter-btn">
                    Upgraden naar Premium+
                </button>
            </div>
        </div>

        <!-- Trending widget -->
        <div class="twitter-widget">
            <div class="twitter-widget-header">
                Trending voor jou
            </div>
            <div class="twitter-widget-content" style="padding: 0;">
                <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); cursor: pointer;" onmouseover="this.style.backgroundColor='var(--bg-hover)'" onmouseout="this.style.backgroundColor='transparent'">
                    <div style="font-size: 13px; color: var(--text-secondary);">Trending in Nederland</div>
                    <div style="font-weight: 700; margin: 2px 0;">#SocialCore</div>
                    <div style="font-size: 13px; color: var(--text-secondary);">1.234 posts</div>
                </div>
                <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); cursor: pointer;" onmouseover="this.style.backgroundColor='var(--bg-hover)'" onmouseout="this.style.backgroundColor='transparent'">
                    <div style="font-size: 13px; color: var(--text-secondary);">Trending</div>
                    <div style="font-weight: 700; margin: 2px 0;">#OpenSource</div>
                    <div style="font-size: 13px; color: var(--text-secondary);">567 posts</div>
                </div>
                <div style="padding: 12px 16px; cursor: pointer;" onmouseover="this.style.backgroundColor='var(--bg-hover)'" onmouseout="this.style.backgroundColor='transparent'">
                    <div style="font-size: 13px; color: var(--text-secondary);">Trending in Tech</div>
                    <div style="font-weight: 700; margin: 2px 0;">#WebDevelopment</div>
                    <div style="font-size: 13px; color: var(--text-secondary);">890 posts</div>
                </div>
            </div>
        </div>

        <!-- Who to follow widget -->
        <div class="twitter-widget">
            <div class="twitter-widget-header">
                Wie te volgen
            </div>
            <div class="twitter-widget-content" style="padding: 0;">
                <div style="display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid var(--border-color);">
                    <img src="<?= base_url('public/assets/images/avatars/default-avatar.png') ?>" alt="Gebruiker" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 12px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 15px;">SocialCore Team</div>
                        <div style="color: var(--text-secondary); font-size: 15px;">@socialcore_team</div>
                    </div>
                    <button class="twitter-btn" style="padding: 6px 16px; font-size: 14px;">
                        Volgen
                    </button>
                </div>
                <div style="display: flex; align-items: center; padding: 12px 16px;">
                    <img src="<?= base_url('public/assets/images/avatars/default-avatar.png') ?>" alt="Gebruiker" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 12px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 15px;">Open Source NL</div>
                        <div style="color: var(--text-secondary); font-size: 15px;">@opensource_nl</div>
                    </div>
                    <button class="twitter-btn" style="padding: 6px 16px; font-size: 14px;">
                        Volgen
                    </button>
                </div>
            </div>
        </div>
    </aside>
</div>