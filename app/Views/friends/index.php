<?php
// Fallback content voor friends/index.php
echo "<div class='container mx-auto px-4 py-8'>";
echo "<h1 class='text-2xl font-bold mb-6'>Mijn Vrienden ({$friendCount})</h1>";

if (isset($friends) && !empty($friends)) {
    echo "<div class='grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4'>";
    foreach ($friends as $friend) {
        echo "<div class='bg-white p-4 rounded-lg shadow-sm border text-center'>";
        
        $avatar = get_avatar_url($friend['avatar']);
        echo "<img src='{$avatar}' alt='Avatar' class='w-16 h-16 rounded-full mx-auto mb-2'>";
        
        echo "<h3 class='font-semibold text-sm'>{$friend['display_name']}</h3>";
        echo "<p class='text-xs text-gray-600'>@{$friend['username']}</p>";
        echo "<a href='/profile?user={$friend['username']}' class='text-blue-600 text-xs hover:underline'>Bekijk profiel</a>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p class='text-gray-600'>Je hebt nog geen vrienden. Begin met het toevoegen van mensen!</p>";
}

echo "</div>";
?>