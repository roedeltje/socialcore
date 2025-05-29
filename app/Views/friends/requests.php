<?php
// Verbeterde friends/requests.php template
echo "<div class='container mx-auto px-4 py-8'>";
echo "<h1 class='text-2xl font-bold mb-6'>Vriendschapsverzoeken</h1>";

if (isset($pendingRequests) && !empty($pendingRequests)) {
    echo "<div class='space-y-4'>";
    foreach ($pendingRequests as $request) {
        echo "<div class='bg-white p-4 rounded-lg shadow-sm border'>";
        echo "<div class='flex items-center justify-between'>";
        echo "<div class='flex items-center space-x-3'>";
        
        // AANGEPASTE REGEL - gebruik avatar_url in plaats van handmatige path
        echo "<img src='{$request['avatar_url']}' alt='Avatar' class='w-12 h-12 rounded-full'>";
        
        echo "<div>";
        echo "<h3 class='font-semibold'>" . htmlspecialchars($request['display_name']) . "</h3>";
        echo "<p class='text-sm text-gray-600'>@" . htmlspecialchars($request['username']) . "</p>";
        echo "<p class='text-xs text-gray-500'>Verzoek verzonden: " . date('d-m-Y H:i', strtotime($request['created_at'])) . "</p>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='flex space-x-2'>";
        // Gebruik base_url() voor betere URL generatie
        echo "<form method='POST' action='" . base_url('friends/accept') . "' class='inline'>";
        echo "<input type='hidden' name='friendship_id' value='{$request['friendship_id']}'>";
        echo "<button type='submit' class='bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors'>✓ Accepteren</button>";
        echo "</form>";
        
        echo "<form method='POST' action='" . base_url('friends/decline') . "' class='inline'>";
        echo "<input type='hidden' name='friendship_id' value='{$request['friendship_id']}'>";
        echo "<button type='submit' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors'>✗ Weigeren</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-6 text-center'>";
    echo "<div class='text-4xl mb-3'>👥</div>";
    echo "<h3 class='text-lg font-semibold text-blue-800 mb-2'>Geen nieuwe vriendschapsverzoeken</h3>";
    echo "<p class='text-blue-600'>Je hebt momenteel geen openstaande vriendschapsverzoeken.</p>";
    echo "</div>";
}

echo "</div>";
?>