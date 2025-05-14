Suggesties voor thema bestanden voor het default thema van het Social Core Project

1 Gebruikersgerelateerd:

    login.php en register.php - Voor aanmeldings- en registratieformulieren
    user-list.php - Voor het weergeven van lijsten met gebruikers (vrienden, volgers)
    notifications.php - Voor het tonen van gebruikersnotificaties
    messages.php en conversation.php - Voor privéberichten en gesprekken


2 Content gerelateerd:

    search-results.php - Voor zoekresultaten
    trending.php - Voor trending content/hashtags
    explore.php - Voor het ontdekken van nieuwe content of gebruikers
    saved-posts.php - Voor opgeslagen/favoriete content


3 Community gerelateerd:

    group.php en group-list.php - Voor groepen (indien van toepassing)
    event.php en event-list.php - Voor evenementen (indien van toepassing)
    friends.php of connections.php - Voor vrienden/connecties4


4 Componenten:

    sidebar.php - Voor een zijbalk met widgets/modules
    widgets.php of een map widgets/ - Voor herbruikbare widgets
    components/ map met bestanden zoals post-card.php, user-card.php, etc.


5 Systeem templates:

    404.php - Voor niet-gevonden pagina's
    maintenance.php - Voor onderhoudsmodus
    settings.php - Voor gebruikersinstellingen


6 Thema-specifieke bestanden:

    theme.json of theme.php - Met metadata en thema-instellingen
    functions.php - Voor thema-specifieke functies

Op basis van het bovenstaande zal dit de structuur worden van het thema bestanden waar een kruisje x voor staat zijn al aangemaakt. Ik zal naargelang we vorderen de lijst aanpassen.

/socialcore/themes/default/
├── assets/
│   ├── css/
│   │   └── style.css x
│   ├── js/
│   │   └── theme.js x
│   └── images/
│       └── logo.png
├── components/
│   ├── post-card.php
│   ├── user-card.php
│   ├── comment.php
│   └── notification-item.php
|   └── language-switcher.php x
├── layouts/
│   ├── header.php x
│   ├── footer.php x
│   └── sidebar.php
├── pages/
│   ├── home.php x
│   ├── profile.php x
│   ├── timeline.php x
│   ├── edit-profile.php x
│   ├── login.php x
│   ├── register.php x
│   └── settings.php x
├── partials/
│   ├── navigation.php x
│   ├── search-form.php
│   └── user-menu.php
├── templates/
│   ├── single-post.php x
│   ├── user-posts.php x
│   ├── notifications.php
│   ├── messages.php x
│   └── search-results.php
├── functions.php x
└── theme.json x