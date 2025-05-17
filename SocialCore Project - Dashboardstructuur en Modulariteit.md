**SocialCore Project - Dashboardstructuur en Modulariteit**

Dit document beschrijft de voorgestelde opzet van het admin-dashboard voor het SocialCore Project, met nadruk op modulariteit en uitbreidbaarheid via plugins.

---

## 1. Kernonderdelen van het Dashboard (Standaard aanwezig)

Deze onderdelen zijn onderdeel van de kern van het SocialCore-platform en zijn direct beschikbaar na installatie.

### 1.1 Dashboard Home

* Algemene statistieken (gebruikers, berichten)
* Systeeminformatie (versies, opslag, serverstatus)
* Laatste activiteiten (bijv. nieuwe registraties)

### 1.2 Gebruikersbeheer

* Gebruikerslijst en filters
* Profielgegevens bekijken
* Gebruiker bewerken of blokkeren
* Rollen toewijzen (bijv. admin, moderator, member)

### 1.3 Contentmoderatie

* Tijdlijnposts beheren
* Reacties beheren
* Gerapporteerde inhoud
* Mediabeheer (bijv. verwijderen van ongepaste uploads)

### 1.4 Instellingen

* Algemene instellingen (sitenaam, e-mail, timezone)
* Registratiebeheer (open/gesloten, validatie)
* E-mailinstellingen (SMTP, notificaties)

### 1.5 Thema’s

* Actief thema instellen (via `/themes/default/config.php`)
* Thema-opties aanpassen (kleur, layout)

### 1.6 Plugins

* Pluginbeheer (installeren, activeren, deactiveren)
* Plugininstellingen laden via `plugin.php`

### 1.7 Beveiliging en Logs

* IP-blokkeringen
* Gebruikslogs (bijv. logins, wijzigingen)
* Foutmeldingen en debugging

---

## 2. Optionele Modules via Plugins

Functies zoals **groepen** en **pagina’s** zijn NIET onderdeel van de kern en worden via plugins toegevoegd.

### Voorbeeld: GroupsManager Plugin

* Bestandslocatie: `/plugins/GroupsManager/plugin.php`
* Registreert eigen routes, views en menu-entry

```php
function plugin_init() {
    register_admin_menu([
        'title' => 'Groepen',
        'icon' => 'users',
        'link'  => '/admin/groups'
    ]);
    // Extra setup (routes, taalbestanden, etc.)
}
```

In de dashboard-weergave:

```php
if (plugin_is_active('GroupsManager')) {
    $adminMenu[] = ['title' => 'Groepen', 'icon' => 'users', 'link' => '/admin/groups'];
}
```

### Andere mogelijke plugins:

* PagesPlugin
* ForumPlugin
* AdsManager

---

## 3. Werkwijze

* Het dashboard toont alleen items van actieve plugins.
* Elke plugin mag eigen adminroutes en views aanleveren.
* Het menu is dynamisch opgebouwd op basis van actieve plugins.
* Dashboard-weergave in `views/admin/layout.php`

---

## 4. Volgende stappen

* `adminMenu` array dynamisch maken
* Plugin loader uitbreiden met menu-hooks
* `views/admin/layout.php` voorzien van zijbalk en hoofdcontent
* Core plugins maken: GroupsManager, PagesPlugin

---

**Laatste opmerking:**
Deze opzet houdt het systeem licht, overzichtelijk en uitbreidbaar voor ontwikkelaars, perfect passend binnen de filosofie van het SocialCore Project.

Toevoeging Kleuren pallet voor het dashboard

Hoofdkleuren
Naam	Kleur	Omschrijving
--primary-color	#0f3ea3	Donkerblauw (voor knoppen en accenten)
--primary-light	#3f64d1	Iets lichtere variant voor hover
--accent-color	#f59e0b	Oranje/amber voor waarschuwingen
--bg-color	#f9fafb	Zeer lichtgrijs, dashboard-achtergrond
--card-bg	#ffffff	Witte kaarten, basisvlak
--text-color	#1f2937	Donkergrijs (primaire tekst)
--text-muted	#6b7280	Secundaire tekst
--border-color	#e5e7eb	Lijnen tussen elementen
--success-color	#10b981	Groen voor bevestiging
--danger-color	#ef4444	Rood voor foutmeldingen

💡 Bonus: Donker thema variant (optioneel voor later)
Naam	Kleur
--bg-color	#111827
--card-bg	#1f2937
--text-color	#f3f4f6
--text-muted	#9ca3af


