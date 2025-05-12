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
    
    // Buffer de view content
    ob_start();
    include $viewPath;
    $content = ob_get_clean();  // Dit wordt gebruikt in layout.php
    
    // Voeg content toe aan de data array zodat het beschikbaar is in de layout
    $data['content'] = $content;
    
    // Extract opnieuw zodat $content beschikbaar is
    extract($data);
    
    // Laad het layout bestand dat de header/footer insluit
    include __DIR__ . '/../Views/layout.php';
}
}