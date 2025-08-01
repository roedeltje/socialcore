<?php
echo "API Test Start<br>";

try {
    echo "Loading api.php...<br>";
    $apiRoutes = require __DIR__ . '/../routes/api.php';
    echo "API routes loaded: " . count($apiRoutes) . "<br>";
    
    echo "Looking for ping route...<br>";
    if (isset($apiRoutes['ping'])) {
        echo "Ping route found!<br>";
        echo "Calling ping function...<br>";
        $apiRoutes['ping']();
    } else {
        echo "Ping route NOT found<br>";
        echo "Available routes: " . implode(', ', array_keys($apiRoutes)) . "<br>";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

echo "API Test End<br>";
?>