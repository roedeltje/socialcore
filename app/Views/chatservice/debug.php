<?php
// Debug view voor ChatHandler testing
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-blue-600 mb-4">🧪 ChatHandler Debug Test</h1>
            
            <div class="bg-green-50 border border-green-200 rounded p-4 mb-6">
                <h2 class="font-semibold text-green-800 mb-2">✅ ChatHandler Status</h2>
                <p class="text-green-700">
                    <?php if ($debug_data['handler'] === 'ChatHandler'): ?>
                        ChatHandler is succesvol geladen!
                    <?php else: ?>
                        ❌ Verkeerde handler geladen
                    <?php endif; ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Systeem Info -->
                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                    <h3 class="font-semibold text-blue-800 mb-3">🔧 Systeem Informatie</h3>
                    <ul class="space-y-2 text-sm">
                        <li><strong>Handler:</strong> <?= htmlspecialchars($debug_data['handler']) ?></li>
                        <li><strong>Methode:</strong> <?= htmlspecialchars($debug_data['method']) ?></li>
                        <li><strong>Timestamp:</strong> <?= htmlspecialchars($debug_data['timestamp']) ?></li>
                        <li><strong>Database:</strong> 
                            <span class="<?= $debug_data['database_connected'] === 'YES' ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $debug_data['database_connected'] ?>
                            </span>
                        </li>
                        <?php if (isset($debug_data['messages_count'])): ?>
                        <li><strong>Berichten in DB:</strong> <?= $debug_data['messages_count'] ?></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Gebruiker Info -->
                <div class="bg-purple-50 border border-purple-200 rounded p-4">
                    <h3 class="font-semibold text-purple-800 mb-3">👤 Gebruiker Status</h3>
                    <ul class="space-y-2 text-sm">
                        <li><strong>Status:</strong> 
                            <span class="<?= $debug_data['user_status'] === 'LOGGED IN' ? 'text-green-600' : 'text-red-600' ?>">
                                <?= htmlspecialchars($debug_data['user_status']) ?>
                            </span>
                        </li>
                        <?php if (isset($debug_data['user_id'])): ?>
                        <li><strong>User ID:</strong> <?= $debug_data['user_id'] ?></li>
                        <?php endif; ?>
                        
                        <?php if (isset($debug_data['current_user'])): ?>
                        <li><strong>Username:</strong> <?= htmlspecialchars($debug_data['current_user']['username'] ?? 'N/A') ?></li>
                        <li><strong>Display Name:</strong> <?= htmlspecialchars($debug_data['current_user']['display_name'] ?? 'N/A') ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Avatar Test -->
            <?php if (isset($debug_data['avatar_input'])): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mt-6">
                <h3 class="font-semibold text-yellow-800 mb-3">🖼️ Avatar URL Test</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm"><strong>Avatar Input:</strong></p>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs"><?= htmlspecialchars($debug_data['avatar_input']) ?></code>
                    </div>
                    <div>
                        <p class="text-sm"><strong>Generated URL:</strong></p>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs break-all"><?= htmlspecialchars($debug_data['avatar_url']) ?></code>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm mb-2"><strong>Avatar Preview:</strong></p>
                    <img src="<?= $debug_data['avatar_url'] ?>" alt="Avatar" class="w-16 h-16 rounded-full border">
                </div>
            </div>
            <?php endif; ?>

            <!-- Database Error -->
            <?php if (isset($debug_data['database_error'])): ?>
            <div class="bg-red-50 border border-red-200 rounded p-4 mt-6">
                <h3 class="font-semibold text-red-800 mb-3">❌ Database Error</h3>
                <code class="text-red-700 text-sm"><?= htmlspecialchars($debug_data['database_error']) ?></code>
            </div>
            <?php endif; ?>

            <!-- Acties -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4">🚀 Volgende Stappen</h3>
                <div class="space-y-2">
                    <a href="/?route=chat" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        Test Chat Overzicht
                    </a>
                    <a href="/?route=chat/compose" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors ml-2">
                        Test Compose Pagina  
                    </a>
                    <a href="/" class="inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors ml-2">
                        Terug naar Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>