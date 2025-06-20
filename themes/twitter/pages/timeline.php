<?php
echo "<!-- EMERGENCY DEBUG: " . date('Y-m-d H:i:s') . " - File loaded! -->";

?>
<?php echo "<!-- DEBUG: Dit is het TWITTER thema timeline.php bestand! -->"; ?>
<?php
/**
 * Twitter Theme - Timeline Page Template (Consistent Rebuild)
 * Nu met dezelfde structuur als profile.php met partials
 */

// Haal posts en gebruikersgegevens op
$posts = $data['posts'] ?? [];
$currentUser = $data['current_user'] ?? null;

// ✅ VERBETERDE AVATAR URL LOGICA (zelfde als profile.php)
function getCorrectAvatarUrl($userAvatar) {
    // Als er geen avatar is, gebruik default
    if (empty($userAvatar)) {
        return base_url('theme-assets/twitter/images/default-avatar.png');
    }
    
    // Als het al een volledige URL is
    if (str_starts_with($userAvatar, 'http')) {
        return $userAvatar;
    }
    
    // Als het een theme asset is (bijv. "theme-assets/default/images/default-avatar.png")
    if (str_starts_with($userAvatar, 'theme-assets')) {
        return base_url($userAvatar);
    }
    
    // Voor uploads - gebruik de uploads map
    // Avatar path is bijv: "avatars/2025/05/avatar_1_68348a9ba26262.13561588.jpg"
    $uploadPath = 'uploads/' . ltrim($userAvatar, '/');
    $fullServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
    
    // Check of bestand bestaat op server
    if (file_exists($fullServerPath)) {
        return base_url($uploadPath);
    }
    
    // Fallback naar default avatar
    return base_url('theme-assets/twitter/images/default-avatar.png');
}

// Current user avatar voor compose box
$currentUserAvatar = isset($_SESSION['avatar']) ? 
    getCorrectAvatarUrl($_SESSION['avatar']) : 
    base_url('theme-assets/twitter/images/default-avatar.png');

// Helper function voor tijd geleden (behouden)
function timeAgo($datetime) {
    // DEBUG: Log wat er binnenkomt
    echo "<!-- DEBUG timeAgo input: '" . $datetime . "' -->";
    
    if (empty($datetime)) return 'onbekend';
    
    // Als het al verwerkt is (bevat "geleden"), geef het terug
    if (strpos($datetime, 'geleden') !== false || strpos($datetime, 'nu') !== false) {
        echo "<!-- DEBUG: Already processed, returning as-is -->";
        return $datetime;
    }
    
    try {
        $date = new DateTime($datetime);
        $now = new DateTime();
        $time = $now->getTimestamp() - $date->getTimestamp();
    } catch (Exception $e) {
        echo "<!-- DEBUG: DateTime error: " . $e->getMessage() . " -->";
        return $datetime;
    }
    
    if ($time < 60) return 'nu';
    if ($time < 3600) return floor($time/60) . 'm';
    if ($time < 86400) return floor($time/3600) . 'u';
    if ($time < 2592000) return floor($time/86400) . 'd';
    
    return date('d M Y', strtotime($datetime));
}

// ✅ DEBUG INFO (tijdelijk - kunnen we later weghalen)
if (isset($_GET['debug'])) {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px;'>";
    echo "<strong>Timeline Debug Info:</strong><br>";
    echo "Posts count: " . count($posts) . "<br>";
    echo "Current user avatar: " . $currentUserAvatar . "<br>";
    if (!empty($posts)) {
        echo "First post user: " . ($posts[0]['username'] ?? 'unknown') . "<br>";
        echo "First post avatar: " . ($posts[0]['avatar'] ?? 'none') . "<br>";
    }
    echo "</div>";
}

// Include sidebars (dezelfde als profile.php)
include_once THEME_PATH . '/partials/left-sidebar.php';
include_once THEME_PATH . '/partials/right-sidebar.php';

$pageCSS = [
    'theme-assets/twitter/css/feed.css',
    'theme-assets/twitter/css/components.css'
];

?>

<?php foreach ($pageCSS as $css): ?>
    <link rel="stylesheet" href="<?= base_url($css) ?>">
<?php endforeach; ?>

<div class="timeline-layout">
    <!-- Main Content -->
    <main class="timeline-main">
        <div class="twitter-timeline">
            <!-- Timeline Header -->
            <div class="timeline-header">
                <h1>Home</h1>
                <div class="timeline-tabs">
                    <button class="tab-btn active">Voor jou</button>
                    <button class="tab-btn">Volgend</button>
                </div>
            </div>

            <!-- Tweet Compose Box -->
            <div class="tweet-compose">
                <?php 
                    $form_id = 'postForm';
                    $context = 'timeline';
                    $user = $currentUser;
                    include __DIR__ . '/../partials/post-form.php';
                ?>
            </div>

            <!-- Timeline Feed -->
            <div class="timeline-feed">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="tweet" data-post-id="<?= $post['id'] ?>">
                            <div class="tweet-avatar">
                                <!-- ✅ AVATAR MET CORRECTE URL EN FALLBACK -->
                                <?php 
                                $postAvatar = getCorrectAvatarUrl($post['avatar'] ?? '');
                                ?>
                                <img src="<?= htmlspecialchars($postAvatar) ?>" 
                                     alt="<?= htmlspecialchars($post['user_name'] ?? $post['username']) ?>"
                                     onerror="this.src='<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>'">
                            </div>
                            <div class="tweet-content">
                                <div class="tweet-header">
                                    <!-- ✅ KLIKBARE GEBRUIKERSNAMEN MET CORRECTE LINKS -->
                                    <a href="/profile/<?= htmlspecialchars($post['username']) ?>" class="tweet-author">
                                        <?= htmlspecialchars($post['user_name'] ?? $post['username']) ?>
                                    </a>
                                    <a href="/profile/<?= htmlspecialchars($post['username']) ?>" class="tweet-username">
                                        @<?= htmlspecialchars($post['username']) ?>
                                    </a>
                                    <span class="tweet-time"><?= \App\Helpers\Language::timeAgo($post['created_at']) ?></span>
                                    
                                    <?php if ($post['user_id'] == ($_SESSION['user_id'] ?? 0) || ($_SESSION['role'] ?? '') === 'admin'): ?>
                                        <div class="tweet-menu">
                                            <button class="menu-trigger" onclick="toggleTweetMenu(this)">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                    <path d="M3 12c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm9 2c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm7 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                                                </svg>
                                            </button>
                                            <div class="tweet-dropdown">
                                                <button class="dropdown-item" onclick="deletePost(<?= $post['id'] ?>)">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                        <path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-2.5l-1-1zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7z"/>
                                                    </svg>
                                                    Verwijderen
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="tweet-text">
                                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                                </div>
                                
                                <?php if (!empty($post['media_path'])): ?>
                                    <div class="tweet-media">
                                        <img src="<?= base_url('uploads/' . htmlspecialchars($post['media_path'])) ?>" 
                                             alt="Tweet media" 
                                             class="tweet-image" 
                                             onclick="openImageModal('<?= base_url('uploads/' . htmlspecialchars($post['media_path'])) ?>')">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="tweet-actions">
                                    <button class="action-btn reply-btn comment-button" data-post-id="<?= $post['id'] ?>">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="comment-icon">
                                            <path d="M1.751 10c0-4.42 3.584-8 8.005-8h4.366c4.49 0 8.129 3.64 8.129 8.13 0 2.96-1.607 5.68-4.196 7.11l-8.054 4.46v-3.69h-.067c-4.49.1-8.183-3.51-8.183-8.01z"/>
                                        </svg>
                                        <span class="text"><?= $post['comments_count'] ?? 0 ?></span>
                                    </button>
                                    
                                    <button class="action-btn retweet-btn" onclick="retweetPost(<?= $post['id'] ?>)">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                            <path d="M4.5 3.88l4.432 4.14-1.364 1.46L5.5 7.55V16c0 1.1.896 2 2 2H13v2H7.5c-2.209 0-4-1.791-4-4V7.55L1.432 9.48.068 8.02 4.5 3.88zM16.5 6H11V4h5.5c2.209 0 4 1.791 4 4v8.45l2.068-1.93 1.364 1.46-4.432 4.14-4.432-4.14 1.364-1.46 2.068 1.93V8c0-1.1-.896-2-2-2z"/>
                                        </svg>
                                        <span><?= $post['retweets_count'] ?? 0 ?></span>
                                    </button>
                                    
                                    <button class="action-btn like-btn <?= ($post['user_liked'] ?? false) ? 'liked' : '' ?>" 
                                            onclick="toggleLike(<?= $post['id'] ?>, this)">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                            <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.030-3.70.477-4.82-.561-1.13-1.666-1.84-2.908-1.91z"/>
                                        </svg>
                                        <span class="like-count"><?= $post['likes'] ?? 0 ?></span>
                                    </button>
                                    
                                    <button class="action-btn share-btn" onclick="sharePost(<?= $post['id'] ?>)">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                            <path d="M12 2.59l5.7 5.7-1.41 1.42L13 6.41V16h-2V6.41l-3.29 3.3-1.42-1.42L12 2.59zM21 15l-.02 3.51c0 1.38-1.12 2.49-2.5 2.49H5.5C4.11 21 3 19.88 3 18.5V15h2v3.5c0 .28.22.5.5.5h12.98c.28 0 .5-.22.5-.5L19 15h2z"/>
                                        </svg>
                                    </button>
                                </div>

                                    <?php 
                                        $comments_data = [
                                            'post_id' => $post['id'],
                                            'comments_list' => $post['comments_list'] ?? [],
                                            'current_user' => $current_user,
                                            'show_comment_form' => true,
                                            'show_likes' => true
                                        ];
                                        extract($comments_data);
                                        include THEME_PATH . '/partials/comments-section.php';
                                        ?>

                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-timeline">
                        <h3>Welkom bij SocialCore!</h3>
                        <p>Je timeline is nog leeg. Begin met tweeten of volg andere gebruikers om berichten te zien.</p>
                        <button class="primary-btn" onclick="document.getElementById('tweetText').focus()">
                            Je eerste tweet
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Image Modal -->
<div id="image-modal" class="image-modal" style="display: none;" onclick="closeImageModal()">
    <div class="modal-content">
        <span class="modal-close" onclick="closeImageModal()">&times;</span>
        <img id="modal-image" src="" alt="Full size image">
    </div>
</div>

<script>
// ===== TIMELINE PAGE JAVASCRIPT (consistent met profile.php) =====


// Remove photo preview
function removePhoto() {
    const photoPreview = document.getElementById('photo-preview');
    const photoInput = document.getElementById('photo-input');
    
    photoPreview.style.display = 'none';
    photoPreview.innerHTML = '';
    photoInput.value = '';
}

// Post interactions
function initPostInteractions() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tweet-menu')) {
            document.querySelectorAll('.tweet-dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
}

// Toggle like function
async function toggleLike(postId, button) {
    const likeCount = button.querySelector('.like-count');
    
    try {
        button.disabled = true;
        
        const response = await fetch('<?= base_url() ?>?route=feed/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update UI
            if (result.liked) {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }
            
            likeCount.textContent = result.like_count;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        button.disabled = false;
    }
}

// Toggle tweet menu
function toggleTweetMenu(button) {
    const dropdown = button.parentElement.querySelector('.tweet-dropdown');
    
    // Close all other dropdowns
    document.querySelectorAll('.tweet-dropdown').forEach(d => {
        if (d !== dropdown) d.classList.remove('show');
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
}

// Reply to post (placeholder)
function replyToPost(postId) {
    showNotification('Reply functionaliteit komt binnenkort!', 'info');
}

// Retweet post (placeholder)
function retweetPost(postId) {
    showNotification('Retweet functionaliteit komt binnenkort!', 'info');
}

// Share post
function sharePost(postId) {
    const tweet = document.querySelector(`[data-post-id="${postId}"]`);
    const tweetText = tweet.querySelector('.tweet-text').textContent;
    const username = tweet.querySelector('.tweet-username').textContent;
    
    if (navigator.share) {
        navigator.share({
            title: `Tweet van ${username}`,
            text: tweetText,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        const shareText = `${tweetText} - ${username} op ${window.location.href}`;
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Link gekopieerd naar klembord!', 'success');
        });
    }
}

// Image modal functions
function openImageModal(imageSrc) {
    document.getElementById('modal-image').src = imageSrc;
    document.getElementById('image-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('image-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Timeline tab functionality
document.querySelectorAll('.tab-btn').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        
        // Future: Load different content based on tab
        if (this.textContent.includes('Volgend')) {
            showNotification('Volgend feed komt binnenkort!', 'info');
        }
    });
});

// Utility function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    
    const colors = {
        success: 'background: #10b981; color: white;',
        error: 'background: #ef4444; color: white;',
        info: 'background: var(--twitter-blue); color: white;',
        warning: 'background: #f59e0b; color: white;'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 70px;
        right: 20px;
        ${colors[type]}
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 1000;
        font-weight: 500;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
<!-- EINDE VAN TIMELINE.PHP - LAATSTE REGEL! -->