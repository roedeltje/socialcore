<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\ThemeManager;

/**
 * AppearanceController - Beheer van thema's, widgets en uiterlijk
 */
class AppearanceController extends Controller
{
    private ?ThemeManager $themeManager;
    
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
     * Check of ThemeManager beschikbaar is, throw exception als niet
     */
    private function ensureThemeManager(): ThemeManager
    {
        if ($this->themeManager === null) {
            throw new \Exception("ThemeManager is niet beschikbaar. Controleer de configuratie.");
        }
        return $this->themeManager;
    }
    
    /**
     * View methode die admin layout gebruikt
     */
    protected function view($view, $data = [], $forceNewSystem = false): void
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
    public function themes(): void
    {
        try {
            $themeManager = $this->ensureThemeManager();
            
            $themes = $themeManager->getAllThemes();
            $activeTheme = $themeManager->getActiveTheme();
            
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
            $this->handleError($e, 'Thema Manager');
        }
    }
    
    /**
     * Centralized error handling
     */
    private function handleError(\Exception $e, string $context): void
    {
        // Error handling met admin layout
        $title = 'Thema Fout';
        $error_message = $e->getMessage();
        
        // Show debug info
        echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; margin: 20px; border-radius: 5px;'>";
        echo "<h3>🔍 Debug Informatie - {$context}</h3>";
        echo "<p><strong>Fout:</strong> " . htmlspecialchars($error_message) . "</p>";
        echo "<p><strong>ThemeManager status:</strong> " . ($this->themeManager ? 'Geladen' : 'Niet geladen') . "</p>";
        
        if ($this->themeManager !== null) {
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
    
    /**
     * Activeer een thema
     */
    public function activateTheme(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToThemes();
            return;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        try {
            $themeManager = $this->ensureThemeManager();
            $themeManager->setActiveTheme($themeName);
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol geactiveerd!";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij activeren thema: " . $e->getMessage();
        }
        
        $this->redirectToThemes();
    }
    
    /**
     * Helper methode voor redirect naar themes pagina
     */
    private function redirectToThemes(): void
    {
        header('Location: ' . base_url('?route=admin/appearance/themes'));
        exit;
    }
    
    /**
     * Thema upload/installatie pagina
     */
    public function installTheme(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleThemeUpload();
            return;
        }
        
        $data = [
            'title' => 'Thema installeren'
        ];
        
        $this->view('admin/appearance/install-theme', $data);
    }
    
    /**
     * Verwerk thema upload
     */
    private function handleThemeUpload(): void
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
            $themeManager = $this->ensureThemeManager();
            $themeName = $themeManager->installTheme($uploadedFile['tmp_name']);
            
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol geïnstalleerd!";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij installeren thema: " . $e->getMessage();
        }
        
        $this->redirectToThemes();
    }
    
    /**
     * Verwijder een thema
     */
    public function deleteTheme(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToThemes();
            return;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        try {
            $themeManager = $this->ensureThemeManager();
            $themeManager->deleteTheme($themeName);
            $_SESSION['success_message'] = "Thema '{$themeName}' is succesvol verwijderd!";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij verwijderen thema: " . $e->getMessage();
        }
        
        $this->redirectToThemes();
    }
    
    /**
     * Thema aanpassen/configureren
     */
    public function customize(): void
    {
        try {
            $themeManager = $this->ensureThemeManager();
            $activeTheme = $themeManager->getActiveTheme();
            $themeData = $themeManager->getThemeData($activeTheme);
            $themeOptions = $themeManager->getThemeOptions($activeTheme);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleCustomizeSubmit($activeTheme);
                return;
            }
            
            $data = [
                'title' => 'Thema aanpassen',
                'themeData' => $themeData,
                'themeOptions' => $themeOptions
            ];
            
            $this->view('admin/appearance/customize', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden customize pagina: " . $e->getMessage();
            $this->redirectToThemes();
        }
    }
    
    /**
     * Verwerk thema configuratie updates
     */
    private function handleCustomizeSubmit(string $themeName): void
    {
        try {
            $options = $_POST['theme_options'] ?? [];
            
            $themeManager = $this->ensureThemeManager();
            // Valideer en sanitize opties hier
            $themeManager->updateThemeOptions($options, $themeName);
            
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
    public function widgets(): void
    {
        $data = [
            'title' => 'Widgets beheren'
        ];
        
        $this->view('admin/appearance/widgets', $data);
    }
    
    /**
     * Menu beheer (placeholder)
     */
    public function menus(): void
    {
        $data = [
            'title' => 'Menu\'s beheren'
        ];
        
        $this->view('admin/appearance/menus', $data);
    }
    
    /**
     * Thema preview (AJAX endpoint)
     */
    public function previewTheme(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $themeName = $_POST['theme'] ?? '';
        
        try {
            $themeManager = $this->ensureThemeManager();
            
            if (!$themeManager->themeExists($themeName)) {
                http_response_code(404);
                echo json_encode(['error' => 'Thema niet gevonden']);
                exit;
            }
            
            $themeData = $themeManager->getThemeData($themeName);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'theme' => $themeData,
                'preview_url' => base_url('?route=home&preview_theme=' . $themeName)
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
}