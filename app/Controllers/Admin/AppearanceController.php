<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\ThemeManager;

/**
 * AppearanceController - Beheer van thema's, widgets en uiterlijk
 */
class AppearanceController extends Controller
{
    private $themeManager;
    
    public function __construct()
    {
        // Eenvoudige initialisatie zonder parent call voor nu
        try {
            $this->themeManager = ThemeManager::getInstance();
        } catch (\Exception $e) {
            // Fallback als ThemeManager niet kan worden geladen
            error_log("ThemeManager kon niet worden geladen: " . $e->getMessage());
            $this->themeManager = null;
        }
    }
    
    /**
     * View methode die admin layout gebruikt
     */
    protected function view($view, $data = [], $forceNewSystem = false)
    {
        // Gebruik de admin layout
        $title = $data['title'] ?? 'Admin';
        $contentView = BASE_PATH . "/app/Views/{$view}.php";
        
        // Laad de admin layout
        include BASE_PATH . '/app/Views/admin/layout.php';
    }
    
    /**
     * Thema's overzicht pagina
     */
    public function themes()
    {
        try {
            // Test of ThemeManager werkt
            if ($this->themeManager === null) {
                throw new \Exception("ThemeManager kon niet worden geladen");
            }
            
            $themes = $this->themeManager->getAllThemes();
            $activeTheme = $this->themeManager->getActiveTheme();
            
            // Debug: Controleer of we data hebben
            if (empty($themes)) {
                throw new \Exception("Geen themes gevonden via ThemeManager");
            }
            
            // Prepare data voor view
            $title = 'Thema\'s beheren';
            
            // DIRECTE ADMIN LAYOUT LOADING (meest reliable)
            $contentView = BASE_PATH . '/app/Views/admin/appearance/themes.php';
            
            // Zorg dat alle variabelen beschikbaar zijn in de view
            extract(compact('title', 'themes', 'activeTheme'));
            
            // Laad admin layout
            include BASE_PATH . '/app/Views/admin/layout.php';
            
        } catch (\Exception $e) {
            // Error handling met admin layout
            $title = 'Thema Fout';
            $error_message = $e->getMessage();
            
            // Show debug info
            echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; margin: 20px; border-radius: 5px;'>";
            echo "<h3>🔍 Debug Informatie - Thema Manager</h3>";
            echo "<p><strong>Fout:</strong> " . htmlspecialchars($error_message) . "</p>";
            echo "<p><strong>ThemeManager status:</strong> " . ($this->themeManager ? 'Geladen' : 'Niet geladen') . "</p>";
            
            if ($this->themeManager) {
                try {
                    $debugThemes = $this->themeManager->getAllThemes();
                    echo "<p><strong>Direct themes test:</strong> " . count($debugThemes) . " gevonden</p>";
                    foreach ($debugThemes as $slug => $theme) {
                        echo "- {$slug}: " . htmlspecialchars($theme['name'] ?? 'Geen naam') . "<br>";
                    }
                } catch (\Exception $debugE) {
                    echo "<p><strong>Debug fout:</strong> " . htmlspecialchars($debugE->getMessage()) . "</p>";
                }
            }
            
            echo "</div>";
            
            // Fallback: lege data
            $themes = [];
            $activeTheme = 'default';
            $contentView = BASE_PATH . '/app/Views/admin/appearance/themes.php';
            include BASE_PATH . '/app/Views/admin/layout.php';
        }
    }
    
    /**
     * Activeer een thema
     */
    public function activateTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/appearance/themes'));
            exit;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        try {
            $this->themeManager->setActiveTheme($themeName);
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol geactiveerd!";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij activeren thema: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/appearance/themes'));
        exit;
    }
    
    /**
     * Thema upload/installatie pagina
     */
    public function installTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleThemeUpload();
        }
        
        $data = [
            'title' => 'Thema installeren'
        ];
        
        $this->view('admin/appearance/install-theme', $data);
    }
    
    /**
     * Verwerk thema upload
     */
    private function handleThemeUpload()
    {
        try {
            if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Geen geldig bestand geüpload.');
            }
            
            $uploadedFile = $_FILES['theme_zip'];
            
            // Controleer bestandstype
            $fileInfo = pathinfo($uploadedFile['name']);
            if (strtolower($fileInfo['extension']) !== 'zip') {
                throw new \Exception('Alleen ZIP bestanden zijn toegestaan.');
            }
            
            // Controleer bestandsgrootte (max 10MB)
            if ($uploadedFile['size'] > 10 * 1024 * 1024) {
                throw new \Exception('Bestand is te groot (maximaal 10MB).');
            }
            
            // Installeer het thema
            $themeName = $this->themeManager->installTheme($uploadedFile['tmp_name']);
            
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol geïnstalleerd!";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij installeren thema: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/appearance/themes'));
        exit;
    }
    
    /**
     * Verwijder een thema
     */
    public function deleteTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/appearance/themes'));
            exit;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        try {
            $this->themeManager->deleteTheme($themeName);
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol verwijderd!";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij verwijderen thema: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/appearance/themes'));
        exit;
    }
    
    /**
     * Thema aanpassen/configureren
     */
    public function customize()
    {
        $activeTheme = $this->themeManager->getActiveTheme();
        $themeData = $this->themeManager->getThemeData($activeTheme);
        $themeOptions = $this->themeManager->getThemeOptions($activeTheme);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleCustomizeSubmit($activeTheme);
        }
        
        $data = [
            'title' => 'Thema aanpassen',
            'themeData' => $themeData,
            'themeOptions' => $themeOptions
        ];
        
        $this->view('admin/appearance/customize', $data);
    }
    
    /**
     * Verwerk thema configuratie updates
     */
    private function handleCustomizeSubmit($themeName)
    {
        try {
            $options = $_POST['theme_options'] ?? [];
            
            // Valideer en sanitize opties hier
            $this->themeManager->updateThemeOptions($options, $themeName);
            
            $_SESSION['success_message'] = 'Thema instellingen zijn opgeslagen!';
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan instellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/appearance/customize'));
        exit;
    }
    
    /**
     * Widget beheer (placeholder)
     */
    public function widgets()
    {
        $data = [
            'title' => 'Widgets beheren'
        ];
        
        $this->view('admin/appearance/widgets', $data);
    }
    
    /**
     * Menu beheer (placeholder)
     */
    public function menus()
    {
        $data = [
            'title' => 'Menu\'s beheren'
        ];
        
        $this->view('admin/appearance/menus', $data);
    }
    
    /**
     * Thema preview (AJAX endpoint)
     */
    public function previewTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        if (!$this->themeManager->themeExists($themeName)) {
            http_response_code(404);
            echo json_encode(['error' => 'Thema niet gevonden']);
            exit;
        }
        
        $themeData = $this->themeManager->getThemeData($themeName);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'theme' => $themeData,
            'preview_url' => base_url('?route=home&preview_theme=' . $themeName)
        ]);
        exit;
    }
}