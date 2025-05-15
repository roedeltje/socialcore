<?php
namespace App\Controllers;

class SetupController extends Controller
{
    public function setupUploads()
    {
        // Definieer base path
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
        $results = [];
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (mkdir($dir, 0755, true)) {
                    $results[] = "Aangemaakt: $dir";
                } else {
                    $results[] = "Fout bij aanmaken: $dir";
                }
            } else {
                $results[] = "Map bestaat al: $dir";
            }
        }

        // Maak .htaccess bestand om directory listing uit te schakelen
        $htaccess = $uploadsPath . '/.htaccess';
        if (!file_exists($htaccess)) {
            $htaccessContent = "Options -Indexes\n";
            file_put_contents($htaccess, $htaccessContent);
            $results[] = "Htaccess bestand aangemaakt";
        }

        // Maak een index.php in temp om directe toegang te blokkeren
        $tempIndex = $uploadsPath . '/temp/index.php';
        if (!file_exists($tempIndex)) {
            $indexContent = "<?php\n// Stilte is goud\nhttp_response_code(403);\nexit('Direct access denied');\n";
            file_put_contents($tempIndex, $indexContent);
            $results[] = "Temp blokkering aangemaakt";
        }

        // Standaard avatar toevoegen als deze nog niet bestaat
        $defaultAvatarDir = $publicPath . '/assets/images';
        if (!file_exists($defaultAvatarDir)) {
            mkdir($defaultAvatarDir, 0755, true);
            $results[] = "Map voor standaard afbeeldingen aangemaakt";
        }

        // We kunnen een eenvoudige placeholder avatar maken met PHP
        $defaultAvatarPath = $defaultAvatarDir . '/default-avatar.png';
        if (!file_exists($defaultAvatarPath) && function_exists('imagecreatetruecolor')) {
            // Maak eenvoudige avatar met GD library als beschikbaar
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
            $results[] = "Default avatar aangemaakt";
        } else if (!file_exists($defaultAvatarPath)) {
            $results[] = "LET OP: Standaard avatar moet handmatig worden toegevoegd in $defaultAvatarDir";
        }

        // Stuur de resultaten naar een view
        $data = [
            'title' => 'Setup voltooid',
            'results' => $results
        ];

        // Als je een setup view hebt, gebruik die
        // Anders direct output
        if (file_exists(BASE_PATH . '/app/Views/setup/uploads.php')) {
            $this->view('setup/uploads', $data);
        } else {
            echo "<h1>Setup Resultaten</h1>";
            echo "<ul>";
            foreach ($results as $result) {
                echo "<li>$result</li>";
            }
            echo "</ul>";
            echo "<p><a href='" . base_url('') . "'>Terug naar home</a></p>";
        }
    }
}