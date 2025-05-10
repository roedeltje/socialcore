<?php
/**
 * SocialCore Taalhulpfuncties
 * 
 * Bevat functies voor meertaligheid binnen het SocialCore platform.
 */

/**
 * Geeft een vertaalde string terug
 * 
 * @param string $key De sleutel in de vorm van "bestand.string_key"
 * @param array $replace Associatieve array met waarden die moeten worden vervangen
 * @param string|null $locale Optionele taal, gebruikt anders de huidige taal
 * @return string
 */
function __($key, array $replace = [], ?string $locale = null): string 
{
    static $translations = [];
    
    // Als geen locale is opgegeven, gebruik dan de huidige taal uit de sessie
    if ($locale === null) {
        $locale = get_current_language();
    }
    
    // Splits de sleutel in bestandsnaam en string key
    $parts = explode('.', $key, 2);
    
    if (count($parts) !== 2) {
        // Ongeldige sleutel formaat, geef de sleutel zelf terug
        return $key;
    }
    
    [$file, $stringKey] = $parts;
    
    // Laad het taalbestand als het nog niet is geladen
    $cacheKey = $locale . '.' . $file;
    if (!isset($translations[$cacheKey])) {
        $langFilePath = __DIR__ . '/../../lang/' . $locale . '/' . $file . '.php';
        
        if (file_exists($langFilePath)) {
            $translations[$cacheKey] = require $langFilePath;
        } else {
            // Bestand bestaat niet, probeer de standaardtaal
            $defaultLocale = get_default_language();
            $defaultFilePath = __DIR__ . '/../../lang/' . $defaultLocale . '/' . $file . '.php';
            
            if ($locale !== $defaultLocale && file_exists($defaultFilePath)) {
                $translations[$cacheKey] = require $defaultFilePath;
            } else {
                // Geen vertaling gevonden, lege array gebruiken
                $translations[$cacheKey] = [];
            }
        }
    }
    
    // Haal de vertaling op
    $translation = $translations[$cacheKey][$stringKey] ?? $stringKey;
    
    // Vervang placeholders met waarden
    if (!empty($replace)) {
        foreach ($replace as $placeholder => $value) {
            $translation = str_replace(':' . $placeholder, $value, $translation);
        }
    }
    
    return $translation;
}

/**
 * Haalt de huidige taal op uit sessie of cookie
 * 
 * @return string Taalcode (bijv. 'nl', 'en')
 */
function get_current_language(): string 
{
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    if (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }
    
    return get_default_language();
}

/**
 * Haalt de standaardtaal van de applicatie op
 * 
 * @return string Taalcode (bijv. 'nl', 'en')
 */
function get_default_language(): string 
{
    // Haal uit de config, of gebruik 'nl' als standaard
    // TODO: Implementeer een config system om dit configureerbaar te maken
    return 'nl';
}

/**
 * Stelt de taal in voor de huidige sessie en (optioneel) cookie
 * 
 * @param string $locale Taalcode (bijv. 'nl', 'en')
 * @param bool $rememberInCookie True om de keuze op te slaan in een cookie
 * @param int $cookieDuration Duur van de cookie in seconden (standaard 30 dagen)
 * @return bool
 */
function set_language(string $locale, bool $rememberInCookie = false, int $cookieDuration = 2592000): bool 
{
    // Controleer of de taal bestaat
    $langDir = __DIR__ . '/../../lang/' . $locale;
    
    if (!is_dir($langDir)) {
        return false;
    }
    
    // Sla de taal op in de sessie
    $_SESSION['language'] = $locale;
    
    // Optioneel: sla de taal op in een cookie
    if ($rememberInCookie) {
        setcookie('language', $locale, [
            'expires' => time() + $cookieDuration,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    return true;
}

/**
 * Krijgt een lijst van alle beschikbare talen
 * 
 * @return array Associatieve array met taalcode => taalnaam
 */
function get_available_languages(): array 
{
    $langDir = __DIR__ . '/../../lang';
    $languages = [];
    
    // Scan de language directory
    $dirs = scandir($langDir);
    
    foreach ($dirs as $dir) {
        if ($dir === '.' || $dir === '..' || !is_dir($langDir . '/' . $dir)) {
            continue;
        }
        
        // Hier zou je een mapping kunnen hebben tussen taalcodes en namen
        // Voor nu gebruiken we een eenvoudige mapping
        $languageNames = [
            'nl' => 'Nederlands',
            'en' => 'English',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'es' => 'Español'
        ];
        
        $languages[$dir] = $languageNames[$dir] ?? $dir;
    }
    
    return $languages;
}