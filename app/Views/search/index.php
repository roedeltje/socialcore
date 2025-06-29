<div class="max-w-4xl mx-auto p-6">
    <!-- Search Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-hyves-blue mb-4">🔍 Zoeken</h1>
        
        <!-- Search Form -->
        <form method="GET" action="/" class="mb-4">
            <input type="hidden" name="route" value="search">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="q" 
                        value="<?= htmlspecialchars($query) ?>"
                        placeholder="Zoek naar mensen, #hashtags..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-hyves-blue focus:border-transparent"
                    >
                </div>
                <div>
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Alles</option>
                        <option value="users" <?= $type === 'users' ? 'selected' : '' ?>>Mensen</option>
                        <option value="hashtags" <?= $type === 'hashtags' ? 'selected' : '' ?>>Hashtags</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 text-white rounded-lg hover:bg-blue-700" style="background-color: #0f3ea3;">
                    Zoeken
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($query)): ?>
        
        <!-- Results Section -->
        <div class="space-y-6">
            
            <!-- User Results -->
            <?php if (!empty($userResults)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">👥 Mensen (<?= count($userResults) ?>)</h2>
                    <div class="space-y-3">
                        <?php foreach ($userResults as $user): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        src="<?= $user['avatar_url'] ?>" 
                                        alt="Avatar" 
                                        class="w-12 h-12 rounded-full object-cover"
                                    >
                                    <div>
                                        <a href="/?route=profile&user=<?= $user['username'] ?>" 
                                           class="font-medium text-hyves-blue hover:underline">
                                            <?= htmlspecialchars($user['display_name'] ?: $user['username']) ?>
                                        </a>
                                        <div class="text-sm text-gray-500">@<?= htmlspecialchars($user['username']) ?></div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php if ($user['friendship_status'] === 'friends'): ?>
                                        <span class="text-green-600">✓ Vrienden</span>
                                    <?php elseif ($user['friendship_status'] === 'sent'): ?>
                                        <span class="text-yellow-600">⏳ Verzoek verzonden</span>
                                    <?php elseif ($user['friendship_status'] === 'received'): ?>
                                        <span class="text-blue-600">📨 Verzoek ontvangen</span>
                                    <?php else: ?>
                                        <a href="/?route=friends/add&user=<?= $user['username'] ?>" 
                                           class="text-hyves-blue hover:underline">+ Toevoegen</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Hashtag Results -->
            <?php if (!empty($hashtagResults)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4"># Hashtags (<?= count($hashtagResults) ?>)</h2>
                    <div class="space-y-3">
                        <?php foreach ($hashtagResults as $hashtag): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                <div>
                                    <a href="/?route=search/hashtag&tag=<?= urlencode($hashtag['tag']) ?>" 
                                       class="font-medium text-hyves-blue hover:underline text-lg">
                                        #<?= htmlspecialchars($hashtag['tag']) ?>
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        <?= $hashtag['usage_count'] ?> berichten
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?= date('j M Y', strtotime($hashtag['created_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>

        <!-- No Results -->
        <?php if (!$hasResults): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="text-gray-500 text-lg mb-2">🤷‍♂️</div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Geen resultaten gevonden</h3>
                <p class="text-gray-500">Probeer een andere zoekterm of controleer je spelling.</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        
        <!-- Welcome/Instructions -->
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Ontdek nieuwe mensen en content</h2>
            <p class="text-gray-500 mb-4">Zoek naar vrienden, bekijk populaire hashtags of ontdek interessante berichten.</p>
            
            <!-- Popular hashtags preview (optioneel) -->
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-600 mb-3">Populaire hashtags:</h3>
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="/?route=search&q=socialcore&type=hashtags" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">#socialcore</a>
                    <a href="/?route=search&q=hyves&type=hashtags" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">#hyves</a>
                    <a href="/?route=search&q=hashtag&type=hashtags" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">#hashtag</a>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>