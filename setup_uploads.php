<?php
// Dit script kan je één keer uitvoeren om de mapstructuur aan te maken

// Definieer base path - pas dit aan naar jouw mapstructuur
define('BASE_PATH', dirname(__DIR__)); // Ga één map omhoog
$publicPath = BASE_PATH . '/public';
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

// Standaard avatar toevoegen als deze nog niet bestaat
$defaultAvatarDir = $publicPath . '/assets/images';
if (!file_exists($defaultAvatarDir)) {
    mkdir($defaultAvatarDir, 0755, true);
    echo "Map voor standaard afbeeldingen aangemaakt<br>";
}

$defaultAvatarPath = $defaultAvatarDir . '/default-avatar.png';
if (!file_exists($defaultAvatarPath)) {
    // Hier maken we een eenvoudige avatar als fallback
    $avatar = imagecreatetruecolor(200, 200);
    $bg = imagecolorallocate($avatar, 100, 149, 237); // Cornflower blue
    $fg = imagecolorallocate($avatar, 255, 255, 255);
    
    // Achtergrond vullen
    imagefill($avatar, 0, 0, $bg);
    
    // Cirkel tekenen als avatar placeholder
    imagefilledellipse($avatar, 100, 100, 150, 150, $fg);
    
    // Tekst toevoegen
    imagestring($avatar, 5, 70, 90, "USER", $bg);
    
    // Opslaan
    imagepng($avatar, $defaultAvatarPath);
    imagedestroy($avatar);
    echo "Default avatar aangemaakt<br>";
}

// Vrienden afbeelding voor de homepage
$friendsImagePath = $defaultAvatarDir . '/friends-group.jpg';
if (!file_exists($friendsImagePath)) {
    echo "Let op: Je moet nog een 'friends-group.jpg' afbeelding toevoegen aan $defaultAvatarDir voor de homepage<br>";
}

// Achtergrond afbeelding voor de homepage
$backgroundImagePath = $defaultAvatarDir . '/background-people.jpg';
if (!file_exists($backgroundImagePath)) {
    echo "Let op: Je moet nog een 'background-people.jpg' afbeelding toevoegen aan $defaultAvatarDir voor de homepage achtergrond<br>";
}

echo "<br>Setup voltooid!";