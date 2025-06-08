<?php /* SocialCore nieuwsfeed in Hyves-stijl */ ?>
<?php echo "<!-- Timeline.php geladen -->"; ?>
<div class="feed-container">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Linker zijbalk (ongewijzigd) -->
        <div class="w-full lg:w-1/4">
            <!-- Gebruikerskaart (ongewijzigd) -->
            <div class="user-card bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <!-- Inhoud ongewijzigd -->
                <div class="bg-blue-100 p-4 border-b border-blue-200">
                    <h2 class="text-xl font-bold text-blue-800"><?= htmlspecialchars($current_user['name']) ?></h2>
                    <p class="text-sm text-blue-600">@<?= htmlspecialchars($current_user['username']) ?></p>
                </div>
                <div class="p-4">
                    <div class="flex items-center space-x-4">
                        <img src="<?= $current_user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                             alt="<?= htmlspecialchars($current_user['name']) ?>" 
                             class="w-16 h-16 rounded-full border-2 border-blue-200">
                        <div>
                            <a href="<?= base_url('profile') ?>" class="hyves-button bg-blue-500 hover:bg-blue-600 text-sm px-3 py-1">
                                Ga naar profiel
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4 text-center">
                        <div class="stats-item">
                            <div class="font-bold text-blue-800"><?= $current_user['post_count'] ?? 0 ?></div>
                            <div class="text-xs text-gray-500">Posts</div>
                        </div>
                        <div class="stats-item">
                            <div class="font-bold text-blue-800"><?= $current_user['following'] ?? 0 ?></div>
                            <div class="text-xs text-gray-500">Volgend</div>
                        </div>
                        <div class="stats-item">
                            <div class="font-bold text-blue-800"><?= $current_user['followers'] ?? 0 ?></div>
                            <div class="text-xs text-gray-500">Volgers</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigatie menu in Hyves-stijl (ongewijzigd) -->
            <div class="hyves-menu bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Inhoud ongewijzigd -->
                <div class="hyves-menu-header bg-blue-100 p-3 border-b border-blue-200">
                    <h3 class="font-bold text-blue-800">Menu</h3>
                </div>
                <div class="hyves-menu-items">
                    <a href="<?= base_url('') ?>" class="hyves-menu-item active">
                        <span class="icon">🏠</span>
                        <span class="text">Nieuwsfeed</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="hyves-menu-item">
                        <span class="icon">👤</span>
                        <span class="text">Mijn profiel</span>
                    </a>
                    <a href="<?= base_url('messages') ?>" class="hyves-menu-item">
                        <span class="icon">✉️</span>
                        <span class="text">Berichten</span>
                    </a>
                    <a href="<?= base_url('profile/photos') ?>" class="hyves-menu-item">
                        <span class="icon">📷</span>
                        <span class="text">Foto's</span>
                    </a>
                    <a href="<?= base_url('friends') ?>" class="hyves-menu-item">
                        <span class="icon">👥</span>
                        <span class="text">Vrienden</span>
                    </a>
                    <a href="<?= base_url('games') ?>" class="hyves-menu-item">
                        <span class="icon">🎮</span>
                        <span class="text">Games</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Midden gedeelte - Posts feed -->
        <div class="w-full lg:w-2/4">
            <!-- Post creator - BIJGEWERKT MET AFBEELDING UPLOAD -->
            <div class="post-composer bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-blue-100 p-3 border-b border-blue-200">
                    <h3 class="font-bold text-blue-800">Plaats een bericht</h3>
                </div>
                <div class="p-4">
                    
                    <?php include THEME_PATH . '/partials/messages.php'; ?>
                    
                    <!-- Formulier aangepast naar de juiste route -->
                    <?php 
                        $form_id = 'postForm';
                        $context = 'timeline';
                        $user = $current_user;
                        include __DIR__ . '/../partials/post-form.php';
                        ?>
                </div>
            </div>
            
            <!-- Posts feed - BIJGEWERKT OM AFBEELDINGEN TE TONEN -->
            <?php foreach ($posts as $post): ?>
                <div class="post-card bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                    <div class="post-header flex justify-between items-center bg-blue-100 p-3 border-b border-blue-200">
                        <div class="flex items-center space-x-3">
                            <img src="<?= $post['avatar'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                alt="<?= htmlspecialchars($post['user_name']) ?>" 
                                class="w-8 h-8 rounded-full border-2 border-blue-200">
                            <div>
                                <?php if ($post['is_wall_message'] && !empty($post['wall_message_header'])): ?>
                                    <!-- Wall message header: Sender → Receiver -->
                                    <a href="<?= base_url('profile/' . $post['user_id']) ?>" class="font-bold text-blue-800 hover:underline">
                                        <?= htmlspecialchars($post['wall_message_header']) ?>
                                    </a>
                                    <div class="text-xs text-blue-600">plaatste een krabbel • <?= $post['created_at'] ?></div>
                                <?php else: ?>
                                    <!-- Regular timeline post -->
                                    <a href="<?= base_url('profile/' . $post['user_id']) ?>" class="font-bold text-blue-800 hover:underline">
                                        <?= htmlspecialchars($post['user_name']) ?>
                                    </a>
                                    <div class="text-xs text-blue-600"><?= $post['created_at'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && ($post['user_id'] == $_SESSION['user_id'] || isset($_SESSION['role']) && $_SESSION['role'] === 'admin')): ?>
                                <div class="relative post-menu">
                                    <button type="button" class="post-menu-button text-blue-600 hover:text-blue-800 p-1 rounded-full">
                                        <span class="icon">⋯</span>
                                    </button>
                                    <div class="post-menu-dropdown absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                                        <form action="<?= base_url('feed/delete') ?>" method="post" class="delete-post-form">
                                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                            <button type="button" class="delete-post-button block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Bericht verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Toon de drie puntjes voor niet-eigenaars, maar zonder menu functionaliteit -->
                                <button class="text-blue-600 hover:text-blue-800">
                                    <span class="icon">⋯</span>
                                </button>
                            <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="post-content mb-4">
                            <?php if (!empty($post['content'])): ?>
                                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            <?php endif; ?>
                            
                            <!-- Afbeelding weergave - NIEUW -->
                            <?php if (!empty($post['media_path'])): ?>
                                <div class="mt-3 post-media rounded-lg overflow-hidden border border-blue-200">
                                    <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                                         alt="Post afbeelding" 
                                         class="w-full h-auto max-h-96 object-contain">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Post actions -->
                        <div class="flex justify-between text-sm border-t border-blue-100 pt-3">
                            <button class="hyves-action-button like-button <?= $post['is_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
                                <span class="like-icon">👍</span>
                                <span class="text"><span class="like-count"><?= $post['likes'] ?></span> Respect!</span>
                            </button>
                            <button class="hyves-action-button">
                                <span class="icon">💬</span>
                                <span class="text"><?= $post['comments'] ?> Reacties</span>
                            </button>
                            <button class="hyves-action-button">
                                <span class="icon">🔄</span>
                                <span class="text">Delen</span>
                            </button>
                        </div>

                        <?php 
                        // Bereid variabelen voor comments sectie
                        $comments_data = [
                            'post_id' => $post['id'],
                            'comments_list' => $post['comments_list'] ?? [],
                            'current_user' => $current_user,
                            'show_comment_form' => true,
                            'show_likes' => true
                        ];

                        // Include de comments sectie partial
                        extract($comments_data);
                        include THEME_PATH . '/partials/comments-section.php';
                        ?>

                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-xl font-bold text-blue-800 mb-2">Nog geen berichten</h3>
                    <p class="text-gray-600">Begin met het volgen van vrienden of plaats je eerste bericht!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Rechter zijbalk (ongewijzigd) -->
        <div class="w-full lg:w-1/4">
            <!-- Inhoud ongewijzigd -->
            <!-- Online vrienden -->
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-blue-100 p-3 border-b border-blue-200 flex justify-between items-center">
                    <h3 class="font-bold text-blue-800">Online vrienden</h3>
                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        <?= count($online_friends) ?>
                    </span>
                </div>
                <div class="p-4">
                    <?php if (empty($online_friends)): ?>
                        <div class="text-center text-gray-500 py-2">
                            Geen vrienden online
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($online_friends as $friend): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="relative">
                                            <img src="<?= base_url('theme-assets/default/images/' . (strpos($friend['name'], 'Lucas') !== false ? 
                                                                            'default-avatar-male.png' : 
                                                                            (strpos($friend['name'], 'Emma') !== false || strpos($friend['name'], 'Sophie') !== false ? 
                                                                             'default-avatar-female.png' : 'default-avatar.png'))) ?>" 
                                                 alt="<?= htmlspecialchars($friend['name']) ?>" 
                                                 class="w-8 h-8 rounded-full border border-blue-200">
                                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                        </div>
                                        <a href="<?= base_url('profile/' . $friend['id']) ?>" class="text-sm font-medium text-blue-800 hover:underline">
                                            <?= htmlspecialchars($friend['name']) ?>
                                        </a>
                                    </div>
                                    <a href="<?= base_url('messages/chat/' . $friend['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <span class="icon">✉️</span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Trending hashtags -->
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-blue-100 p-3 border-b border-blue-200">
                    <h3 class="font-bold text-blue-800">Populair op SocialCore</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <?php foreach ($trending_hashtags as $hashtag): ?>
                            <div class="flex items-center space-x-2">
                                <span class="text-red-500 text-lg">📈</span>
                                <div>
                                    <a href="<?= base_url('search?q=%23' . $hashtag['tag']) ?>" class="text-sm font-medium text-blue-800 hover:underline">
                                        #<?= htmlspecialchars($hashtag['tag']) ?>
                                    </a>
                                    <div class="text-xs text-gray-500"><?= number_format($hashtag['count']) ?> posts</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Suggesties voor vrienden -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-100 p-3 border-b border-blue-200">
                    <h3 class="font-bold text-blue-800">Mensen die je misschien kent</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <?php foreach ($suggested_users as $user): ?>
                            <div class="text-center">
                                <a href="<?= base_url('profile/' . $user['id']) ?>" class="block">
                                    <img src="<?= base_url('theme-assets/default/images/' . (strpos($user['name'], 'Tim') !== false || 
                                                                    strpos($user['name'], 'Robin') !== false ? 
                                                                    'default-avatar-male.png' : 
                                                                    (strpos($user['name'], 'Nina') !== false || strpos($user['name'], 'Laura') !== false ? 
                                                                     'default-avatar-female.png' : 'default-avatar.png'))) ?>" 
                                         alt="<?= htmlspecialchars($user['name']) ?>" 
                                         class="w-12 h-12 mx-auto rounded-full border-2 border-blue-200">
                                    <div class="mt-1 text-sm font-medium text-blue-800 truncate">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </div>
                                </a>
                                <button class="hyves-button bg-blue-500 hover:bg-blue-600 text-xs mt-1 py-1 px-2">
                                    + Volgen
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Clear old content na gebruik
if (isset($_SESSION['old_content'])) {
    unset($_SESSION['old_content']);
}
?>
<script>
// Op de nieuwsfeed pagina:
console.log('=== NIEUWSFEED DEBUG ===');
console.log('Post forms:', document.querySelectorAll('.add-comment-form').length);
console.log('Emoji triggers:', document.querySelectorAll('.emoji-picker-trigger').length);
console.log('Emoji panels:', document.querySelectorAll('.emoji-picker-panel').length);

// Kijk naar de parent containers
document.querySelectorAll('.emoji-picker-panel').forEach((panel, index) => {
    console.log(`Panel ${index}:`, panel);
    console.log(`Panel ${index} parent:`, panel.parentElement);
    console.log(`Panel ${index} computed style:`, window.getComputedStyle(panel));
});
</script>