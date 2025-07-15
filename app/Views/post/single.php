<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Bericht') ?> - SocialCore</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <style>
        .post-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .post-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .post-header {
            background: #f3f4f6;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .post-content {
            padding: 20px;
        }
        
        .comment-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .avatar-large {
            width: 60px;
            height: 60px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .post-media img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            margin-bottom: 16px;
        }
        
        .success-message {
            background: #f0fdf4;
            color: #16a34a;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #bbf7d0;
            margin-bottom: 16px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="post-container">
        <!-- Navigation terug -->
        <div class="mb-4">
            <a href="<?= base_url() ?>" class="btn btn-secondary">
                ← Terug naar tijdlijn
            </a>
            <?php if (isset($post_owner) && $post_owner): ?>
                <a href="<?= base_url('?route=profile&user=' . urlencode($post_owner['username'])) ?>" class="btn btn-secondary ml-2">
                    👤 Profiel van <?= htmlspecialchars($post_owner['display_name'] ?? $post_owner['username']) ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- Success/Error berichten -->
        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success) && $success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Post Card -->
        <?php if (isset($post) && $post): ?>
            <div class="post-card mb-6">
                <!-- Post Header -->
                <div class="post-header">
                    <div class="flex items-center">
                        <img src="<?= $post['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                             alt="<?= htmlspecialchars($post['user_name'] ?? 'Gebruiker') ?>" 
                             class="avatar avatar-large mr-4">
                        <div class="flex-grow">
                            <div class="font-bold text-lg text-gray-900">
                                <a href="<?= base_url('?route=profile&user=' . urlencode($post['username'])) ?>" 
                                   class="hover:text-blue-600">
                                    <?= htmlspecialchars($post['user_name'] ?? $post['username']) ?>
                                </a>
                            </div>
                            <div class="text-gray-600 text-sm">
                                <?= $post['created_at_formatted'] ?? 'Onbekende tijd' ?>
                            </div>
                            <?php if ($post['visibility'] !== 'public'): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    🔒 <?= ucfirst($post['visibility'] ?? 'public') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Edit/Delete buttons voor eigenaar -->
                        <?php if (isset($can_edit) && $can_edit): ?>
                            <div class="ml-4">
                                <button class="text-gray-500 hover:text-gray-700 p-2" onclick="togglePostMenu(<?= $post['id'] ?>)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <div id="post-menu-<?= $post['id'] ?>" class="hidden absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                    <?php if (isset($can_delete) && $can_delete): ?>
                                        <a href="#" onclick="deletePost(<?= $post['id'] ?>)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            🗑️ Verwijderen
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="post-content">
                    <!-- Text content -->
                    <?php if (!empty($post['content'])): ?>
                        <div class="mb-4 text-gray-800 leading-relaxed">
                            <?= $post['content_formatted'] ?>
                        </div>
                    <?php endif; ?>

                    <!-- Media content -->
                    <?php if (!empty($post['media_url'])): ?>
                        <div class="post-media mb-4">
                            <?php if ($post['media_type'] === 'image'): ?>
                                <img src="<?= $post['media_url'] ?>" 
                                     alt="Post afbeelding" 
                                     class="rounded-lg border max-w-full h-auto">
                            <?php elseif ($post['media_type'] === 'video'): ?>
                                <video controls class="rounded-lg border max-w-full h-auto">
                                    <source src="<?= $post['media_url'] ?>" type="video/mp4">
                                    Je browser ondersteunt deze video niet.
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <!-- Like button -->
                            <button class="like-button flex items-center space-x-2 text-gray-600 hover:text-blue-600 <?= isset($post['is_liked']) && $post['is_liked'] ? 'text-blue-600' : '' ?>" 
                                    data-post-id="<?= $post['id'] ?>">
                                <span class="text-lg">👍</span>
                                <span class="like-count"><?= $post['likes'] ?? 0 ?></span>
                                <span>Respect</span>
                            </button>
                            
                            <!-- Comment count -->
                            <div class="flex items-center space-x-2 text-gray-600">
                                <span class="text-lg">💬</span>
                                <span><?= count($comments ?? []) ?> Reacties</span>
                            </div>
                        </div>
                        
                        <!-- Share button -->
                        <button onclick="sharePost(<?= $post['id'] ?>)" class="flex items-center space-x-2 text-gray-600 hover:text-green-600">
                            <span class="text-lg">📤</span>
                            <span>Delen</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Comments sectie -->
            <div class="post-card">
                <div class="post-header">
                    <h3 class="font-bold text-lg">Reacties (<?= count($comments ?? []) ?>)</h3>
                </div>
                
                <div class="post-content">
                    <!-- Comment form -->
                    <?php if (isset($can_comment) && $can_comment && $current_user): ?>
                        <form class="mb-6 bg-gray-50 p-4 rounded-lg" method="post" action="<?= base_url('?route=comments/add') ?>">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <div class="flex items-start space-x-3">
                                <img src="<?= $current_user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                     alt="Je avatar" 
                                     class="avatar">
                                <div class="flex-grow">
                                    <textarea name="content" 
                                              placeholder="Schrijf een reactie..." 
                                              class="w-full p-3 border border-gray-300 rounded-lg resize-none"
                                              rows="2" 
                                              required></textarea>
                                    <div class="mt-2 flex justify-end">
                                        <button type="submit" class="btn btn-primary">
                                            Reageren
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php elseif (!$current_user): ?>
                        <div class="mb-6 text-center py-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-600 mb-2">Log in om te reageren</p>
                            <a href="<?= base_url('?route=auth/login') ?>" class="btn btn-primary">
                                Inloggen
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Comments lijst -->
                    <?php if (!empty($comments)): ?>
                        <div class="space-y-4">
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-card">
                                    <div class="flex items-start space-x-3">
                                        <img src="<?= $comment['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                             alt="<?= htmlspecialchars($comment['commenter_name'] ?? 'Gebruiker') ?>" 
                                             class="avatar">
                                        <div class="flex-grow">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium text-gray-900">
                                                    <a href="<?= base_url('?route=profile&user=' . urlencode($comment['username'])) ?>" 
                                                       class="hover:text-blue-600">
                                                        <?= htmlspecialchars($comment['commenter_name']) ?>
                                                    </a>
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    <?= $comment['created_at_formatted'] ?>
                                                </span>
                                            </div>
                                            <div class="text-gray-800">
                                                <?= $comment['content_formatted'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">💬</div>
                            <p>Nog geen reacties. Wees de eerste!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Post niet gevonden -->
            <div class="post-card text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Bericht niet gevonden</h2>
                <p class="text-gray-600 mb-4">Dit bericht bestaat niet of je hebt geen toegang.</p>
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    Terug naar tijdlijn
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript -->
    <script>
        // Like functionality
        document.addEventListener('DOMContentLoaded', function() {
            const likeButtons = document.querySelectorAll('.like-button');
            
            likeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    toggleLike(postId, this);
                });
            });
        });
        
        function toggleLike(postId, button) {
            button.disabled = true;
            
            fetch('<?= base_url("feed/like") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeCount = button.querySelector('.like-count');
                    likeCount.textContent = data.likes;
                    
                    // Toggle liked state
                    if (data.liked) {
                        button.classList.add('text-blue-600');
                        button.classList.remove('text-gray-600');
                    } else {
                        button.classList.remove('text-blue-600');
                        button.classList.add('text-gray-600');
                    }
                } else {
                    alert(data.message || 'Er ging iets mis');
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                alert('Er ging iets mis bij het liken');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
        
        function togglePostMenu(postId) {
            const menu = document.getElementById(`post-menu-${postId}`);
            menu.classList.toggle('hidden');
        }
        
        function deletePost(postId) {
            if (confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("feed/delete") ?>';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'post_id';
                input.value = postId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function sharePost(postId) {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Bekijk dit bericht op SocialCore',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert('Link gekopieerd naar klembord!');
                });
            }
        }
        
        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.post-menu')) {
                document.querySelectorAll('[id^="post-menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>