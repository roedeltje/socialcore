# SocialCore

**Het eerste Nederlandstalige open source social media platform.**  
Lichtgewicht, modulair en uitbreidbaar. Gebouwd voor ontwikkelaars en communitybeheerders.

## 🔧 Wat is SocialCore?

SocialCore is een modern, open source platform voor het bouwen van sociale netwerken.  
Geïnspireerd op Sngine, maar helemaal opnieuw opgebouwd met:

- Eén minimalistische kern
- Eén standaardthema (in `/themes/default`)
- Plugin-ondersteuning zoals bij WordPress
- Gebruik van moderne PHP- en front-end technieken (Laravel/Blade-stijl & Tailwind CSS)

## 🧩 Kernprincipes

- Modulair: alles is uitbreidbaar via plugins en thema's
- Eenvoudig: minimale afhankelijkheden
- Developer-first: alles is leesbaar, documenteerbaar en makkelijk aanpasbaar
- Meertalig met Nederlandse basis

## 🌍 Meertaligheid

SocialCore ondersteunt volledig meertalige interfaces:

- Nederlandse en Engelse vertalingen ingebouwd
- Eenvoudige `__('app.key')` syntax voor strings
- Taalschakelaar component voor gebruikers
- Uitbreidbaar met extra talen
- Zie [`docs/i18n.md`](docs/i18n.md) voor implementatiedetails

## 📁 Structuur

```plaintext
/socialcore
├── app/               # Applicatiecode (views, controllers)
├── core/              # Kerncode van het platform
├── docs/              # Documentatie
├── lang/              # Taalbestanden (NL, EN, ...)
├── plugins/           # Uitbreidbare plugins
├── public/            # index.php + assets (frontend)
├── routes/            # Routedefinitie
├── themes/default/    # Standaardthema
├── README.md
└── LICENSE (MIT)
```

## 🚀 Functionaliteiten

- Gebruikersregistratie & inloggen
- Profielpagina's
- Berichten plaatsen
- Vriendverzoeken
- REST API (`/api/v1/`)
- Meertalige interface
- Thema-systeem
- Plugin-systeem
- Simpele chat (basisversie, uitbreidbaar via plugin)

## 🛠️ Ontwikkeling

Het project bevindt zich momenteel in vroege ontwikkeling. Bijdragen zijn welkom!

```bash
# Kloon de repository
git clone https://github.com/roedeltje/socialcore.git

# Maak aanpassingen en test lokaal
```

Bekijk ook ons [`WERKSCHEMA.md`](WERKSCHEMA.md) voor de ontwikkelingsplanning.

## 📄 Licentie

SocialCore is open source software onder de [MIT licentie](LICENSE).
