# SocialCore Installatie Handleiding

## Vereisten
- PHP 8.1 of hoger
- MySQL 5.7 of hoger
- Apache/Nginx webserver met mod_rewrite (of equivalent)

## Installatiestappen

1. **Clone de repository**

git clone https://github.com/roedeltje/socialcore.git

2. **Configureer de database**
- Maak een nieuwe MySQL database aan
- Maak een kopie van `.env.example.php` en noem het `.env.php`
- Vul de juiste database-instellingen in `.env.php` in

3. **Importeer de database**
- Importeer het `database.sql` bestand in je nieuw aangemaakte database

4. **Configureer je webserver**
- Zorg ervoor dat de document root verwijst naar de `public` map
- Zorg ervoor dat URL rewriting is ingeschakeld

5. **Open de website**
- Ga naar de URL van je website
- Je zou de SocialCore welkomstpagina moeten zien

## Problemen oplossen

Als je problemen ondervindt tijdens de installatie:
- Controleer of alle vereisten zijn geïnstalleerd
- Controleer of je database-instellingen correct zijn
- Controleer of je webserver correct is geconfigureerd
- Controleer de foutlogs van PHP en je webserver