# 🛠️ Werkstructuur SocialCore

Een overzicht van de ontwikkelstappen voor het SocialCore project.

## ✅ 1. Voorbereiding
- [x] Domeinnaam geregistreerd (`socialcoreproject.nl`)
- [x] Subdomein ingesteld voor ontwikkeling (`dev.socialcoreproject.nl`)
- [x] Document root ingesteld op `/public`
- [x] PHP 8.3 geconfigureerd met benodigde extensies
- [x] Git gekoppeld aan Plesk met automatische deployment
- [x] Composer actief
- [x] README.md opgesteld

## ✅ 2. Core Framework
- [x] Basis mapstructuur opgezet (`/app`, `/core`, `/routes`, etc.)
- [x] Simpele router geïmplementeerd
- [x] REST API routing toegevoegd (`/api/v1/...`)
- [ ] Router uitbreiden met middleware en fallback

## ✅ 3. Meertaligheid
- [x] Mapstructuur `/lang/nl` en `/lang/en`
- [ ] Vertaal helper (`__()`)
- [ ] Taalschakelaar component

## 🔐 4. Authenticatie
- [ ] Registratie en login
- [ ] Sessie of JWT
- [ ] Wachtwoord reset
- [ ] E-mailverificatie

## 👤 5. Gebruikersprofiel
- [ ] Profielfoto upload
- [ ] Biografie / beschrijving
- [ ] Eigen feed
- [ ] Privacyopties

## 🧩 6. Plugin Systeem
- [ ] `/plugins/` structuur
- [ ] plugin.json per plugin
- [ ] Init-bestand en hooksysteem

## 🎨 7. Thema Systeem
- [ ] `/themes/default/` als basis
- [ ] Blade-stijl views
- [ ] Thema instelbaar

## 💬 8. Chat
- [ ] Basis tekstchat
- [ ] Uitbreiding via plugin (GIFs, stickers, video)

## 🚀 9. Eerste Alpha Release
- [ ] Publieke demo op subdomein
- [ ] Documentatie uitbreiden
- [ ] Feedback verzamelen
