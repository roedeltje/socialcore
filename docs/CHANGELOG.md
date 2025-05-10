# CHANGELOG

## Stap 4 - Inlogsysteem met sessies

### Toegevoegd
- core/Auth.php met sessie-authenticatie
- core/Database.php voor PDO-verbinding
- public/login.php, register.php, dashboard.php, logout.php
- views/auth/{login.php, register.php}
- routes/web.php uitgebreid met login- en dashboardroutes

### Gewijzigd
- N.v.t. — bestaande .htaccess werkte al correct voor nette URLs

### Volgende verbeteringen
- Flash messages voor betere feedback
- Invoervalidatie en beveiligingschecks (CSRF, throttling)
- Admin/dashboard afscherming per rol
