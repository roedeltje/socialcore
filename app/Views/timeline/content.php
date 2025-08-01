<?php
/**
 * Timeline Content - Alleen de content zonder HTML structuur
 */

// Zorg dat we de juiste data hebben
$posts = $posts ?? [];
$currentUser = $currentUser ?? [];
$totalPosts = $totalPosts ?? 0;
$baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
?>

<!-- Timeline Container -->
<div class="timeline-container max-w-4xl mx-auto">
    
    <!-- Timeline Header Info -->
    <div class="timeline-info bg-gray-800 rounded-lg p-4 mb-6">
        <h1 class="text-2xl font-bold text-white mb-2">SocialCore Timeline</h1>
        <div class="timeline-stats">
            <span class="text-gray-400"><?= number_format($totalPosts) ?> berichten</span>
        </div>
    </div>

    <!-- Post Form Section -->
    <section class="post-form-section bg-gray-800 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-white mb-4">Nieuw bericht plaatsen</h2>
        <form id="core-post-form" action="<?= $baseUrl ?>/?route=feed/create" method="POST" enctype="multipart/form-data">
            <div class="form-group mb-4">
                <textarea 
                    id="post-content" 
                    name="content" 
                    placeholder="Waar denk je aan?"
                    maxlength="1000"
                    rows="3"
                    class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-blue-500 focus:outline-none resize-none"
                    required></textarea>
                <div class="char-counter text-right mt-2">
                    <span id="char-count" class="text-gray-400">0</span><span class="text-gray-500">/1000</span>
                </div>
            </div>
            
            <div class="form-actions flex items-center justify-between">
                <div class="media-upload flex items-center space-x-4">
                    <input type="file" id="post-image" name="image" accept="image/*" style="display: none;">
                    <button type="button" onclick="document.getElementById('post-image').click()" class="btn-media bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        📷 Foto toevoegen
                    </button>
                    <div id="image-preview" class="image-preview" style="display: none;">
                        <img id="preview-img" src="" alt="Preview" class="h-20 rounded border">
                        <button type="button" onclick="removeImage()" class="remove-image ml-2 text-red-400 hover:text-red-300">×</button>
                    </div>
                </div>

                <!-- Emoji Picker -->
                <div class="emoji-picker relative">
                    <button type="button" id="emoji-button" class="btn-media bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        😀 Emoji's
                    </button>
                    <div id="emoji-panel" class="emoji-panel absolute right-0 mt-2 bg-gray-800 border border-gray-600 rounded-lg shadow-xl z-50 w-80 max-h-96 overflow-y-auto" style="display: none;">
                        <div class="emoji-categories p-4">
                            <!-- Gezichten & Emoties -->
                            <div class="emoji-category mb-4">
                                <h4 class="text-white text-sm font-medium mb-2">😊 Gezichten</h4>
                                <div class="emoji-grid grid grid-cols-8 gap-1">
                                    <span onclick="insertEmoji('😀')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😀</span>
                                    <span onclick="insertEmoji('😃')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😃</span>
                                    <span onclick="insertEmoji('😄')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😄</span>
                                    <span onclick="insertEmoji('😁')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😁</span>
                                    <span onclick="insertEmoji('😆')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😆</span>
                                    <span onclick="insertEmoji('😅')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😅</span>
                                    <span onclick="insertEmoji('😂')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😂</span>
                                    <span onclick="insertEmoji('🤣')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🤣</span>
                                    <span onclick="insertEmoji('😊')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😊</span>
                                    <span onclick="insertEmoji('😇')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😇</span>
                                    <span onclick="insertEmoji('🙂')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🙂</span>
                                    <span onclick="insertEmoji('😉')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😉</span>
                                    <span onclick="insertEmoji('😍')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😍</span>
                                    <span onclick="insertEmoji('🥰')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🥰</span>
                                    <span onclick="insertEmoji('😘')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😘</span>
                                    <span onclick="insertEmoji('😎')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">😎</span>
                                </div>
                            </div>

                            <!-- Harten -->
                            <div class="emoji-category mb-4">
                                <h4 class="text-white text-sm font-medium mb-2">❤️ Liefde</h4>
                                <div class="emoji-grid grid grid-cols-8 gap-1">
                                    <span onclick="insertEmoji('❤️')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">❤️</span>
                                    <span onclick="insertEmoji('🧡')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🧡</span>
                                    <span onclick="insertEmoji('💛')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">💛</span>
                                    <span onclick="insertEmoji('💚')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">💚</span>
                                    <span onclick="insertEmoji('💙')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">💙</span>
                                    <span onclick="insertEmoji('💜')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">💜</span>
                                    <span onclick="insertEmoji('🖤')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🖤</span>
                                    <span onclick="insertEmoji('🤍')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🤍</span>
                                </div>
                            </div>

                            <!-- Gebaren -->
                            <div class="emoji-category mb-4">
                                <h4 class="text-white text-sm font-medium mb-2">👍 Gebaren</h4>
                                <div class="emoji-grid grid grid-cols-8 gap-1">
                                    <span onclick="insertEmoji('👍')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">👍</span>
                                    <span onclick="insertEmoji('👎')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">👎</span>
                                    <span onclick="insertEmoji('👌')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">👌</span>
                                    <span onclick="insertEmoji('✌️')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">✌️</span>
                                    <span onclick="insertEmoji('🤞')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🤞</span>
                                    <span onclick="insertEmoji('👋')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">👋</span>
                                    <span onclick="insertEmoji('👏')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">👏</span>
                                    <span onclick="insertEmoji('🙌')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🙌</span>
                                </div>
                            </div>

                            <!-- Objecten -->
                            <div class="emoji-category">
                                <h4 class="text-white text-sm font-medium mb-2">🔥 Objecten</h4>
                                <div class="emoji-grid grid grid-cols-8 gap-1">
                                    <span onclick="insertEmoji('🔥')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🔥</span>
                                    <span onclick="insertEmoji('💯')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">💯</span>
                                    <span onclick="insertEmoji('✨')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">✨</span>
                                    <span onclick="insertEmoji('🎉')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🎉</span>
                                    <span onclick="insertEmoji('🎊')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🎊</span>
                                    <span onclick="insertEmoji('🎈')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🎈</span>
                                    <span onclick="insertEmoji('🚀')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">🚀</span>
                                    <span onclick="insertEmoji('⭐')" class="emoji-item cursor-pointer hover:bg-gray-700 p-1 rounded text-lg">⭐</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" id="submit-btn" class="btn-primary bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Plaatsen
                </button>
            </div>
        </form>
    </section>

    <!-- Posts Container -->
    <main class="timeline-main">
        <div class="posts-container space-y-6">
            <?php if (empty($posts)): ?>
                <!-- Empty state -->
                <div class="empty-timeline bg-gray-800 rounded-lg p-8 text-center">
                    <div class="empty-icon text-4xl mb-4">📝</div>
                    <h3 class="text-xl font-semibold text-white mb-2">Nog geen berichten</h3>
                    <p class="text-gray-400">Wees de eerste om iets te delen!</p>
                </div>
            <?php else: ?>
                <!-- Posts list -->
                <?php foreach ($posts as $post): ?>
                    <article class="timeline-post bg-gray-800 rounded-lg p-6" data-post-id="<?= $post['id'] ?>">
                        <!-- Post header -->
                        <header class="post-header flex items-center justify-between mb-4">
                            <div class="post-author flex items-center space-x-3">
                                <img src="<?= $post['author_avatar_url'] ?? ($baseUrl . '/public/theme-assets/default/images/default-avatar.png') ?>" 
                                     alt="<?= htmlspecialchars($post['author_name'] ?? 'Gebruiker') ?>"
                                     class="author-avatar w-12 h-12 rounded-full border-2 border-gray-600">
                                <div class="author-info">
                                    <h4 class="author-name">
                                        <a href="<?= $baseUrl ?>/?route=profile&user=<?= $post['author_username'] ?>" 
                                           class="text-white font-medium hover:text-blue-400 transition-colors">
                                            <?= htmlspecialchars($post['author_name'] ?? $post['author_username']) ?>
                                        </a>
                                    </h4>
                                    <time class="post-time text-gray-400 text-sm" datetime="<?= $post['created_at'] ?>">
                                        <?= $post['time_ago'] ?? date('d-m-Y H:i', strtotime($post['created_at'])) ?>
                                    </time>
                                </div>
                            </div>
                            
                            <!-- Post actions dropdown -->
                            <?php if (isset($_SESSION['user_id']) && 
                                      ($_SESSION['user_id'] == $post['user_id'] || ($_SESSION['role'] ?? '') === 'admin')): ?>
                            <div class="post-menu relative">
                                <button class="menu-toggle text-gray-400 hover:text-white p-2 rounded" onclick="togglePostMenu(<?= $post['id'] ?>)">⋯</button>
                                <div class="menu-dropdown absolute right-0 mt-2 bg-gray-700 rounded-lg shadow-xl border border-gray-600 z-50" id="menu-<?= $post['id'] ?>" style="display: none;">
                                    <a href="#" onclick="deletePost(<?= $post['id'] ?>); return false;" class="menu-item flex items-center px-4 py-2 text-red-400 hover:text-red-300 hover:bg-gray-600 transition-colors">
                                        🗑️ Verwijderen
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </header>

                        <!-- Post content -->
                        <div class="post-content">
                            <div class="post-text text-white mb-4">
                                <?= nl2br(htmlspecialchars($post['content'])) ?>
                            </div>
                            
                            <!-- Post image -->
                            <?php if (!empty($post['image_url'])): ?>
                            <div class="post-media mb-4">
                                <img src="<?= $post['image_url'] ?>" 
                                    alt="Geplaatste afbeelding" 
                                    class="post-image w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openImageModal('<?= $post['image_url'] ?>')">
                            </div>
                            <?php endif; ?>

                            <!-- Link Preview Section -->
                            <?php if (!empty($post['link_preview_id']) && !empty($post['preview_url'])): ?>
                            <div class="link-preview bg-gray-700 border border-gray-600 rounded-lg p-4 mb-4">
                                <a href="<?= htmlspecialchars($post['preview_url']) ?>" target="_blank" class="block hover:bg-gray-600 transition-colors rounded">
                                    <?php if (!empty($post['preview_image'])): ?>
                                    <img src="<?= htmlspecialchars($post['preview_image']) ?>" alt="Link preview" class="w-full h-48 object-cover rounded mb-3">
                                    <?php endif; ?>
                                    <h4 class="text-white font-medium mb-1"><?= htmlspecialchars($post['preview_title'] ?? $post['preview_url']) ?></h4>
                                    <?php if (!empty($post['preview_description'])): ?>
                                    <p class="text-gray-300 text-sm mb-2"><?= htmlspecialchars($post['preview_description']) ?></p>
                                    <?php endif; ?>
                                    <span class="text-blue-400 text-sm"><?= htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST)) ?></span>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post footer -->
                        <footer class="post-footer border-t border-gray-600 pt-4">
                            <div class="post-actions flex items-center space-x-6">
                                <button class="action-btn like-btn flex items-center space-x-2 text-gray-400 hover:text-blue-400 transition-colors" 
                                        data-post-id="<?= $post['id'] ?>"
                                        onclick="toggleLike(<?= $post['id'] ?>)">
                                    <span class="like-icon">👍</span>
                                    <span class="like-count"><?= $post['likes_count'] ?? 0 ?></span>
                                    <span class="like-text">Respect</span>
                                </button>
                                
                                <button class="action-btn comment-btn flex items-center space-x-2 text-gray-400 hover:text-green-400 transition-colors" 
                                        onclick="toggleComments(<?= $post['id'] ?>)">
                                    <span class="comment-icon">💬</span>
                                    <span class="comment-count"><?= $post['comments_count'] ?? 0 ?></span>
                                    <span class="comment-text">Reacties</span>
                                </button>
                                
                                <button class="action-btn share-btn flex items-center space-x-2 text-gray-400 hover:text-purple-400 transition-colors">
                                    <span class="share-icon">📤</span>
                                    <span class="share-text">Delen</span>
                                </button>
                            </div>
                        </footer>

                        <!-- Comments section (initially hidden) -->
                        <section class="comments-section mt-4 pt-4 border-t border-gray-600" id="comments-<?= $post['id'] ?>" style="display: none;">
                            <div class="comments-list mb-4">
                                <!-- Comments will be loaded here via AJAX -->
                            </div>
                            
                            <!-- Comment form -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <form class="comment-form" onsubmit="submitComment(event, <?= $post['id'] ?>)">
                                <div class="comment-input-group flex items-center space-x-3">
                                    <img src="<?= $currentUser['avatar_url'] ?? ($baseUrl . '/public/theme-assets/default/images/default-avatar.png') ?>" 
                                         alt="Jouw avatar" class="comment-avatar w-8 h-8 rounded-full">
                                    <input type="text" 
                                           placeholder="Schrijf een reactie..." 
                                           name="comment" 
                                           class="comment-input flex-1 bg-gray-700 text-white rounded-lg px-4 py-2 border border-gray-600 focus:border-blue-500 focus:outline-none"
                                           maxlength="500">
                                    <button type="submit" class="comment-submit bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        Verstuur
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </section>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Load more button -->
        <?php if (count($posts) >= 20): ?>
        <div class="load-more-container text-center mt-8">
            <button id="load-more-btn" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors" onclick="loadMorePosts()">
                Meer berichten laden
            </button>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- Image modal -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none;" onclick="closeImageModal()">
    <div class="modal-content relative max-w-4xl max-h-4xl">
        <img id="modal-image" src="" alt="Volledige afbeelding" class="max-w-full max-h-full rounded">
        <button class="modal-close absolute top-4 right-4 text-white text-3xl hover:text-gray-300" onclick="closeImageModal()">×</button>
    </div>
</div>

<!-- JavaScript -->
<script>
// Character counter
document.getElementById('post-content').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('char-count').textContent = count;
    
    if (count > 900) {
        document.getElementById('char-count').classList.add('text-red-400');
        document.getElementById('char-count').classList.remove('text-gray-400');
    } else {
        document.getElementById('char-count').classList.add('text-gray-400');
        document.getElementById('char-count').classList.remove('text-red-400');
    }
});

// Image preview
document.getElementById('post-image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('post-image').value = '';
    document.getElementById('image-preview').style.display = 'none';
}

// Emoji functionality
document.getElementById('emoji-button').addEventListener('click', function(e) {
    e.preventDefault();
    const panel = document.getElementById('emoji-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
});

function insertEmoji(emoji) {
    const textarea = document.getElementById('post-content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const before = text.substring(0, start);
    const after = text.substring(end, text.length);
    
    textarea.value = before + emoji + after;
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    textarea.focus();
    
    // Update character count
    const event = new Event('input');
    textarea.dispatchEvent(event);
    
    // Hide emoji panel
    document.getElementById('emoji-panel').style.display = 'none';
}

// Close emoji panel when clicking outside
document.addEventListener('click', function(e) {
    const emojiButton = document.getElementById('emoji-button');
    const emojiPanel = document.getElementById('emoji-panel');
    
    if (!emojiButton.contains(e.target) && !emojiPanel.contains(e.target)) {
        emojiPanel.style.display = 'none';
    }
});

// Post menu toggle
function togglePostMenu(postId) {
    const menu = document.getElementById('menu-' + postId);
    const isVisible = menu.style.display !== 'none';
    
    // Hide all open menus
    document.querySelectorAll('.menu-dropdown').forEach(m => m.style.display = 'none');
    
    // Show this menu if it wasn't visible
    if (!isVisible) {
        menu.style.display = 'block';
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.post-menu')) {
        document.querySelectorAll('.menu-dropdown').forEach(m => m.style.display = 'none');
    }
});

// Like functionality
async function toggleLike(postId) {
    try {
        const response = await fetch('<?= $baseUrl ?>/?route=feed/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        });
        
        const result = await response.json();
        if (result.success) {
            const likeBtn = document.querySelector(`[data-post-id="${postId}"]`);
            const countSpan = likeBtn.querySelector('.like-count');
            countSpan.textContent = result.likes_count;
            
            if (result.liked) {
                likeBtn.classList.add('text-blue-400');
                likeBtn.classList.remove('text-gray-400');
            } else {
                likeBtn.classList.add('text-gray-400');
                likeBtn.classList.remove('text-blue-400');
            }
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    }
}

// Delete post
async function deletePost(postId) {
    if (confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
        try {
            const response = await fetch('<?= $baseUrl ?>/?route=feed/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            });
            
            const result = await response.json();
            if (result.success) {
                document.querySelector(`[data-post-id="${postId}"]`).remove();
            } else {
                alert('Er ging iets mis bij het verwijderen van het bericht.');
            }
        } catch (error) {
            console.error('Error deleting post:', error);
            alert('Er ging iets mis bij het verwijderen van het bericht.');
        }
    }
}

// Image modal
function openImageModal(imageSrc) {
    document.getElementById('modal-image').src = imageSrc;
    document.getElementById('image-modal').style.display = 'flex';
}

function closeImageModal() {
    document.getElementById('image-modal').style.display = 'none';
}

// Comments functionality
function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection.style.display === 'none') {
        commentsSection.style.display = 'block';
        // Load comments via AJAX here if needed
    } else {
        commentsSection.style.display = 'none';
    }
}

function submitComment(event, postId) {
    event.preventDefault();
    // Implement comment submission
    console.log('Submit comment for post:', postId);
}

// Form submission
document.getElementById('core-post-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Bezig...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            // Clear form
            this.reset();
            document.getElementById('char-count').textContent = '0';
            removeImage();
            
            // Reload page to show new post
            location.reload();
        } else {
            alert(result.message || 'Er ging iets mis bij het plaatsen van je bericht.');
        }
    } catch (error) {
        console.error('Error submitting post:', error);
        alert('Er ging iets mis bij het plaatsen van je bericht.');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});

console.log('🎯 Timeline content loaded successfully');
</script>