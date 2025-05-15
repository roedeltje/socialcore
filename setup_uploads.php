<?php
// Dit script kan je één keer uitvoeren om de mapstructuur aan te maken

// Definieer base path
$publicPath = __DIR__ . '/public';
$uploadsPath = $publicPath . '/uploads';

// Maak hoofdmappen aan
$directories = [
    $uploadsPath,
    $uploadsPath . '/avatars',
    $uploadsPath . '/covers',
    $uploadsPath . '/posts',
    $uploadsPath . '/attachments',
    $uploadsPath . '/temp'
];

// Huidige jaar en maand
$currentYear = date('Y');
$currentMonth = date('m');

// Voeg jaar/maand structuur toe
foreach (['avatars', 'covers', 'posts', 'attachments'] as $type) {
    $directories[] = $uploadsPath . '/' . $type . '/' . $currentYear;
    $directories[] = $uploadsPath . '/' . $type . '/' . $currentYear . '/' . $currentMonth;
}

// Maak mappen aan en stel rechten in
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "Aangemaakt: $dir<br>";
        } else {
            echo "Fout bij aanmaken: $dir<br>";
        }
    } else {
        echo "Map bestaat al: $dir<br>";
    }
}

// Maak .htaccess bestand om directory listing uit te schakelen
$htaccess = $uploadsPath . '/.htaccess';
if (!file_exists($htaccess)) {
    $htaccessContent = "Options -Indexes\n";
    file_put_contents($htaccess, $htaccessContent);
    echo "Htaccess bestand aangemaakt<br>";
}

// Maak een index.php in temp om directe toegang te blokkeren
$tempIndex = $uploadsPath . '/temp/index.php';
if (!file_exists($tempIndex)) {
    $indexContent = "<?php\n// Stilte is goud\nhttp_response_code(403);\nexit('Direct access denied');\n";
    file_put_contents($tempIndex, $indexContent);
    echo "Temp blokkering aangemaakt<br>";
}

echo "Setup voltooid!";