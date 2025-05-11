<?php
namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        // Maak variabelen beschikbaar in de view
        extract($data);
        
        // Bouw het volledige pad op naar de view
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        // Controleer of het bestand bestaat
        if (!file_exists($viewPath)) {
            echo "<div style='color: red; padding: 20px; border: 1px solid red;'>";
            echo "View niet gevonden: " . htmlspecialchars($view) . ".php";
            echo "<br>Volledig pad: " . htmlspecialchars($viewPath);
            echo "</div>";
            return;
        }
        
        // Laad header, view en footer
        include __DIR__ . '/../Views/layout/header.php';
        include $viewPath;
        include __DIR__ . '/../Views/layout/footer.php';
    }
}