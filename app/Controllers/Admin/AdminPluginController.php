<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;
use ZipArchive;
use Exception;

class AdminPluginController extends Controller
{
    /**
     * Plugin overzicht - toont geïnstalleerde plugins
     */
    public function index()
    {
        $plugins = $this->getInstalledPlugins();
        $activePlugins = $this->getActivePlugins();
        
        $data = [
            'title' => 'Plugin Overzicht',
            'plugins' => $plugins,
            'activePlugins' => $activePlugins,
            'totalPlugins' => count($plugins),
            'activeCount' => count($activePlugins)
        ];
        
        // Gebruik de admin layout methode (zoals andere admin controllers)
        $contentView = BASE_PATH . '/app/Views/admin/plugins/index.php';
        $this->view('admin/layout', array_merge($data, ['contentView' => $contentView]));
    }
    
    /**
     * Geïnstalleerde plugins weergave (gedetailleerd overzicht)
     */
    public function installed()
    {
        $plugins = $this->getInstalledPlugins();
        $activePlugins = $this->getActivePlugins();
        
        // Voeg extra informatie toe voor elke plugin
        $detailedPlugins = [];
        foreach ($plugins as $plugin) {
            $pluginData = $plugin;
            $pluginData['is_active'] = in_array($plugin['name'], $activePlugins);
            $pluginData['file_size'] = $this->getPluginFileSize($plugin['path']);
            $pluginData['file_count'] = $this->getPluginFileCount($plugin['path']);
            $pluginData['last_modified'] = $this->getPluginLastModified($plugin['path']);
            $pluginData['has_readme'] = file_exists($plugin['path'] . '/readme.txt');
            $pluginData['has_assets'] = is_dir($plugin['path'] . '/assets');
            $detailedPlugins[] = $pluginData;
        }
        
        $data = [
            'title' => 'Geïnstalleerde Plugins',
            'plugins' => $detailedPlugins,
            'activePlugins' => $activePlugins,
            'totalPlugins' => count($plugins),
            'activeCount' => count($activePlugins)
        ];
        
        $contentView = BASE_PATH . '/app/Views/admin/plugins/installed.php';
        $this->view('admin/layout', array_merge($data, ['contentView' => $contentView]));
    }
    
    /**
     * Nieuwe plugin toevoegen/installeren
     */
    public function addNew()
    {
        $data = [
            'title' => 'Plugin Installeren',
            'availablePlugins' => $this->getAvailablePlugins(),
            'uploadDir' => $this->getPluginUploadDir()
        ];
        
        $contentView = BASE_PATH . '/app/Views/admin/plugins/add-new.php';
        $this->view('admin/layout', array_merge($data, ['contentView' => $contentView]));
    }
    
    /**
     * Plugin editor voor ontwikkelaars
     */
    public function editor()
    {
        $selectedPlugin = $_GET['plugin'] ?? '';
        $plugins = $this->getInstalledPlugins();
        
        $data = [
            'title' => 'Plugin Editor',
            'plugins' => $plugins,
            'selectedPlugin' => $selectedPlugin,
            'pluginContent' => $selectedPlugin ? $this->getPluginContent($selectedPlugin) : ''
        ];
        
        $contentView = BASE_PATH . '/app/Views/admin/plugins/editor.php';
        $this->view('admin/layout', array_merge($data, ['contentView' => $contentView]));
    }
    
    /**
     * Plugin activeren
     */
    public function activate()
    {
        $pluginName = $_POST['plugin'] ?? $_GET['plugin'] ?? '';
        
        if (empty($pluginName)) {
            $_SESSION['error'] = 'Geen plugin opgegeven.';
            header('Location: ' . base_url('?route=admin/plugins'));
            exit;
        }
        
        if ($this->activatePlugin($pluginName)) {
            $_SESSION['success'] = "Plugin '{$pluginName}' succesvol geactiveerd.";
        } else {
            $_SESSION['error'] = "Kon plugin '{$pluginName}' niet activeren.";
        }
        
        header('Location: ' . base_url('?route=admin/plugins'));
        exit;
    }
    
    /**
     * Plugin deactiveren
     */
    public function deactivate()
    {
        $pluginName = $_POST['plugin'] ?? $_GET['plugin'] ?? '';
        
        if (empty($pluginName)) {
            $_SESSION['error'] = 'Geen plugin opgegeven.';
            header('Location: ' . base_url('?route=admin/plugins'));
            exit;
        }
        
        if ($this->deactivatePlugin($pluginName)) {
            $_SESSION['success'] = "Plugin '{$pluginName}' succesvol gedeactiveerd.";
        } else {
            $_SESSION['error'] = "Kon plugin '{$pluginName}' niet deactiveren.";
        }
        
        header('Location: ' . base_url('?route=admin/plugins'));
        exit;
    }
    
    /**
     * Plugin verwijderen
     */
    public function delete()
    {
        $pluginName = $_POST['plugin'] ?? $_GET['plugin'] ?? '';
        
        if (empty($pluginName)) {
            $_SESSION['error'] = 'Geen plugin opgegeven.';
            header('Location: ' . base_url('?route=admin/plugins'));
            exit;
        }
        
        if ($this->deletePlugin($pluginName)) {
            $_SESSION['success'] = "Plugin '{$pluginName}' succesvol verwijderd.";
        } else {
            $_SESSION['error'] = "Kon plugin '{$pluginName}' niet verwijderen.";
        }
        
        header('Location: ' . base_url('?route=admin/plugins'));
        exit;
    }
    
    /**
     * Plugin uploaden en installeren
     */
    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/plugins/add-new'));
            exit;
        }
        
        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Er is een fout opgetreden bij het uploaden van het bestand.';
            header('Location: ' . base_url('?route=admin/plugins/add-new'));
            exit;
        }
        
        $result = $this->installPluginFromZip($_FILES['plugin_zip']);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: ' . base_url('?route=admin/plugins'));
        exit;
    }
    
    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================
    
    /**
     * Haal alle geïnstalleerde plugins op
     */
    private function getInstalledPlugins()
    {
        $pluginsDir = BASE_PATH . '/plugins';
        $plugins = [];
        
        if (!is_dir($pluginsDir)) {
            // Probeer map aan te maken, maar geef geen fout als het niet lukt
            if (!@mkdir($pluginsDir, 0755, true)) {
                // Map bestaat niet en kan niet worden aangemaakt
                // Dit is normaal bij een verse installatie
                return [];
            }
        }
        
        $directories = scandir($pluginsDir);
        
        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            
            $pluginPath = $pluginsDir . '/' . $dir;
            $pluginFile = $pluginPath . '/plugin.php';
            
            if (is_dir($pluginPath) && file_exists($pluginFile)) {
                $pluginInfo = $this->getPluginInfo($pluginFile);
                $pluginInfo['name'] = $dir;
                $pluginInfo['path'] = $pluginPath;
                $plugins[] = $pluginInfo;
            }
        }
        
        return $plugins;
    }
    
    /**
     * Haal actieve plugins op uit database
     */
    private function getActivePlugins()
    {
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            // Controleer of active_plugins tabel bestaat
            $stmt = $pdo->query("SHOW TABLES LIKE 'active_plugins'");
            if ($stmt->rowCount() === 0) {
                $this->createActivePluginsTable();
            }
            
            $stmt = $pdo->query("SELECT plugin_name FROM active_plugins WHERE is_active = 1");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (\Exception $e) {
            error_log("Error getting active plugins: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Plugin informatie uit plugin.php bestand halen
     */
    private function getPluginInfo($pluginFile)
    {
        $content = file_get_contents($pluginFile);
        
        // Default waarden
        $info = [
            'title' => 'Onbekende Plugin',
            'description' => 'Geen beschrijving beschikbaar.',
            'version' => '1.0.0',
            'author' => 'Onbekend',
            'requires' => 'SocialCore 1.0'
        ];
        
        // Parse plugin header informatie (WordPress-stijl)
        if (preg_match('/Plugin Name:\s*(.+)/i', $content, $matches)) {
            $info['title'] = trim($matches[1]);
        }
        
        if (preg_match('/Description:\s*(.+)/i', $content, $matches)) {
            $info['description'] = trim($matches[1]);
        }
        
        if (preg_match('/Version:\s*(.+)/i', $content, $matches)) {
            $info['version'] = trim($matches[1]);
        }
        
        if (preg_match('/Author:\s*(.+)/i', $content, $matches)) {
            $info['author'] = trim($matches[1]);
        }
        
        if (preg_match('/Requires:\s*(.+)/i', $content, $matches)) {
            $info['requires'] = trim($matches[1]);
        }
        
        return $info;
    }
    
    /**
     * Beschikbare plugins voor installatie (dummy data voor nu)
     */
    private function getAvailablePlugins()
    {
        return [
            [
                'name' => 'groups-manager',
                'title' => 'Groups Manager',
                'description' => 'Voeg groepen functionaliteit toe aan je sociale netwerk',
                'version' => '1.0.0',
                'author' => 'SocialCore Team',
                'download_url' => '#'
            ],
            [
                'name' => 'pages-plugin',
                'title' => 'Pages Plugin',
                'description' => 'Creëer en beheer aangepaste pagina\'s',
                'version' => '1.0.0',
                'author' => 'SocialCore Team',
                'download_url' => '#'
            ],
            [
                'name' => 'forum-plugin',
                'title' => 'Forum Plugin',
                'description' => 'Voeg een volledig forum toe aan je site',
                'version' => '1.0.0',
                'author' => 'SocialCore Team',
                'download_url' => '#'
            ]
        ];
    }
    
    /**
     * Plugin upload directory
     */
    private function getPluginUploadDir()
    {
        $uploadDir = BASE_PATH . '/plugins';
        if (!is_dir($uploadDir)) {
            // Probeer map aan te maken, maar geef geen fout als het niet lukt
            @mkdir($uploadDir, 0755, true);
        }
        return $uploadDir;
    }
    
    /**
     * Plugin content voor editor
     */
    private function getPluginContent($pluginName)
    {
        $pluginFile = BASE_PATH . '/plugins/' . $pluginName . '/plugin.php';
        
        if (file_exists($pluginFile)) {
            return file_get_contents($pluginFile);
        }
        
        return '';
    }
    
    /**
     * Plugin activeren
     */
    private function activatePlugin($pluginName)
    {
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            // Controleer of plugin bestaat
            $pluginPath = BASE_PATH . '/plugins/' . $pluginName . '/plugin.php';
            if (!file_exists($pluginPath)) {
                return false;
            }
            
            // Voeg toe aan active_plugins tabel
            $stmt = $pdo->prepare("
                INSERT INTO active_plugins (plugin_name, is_active, activated_at) 
                VALUES (?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE is_active = 1, activated_at = NOW()
            ");
            
            return $stmt->execute([$pluginName]);
            
        } catch (\Exception $e) {
            error_log("Error activating plugin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Plugin deactiveren
     */
    private function deactivatePlugin($pluginName)
    {
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            $stmt = $pdo->prepare("UPDATE active_plugins SET is_active = 0 WHERE plugin_name = ?");
            return $stmt->execute([$pluginName]);
            
        } catch (\Exception $e) {
            error_log("Error deactivating plugin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Plugin verwijderen
     */
    private function deletePlugin($pluginName)
    {
        try {
            // Eerst deactiveren
            $this->deactivatePlugin($pluginName);
            
            // Verwijder plugin directory
            $pluginPath = BASE_PATH . '/plugins/' . $pluginName;
            if (is_dir($pluginPath)) {
                $this->removeDirectory($pluginPath);
            }
            
            // Verwijder uit database
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            $stmt = $pdo->prepare("DELETE FROM active_plugins WHERE plugin_name = ?");
            $stmt->execute([$pluginName]);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("Error deleting plugin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Plugin installeren van ZIP bestand
     */
    private function installPluginFromZip($uploadedFile)
    {
        try {
            // Validatie
            if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Er is een fout opgetreden bij het uploaden.'];
            }
            
            // Bestandstype controleren
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $uploadedFile['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($mimeType, ['application/zip', 'application/x-zip-compressed'])) {
                return ['success' => false, 'message' => 'Alleen ZIP bestanden zijn toegestaan.'];
            }
            
            // Bestandsgrootte controleren (10MB max)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($uploadedFile['size'] > $maxSize) {
                return ['success' => false, 'message' => 'Bestand is te groot. Maximum grootte is 10MB.'];
            }
            
            // ZIP bestand openen
            $zip = new ZipArchive();
            $result = $zip->open($uploadedFile['tmp_name']);
            
            if ($result !== TRUE) {
                return ['success' => false, 'message' => 'ZIP bestand kon niet worden geopend.'];
            }
            
            // Plugin naam en structuur valideren
            $pluginValidation = $this->validateZipStructure($zip);
            if (!$pluginValidation['valid']) {
                $zip->close();
                return ['success' => false, 'message' => $pluginValidation['message']];
            }
            
            $pluginName = $pluginValidation['plugin_name'];
            $pluginDir = BASE_PATH . '/plugins/' . $pluginName;
            
            // Controleer of plugin al bestaat
            if (is_dir($pluginDir)) {
                $zip->close();
                return ['success' => false, 'message' => "Plugin '{$pluginName}' bestaat al. Verwijder eerst de bestaande plugin."];
            }
            
            // Tijdelijke extractie map
            $tempDir = BASE_PATH . '/plugins/temp_' . uniqid();
            
            // Controleer of plugins directory schrijfbaar is
            if (!is_writable(BASE_PATH . '/plugins/')) {
                $zip->close();
                return ['success' => false, 'message' => 'Plugins directory is niet schrijfbaar. Controleer bestandsrechten.'];
            }
            
            // Maak temp directory aan
            if (!mkdir($tempDir, 0755, true)) {
                $zip->close();
                return ['success' => false, 'message' => 'Tijdelijke map kon niet worden aangemaakt.'];
            }
            
            // Extracteer ZIP met betere error handling
            $extractResult = $zip->extractTo($tempDir);
            if (!$extractResult) {
                $zip->close();
                $this->removeDirectory($tempDir);
                
                // Probeer meer specifieke error info
                $lastError = error_get_last();
                $errorMsg = $lastError ? $lastError['message'] : 'Onbekende fout bij uitpakken';
                
                return ['success' => false, 'message' => 'ZIP bestand kon niet worden uitgepakt: ' . $errorMsg];
            }
            $zip->close();
            
            // Verplaats plugin naar juiste locatie
            $extractedPluginDir = $tempDir . '/' . $pluginName;
            if (!is_dir($extractedPluginDir)) {
                $this->removeDirectory($tempDir);
                return ['success' => false, 'message' => 'Plugin map structuur is incorrect.'];
            }
            
            // Verplaats plugin
            if (!rename($extractedPluginDir, $pluginDir)) {
                $this->removeDirectory($tempDir);
                return ['success' => false, 'message' => 'Plugin kon niet worden geïnstalleerd.'];
            }
            
            // Cleanup temp directory
            $this->removeDirectory($tempDir);
            
            // Valideer geïnstalleerde plugin
            $pluginFile = $pluginDir . '/plugin.php';
            if (!file_exists($pluginFile)) {
                $this->removeDirectory($pluginDir);
                return ['success' => false, 'message' => 'Plugin bevat geen geldig plugin.php bestand.'];
            }
            
            // Parse plugin informatie
            $pluginInfo = $this->getPluginInfo($pluginFile);
            
            return [
                'success' => true, 
                'message' => "Plugin '{$pluginInfo['title']}' is succesvol geïnstalleerd!"
            ];
            
        } catch (Exception $e) {
            error_log("Plugin installation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Er is een onverwachte fout opgetreden tijdens de installatie.'];
        }
    }
    
    /**
     * Valideer ZIP bestand structuur
     */
    private function validateZipStructure($zip)
    {
        $numFiles = $zip->numFiles;
        
        if ($numFiles === 0) {
            return ['valid' => false, 'message' => 'ZIP bestand is leeg.'];
        }
        
        // Zoek naar plugin.php bestanden
        $pluginFiles = [];
        $pluginName = null;
        
        for ($i = 0; $i < $numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            
            // Skip MacOS bestanden
            if (strpos($filename, '__MACOSX/') === 0 || strpos($filename, '.DS_Store') !== false) {
                continue;
            }
            
            // Zoek naar plugin.php
            if (basename($filename) === 'plugin.php') {
                $pathParts = explode('/', $filename);
                
                // Plugin moet in een map zitten
                if (count($pathParts) >= 2) {
                    $potentialPluginName = $pathParts[0];
                    
                    // Valideer plugin naam
                    if (preg_match('/^[a-zA-Z0-9\-_]+$/', $potentialPluginName)) {
                        $pluginFiles[] = $filename;
                        if (!$pluginName) {
                            $pluginName = $potentialPluginName;
                        }
                    }
                }
            }
        }
        
        if (empty($pluginFiles)) {
            return ['valid' => false, 'message' => 'ZIP bevat geen geldig plugin.php bestand in een plugin map.'];
        }
        
        if (count($pluginFiles) > 1) {
            return ['valid' => false, 'message' => 'ZIP bevat meerdere plugins. Upload één plugin per keer.'];
        }
        
        // Valideer plugin naam
        if (empty($pluginName) || strlen($pluginName) < 2) {
            return ['valid' => false, 'message' => 'Plugin map naam is ongeldig.'];
        }
        
        // Controleer of plugin.php valid PHP header heeft
        $pluginContent = $zip->getFromName($pluginFiles[0]);
        if (!$this->validatePluginHeader($pluginContent)) {
            return ['valid' => false, 'message' => 'Plugin.php bevat geen geldige plugin header.'];
        }
        
        return [
            'valid' => true, 
            'plugin_name' => $pluginName,
            'plugin_file' => $pluginFiles[0]
        ];
    }
    
    /**
     * Valideer plugin header
     */
    private function validatePluginHeader($content)
    {
        // Controleer of het PHP code is
        if (strpos($content, '<?php') !== 0) {
            return false;
        }
        
        // Controleer of het een plugin header heeft
        $requiredHeaders = ['Plugin Name:', 'Description:', 'Version:'];
        $foundHeaders = 0;
        
        foreach ($requiredHeaders as $header) {
            if (strpos($content, $header) !== false) {
                $foundHeaders++;
            }
        }
        
        // Minimaal 2 van de 3 headers moeten aanwezig zijn
        return $foundHeaders >= 2;
    }
    
    /**
     * Directory recursief verwijderen
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
    
    /**
     * Maak active_plugins tabel aan als deze niet bestaat
     */
    private function createActivePluginsTable()
    {
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            $sql = "
                CREATE TABLE IF NOT EXISTS active_plugins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    plugin_name VARCHAR(100) NOT NULL UNIQUE,
                    is_active TINYINT(1) DEFAULT 1,
                    activated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $pdo->exec($sql);
            
        } catch (\Exception $e) {
            error_log("Error creating active_plugins table: " . $e->getMessage());
        }
    }
    
    /**
     * Plugin bestandsgrootte bepalen
     */
    private function getPluginFileSize($pluginPath)
    {
        if (!is_dir($pluginPath)) return 0;
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pluginPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Aantal bestanden in plugin map tellen
     */
    private function getPluginFileCount($pluginPath)
    {
        if (!is_dir($pluginPath)) return 0;
        
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pluginPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Laatste wijzigingsdatum van plugin bepalen
     */
    private function getPluginLastModified($pluginPath)
    {
        $pluginFile = $pluginPath . '/plugin.php';
        if (file_exists($pluginFile)) {
            return filemtime($pluginFile);
        }
        return 0;
    }
}