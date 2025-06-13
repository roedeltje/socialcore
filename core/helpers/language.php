<?php
/**
 * Language Helper - SocialCore
 * 
 * VERVANG DE HELE INHOUD van /core/helpers/language.php met dit bestand
 */

/**
 * Vertaal een sleutel naar de huidige taal
 * 
 * @param string $key De vertaalsleutel
 * @param array|string $replace Vervangingen voor placeholders (optioneel)
 * @param string $file Het vertaalbestand (optioneel)
 * @return string De vertaalde tekst
 */
function __($key, $replace = [], $file = null) {
    // Zorg ervoor dat $replace altijd een array is
    if (is_string($replace)) {
        $replace = [$replace];
    } elseif (!is_array($replace)) {
        $replace = [];
    }
    
    // Als er geen file is opgegeven, probeer de key te splitsen
    if ($file === null) {
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key, 2);
            $file = $parts[0];
            $key = $parts[1];
        } else {
            $file = 'app'; // Default file
        }
    }
    
    // Probeer de vertaling te vinden
    $translation = getTranslation($key, $file);
    
    // Als er geen vertaling is, gebruik de key zelf
    if ($translation === null) {
        $translation = $key;
    }
    
    // Vervang placeholders als er vervangingen zijn
    if (!empty($replace)) {
        foreach ($replace as $search => $replacement) {
            if (is_numeric($search)) {
                // Numerieke keys: vervang :0, :1, etc.
                $translation = str_replace(':' . $search, $replacement, $translation);
            } else {
                // String keys: vervang :key
                $translation = str_replace(':' . $search, $replacement, $translation);
            }
        }
    }
    
    return $translation;
}

/**
 * Helper functie om vertaling op te halen
 */
function getTranslation($key, $file) {
    static $translations = [];
    
    if (!isset($translations[$file])) {
        $langFile = __DIR__ . '/../../lang/' . getCurrentLanguage() . '/' . $file . '.php';
        if (file_exists($langFile)) {
            $translations[$file] = require $langFile;
        } else {
            $translations[$file] = [];
        }
    }
    
    return $translations[$file][$key] ?? null;
}

/**
 * Krijg huidige taal
 */
function getCurrentLanguage() {
    return $_SESSION['language'] ?? 'nl';
}

/**
 * Zet de huidige taal
 */
function setLanguage($lang) {
    $_SESSION['language'] = $lang;
}

/**
 * Krijg alle beschikbare talen
 */
function getAvailableLanguages() {
    $languages = [];
    $langDir = __DIR__ . '/../../lang';
    
    if (is_dir($langDir)) {
        $dirs = scandir($langDir);
        foreach ($dirs as $dir) {
            if ($dir !== '.' && $dir !== '..' && is_dir($langDir . '/' . $dir)) {
                $languages[] = $dir;
            }
        }
    }
    
    return $languages;
}

/**
 * Laad een specifiek language bestand
 */
function loadLanguageFile($file, $lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    $langFile = __DIR__ . '/../../lang/' . $lang . '/' . $file . '.php';
    
    if (file_exists($langFile)) {
        return require $langFile;
    }
    
    return [];
}

/**
 * Controleer of een taal bestaat
 */
function languageExists($lang) {
    $langDir = __DIR__ . '/../../lang/' . $lang;
    return is_dir($langDir);
}

/**
 * Plural helper voor vertalingen
 */
function __n($single, $plural, $count, $replace = [], $file = null) {
    $key = ($count == 1) ? $single : $plural;
    $replace['count'] = $count;
    return __($key, $replace, $file);
}

/**
 * Escape HTML en vertaal tegelijk
 */
function __e($key, $replace = [], $file = null) {
    return htmlspecialchars(__($key, $replace, $file), ENT_QUOTES, 'UTF-8');
}