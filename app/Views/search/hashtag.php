<div class="max-w-4xl mx-auto p-6">
    
    <?php if ($hashtag): ?>
        <!-- Hashtag Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-hyves-blue mb-2">#<?= htmlspecialchars($hashtag['tag']) ?></h1>
                    <p class="text-gray-600"><?= $hashtag['usage_count'] ?> berichten</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Aangemaakt op</div>
                    <div class="text-sm text-gray-700"><?= date('j M Y', strtotime($hashtag['created_at'])) ?></div>
                </div>
            </div>
            
            <!-- Back to search -->
            <div class="mt-4 pt-4 border-t">
                <a href="/?route=search" class="text-hyves-blue hover:underline">
                    ← Terug naar zoeken
                </a>
            </div>
        </div>

        <!-- Posts met deze hashtag -->
        <?php if (!empty($posts)): ?>
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800">Berichten</h2>
                
                <?php foreach ($posts as $post): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <!-- Post header -->
                        <div class="flex items-center mb-4">
                            <img 
                                src="<?= $post['avatar_url'] ?>" 
                                alt="Avatar" 
                                class="w-12 h-12 rounded-full object-cover mr-3"
                            >
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <a href="/?route=profile&user=<?= $post['username'] ?>" 
                                       class="font-medium text-hyves-blue hover:underline">
                                        <?= htmlspecialchars($post['display_name'] ?: $post['username']) ?>
                                    </a>
                                    <span class="text-gray-500 text-sm">•</span>
                                    <span class="text-gray-500 text-sm"><?= $post['time_ago'] ?></span>
                                </div>
                                <div class="text-sm text-gray-500">@<?= htmlspecialchars($post['username']) ?></div>
                            </div>
                        </div>

                        <!-- Post content -->
                        <div class="mb-4">
                            <p class="text-gray-800 leading-relaxed">
                                <?php
                                // Highlight de hashtag in de content
                                $content = htmlspecialchars($post['content']);
                                $highlightedContent = preg_replace(
                                    '/\#' . preg_quote($hashtag['tag'], '/') . '\b/i', 
                                    '<span class="font-semibold text-hyves-blue">#' . $hashtag['tag'] . '</span>', 
                                    $content
                                );
                                echo $highlightedContent;
                                ?>
                            </p>
                        </div>

                        <!-- Post media (if any) -->
                        <?php if (!empty($post['media_url'])): ?>
                            <div class="mb-4">
                                <img 
                                    src="<?= $post['media_url'] ?>" 
                                    alt="Post afbeelding" 
                                    class="max-w-full h-auto rounded-lg"
                                >
                            </div>
                        <?php endif; ?>

                        <!-- Post actions -->
                        <div class="flex items-center space-x-6 pt-4 border-t border-gray-100">
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-hyves-blue">
                                <span>👍</span>
                                <span><?= $post['likes_count'] ?? 0 ?></span>
                                <span>Respect!</span>
                            </button>
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-hyves-blue">
                                <span>💬</span>
                                <span>Reacties</span>
                            </button>
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-hyves-blue">
                                <span>🔗</span>
                                <span>Delen</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Geen berichten gevonden -->
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="text-gray-500 text-6xl mb-4">📝</div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Geen berichten gevonden</h3>
                <p class="text-gray-500">Er zijn nog geen berichten met de hashtag #<?= htmlspecialchars($hashtag['tag']) ?>.</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Hashtag niet gevonden -->
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="text-gray-500 text-6xl mb-4">🤷‍♂️</div>
            <h1 class="text-2xl font-bold text-gray-700 mb-2">Hashtag niet gevonden</h1>
            <p class="text-gray-500 mb-4">De hashtag #<?= htmlspecialchars($tag) ?> bestaat niet.</p>
            <a href="/?route=search" class="inline-block px-6 py-2 text-white rounded-lg hover:bg-blue-700" style="background-color: #0f3ea3;">
                Terug naar zoeken
            </a>
        </div>
    <?php endif; ?>

</div>