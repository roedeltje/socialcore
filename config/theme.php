<?php
/**
 * Theme Configuration
 * Dit bestand bevat alle thema-gerelateerde configuratie voor SocialCore
 */

return [
    // Actief thema - dit is het thema dat momenteel wordt gebruikt
    'active_theme' => 'default',
    
    // Pad naar de thema-directory relatief aan de projectroot
    'themes_directory' => 'themes',
    
    // Fallback thema als het actieve thema ontbreekt of beschadigd is
    'fallback_theme' => 'default',
    
    // Of thema-overschrijvingen toegestaan zijn (belangrijk voor plugins/extensies)
    'allow_overrides' => true,
    
    // Verplichte bestanden die elk thema moet hebben
    'required_files' => [
        'layouts/header.php',
        'layouts/footer.php',
        'pages/home.php',
        'theme.json'
    ],
    
    // Componenten caching (kan performance verbeteren bij productie)
    'cache_enabled' => false,
    
    // Optionele thema-specifieke instellingen
    'settings' => [
        // Hier kunnen thema-specifieke configuraties komen, bijvoorbeeld:
        'show_author_avatar' => true,
        'posts_per_page' => 10,
        'enable_dark_mode' => false
    ]
];