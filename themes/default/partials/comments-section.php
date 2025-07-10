<?php 
/**
 * Herbruikbare comments sectie partial
 * Bestand: /themes/default/partials/comments-section.php
 * 
 * Vereiste variabelen:
 * - $post_id: ID van de post
 * - $comments_list: Array met comments
 * - $current_user: Array met huidige gebruiker gegevens
 * - $show_comment_form: Boolean of comment form getoond moet worden (default: true)
 * - $show_likes: Boolean of like functionaliteit getoond moet worden (default: true)
 */

// Zorg voor veilige defaults
$post_id = $post_id ?? 0;
$comments_list = $comments_list ?? [];
$current_user = $current_user ?? ['name' => 'Gebruiker', 'avatar_url' => base_url('theme-assets/default/images/default-avatar.png')];
$show_comment_form = $show_comment_form ?? true;
$show_likes = $show_likes ?? true;
$current_user_id = $_SESSION['user_id'] ?? 0;

// Unieke form ID voor dit post
$form_id = 'comment-form-' . $post_id;
?>

<div class="comments-section border-t border-gray-200 pt-3 mt-3" data-post-id="<?= htmlspecialchars($post_id) ?>">
    <!-- Bestaande comments -->
    <div class="existing-comments space-y-3 mb-4">
        <?php if (!empty($comments_list)): ?>
            <?php foreach ($comments_list as $comment): ?>
                <?php 
                // Bereid comment data voor
                $comment_data = $comment;
                $comment_data['current_user_id'] = $current_user_id;
                $comment_data['show_likes'] = $show_likes;
                
                // Include de comment item partial
                include __DIR__ . '/comment-item.php';
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Toon alleen als er geen comments zijn -->
            <div class="no-comments text-center text-gray-500 text-sm py-2">
                Nog geen reacties. Wees de eerste!
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Comment formulier -->
    <?php if ($show_comment_form && $current_user_id): ?>
        <?php 
        // Bereid variabelen voor de comment form
        $form_data = [
            'post_id' => $post_id,
            'current_user' => $current_user,
            'form_id' => $form_id,
            'placeholder' => 'Schrijf een reactie...'
        ];
        
        // Include de comment form partial
        extract($form_data);
        include __DIR__ . '/comment-form.php';
        ?>
    <?php elseif (!$current_user_id): ?>
        <!-- Bericht voor niet-ingelogde gebruikers -->
        <div class="login-to-comment text-center py-3">
            <p class="text-gray-500 text-sm mb-2">Je moet ingelogd zijn om te reageren</p>
            <a href="<?= base_url('login') ?>" class="hyves-button bg-blue-500 hover:bg-blue-600 text-xs px-3 py-1">
                Inloggen
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialiseer comment functionaliteit voor deze sectie
document.addEventListener('DOMContentLoaded', function() {
    const commentsSection = document.querySelector('[data-post-id="<?= htmlspecialchars($post_id) ?>"]');
    if (!commentsSection) return;
    
    // Comment menu toggles
    const commentMenuButtons = commentsSection.querySelectorAll('.comment-menu-button');
    commentMenuButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.parentElement.querySelector('.comment-menu-dropdown');
            
            // Sluit andere dropdowns
            document.querySelectorAll('.comment-menu-dropdown').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                }
            });
            
            // Toggle huidige dropdown
            dropdown.classList.toggle('hidden');
        });
    });
    
    // Sluit menu's bij klikken buiten
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.comment-menu')) {
            commentsSection.querySelectorAll('.comment-menu-dropdown').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
    
    // Comment like buttons
    const likeButtons = commentsSection.querySelectorAll('.comment-like-button');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            toggleCommentLike(commentId, this);
        });
    });
});

// Comment delete functie
function deleteComment(commentId) {
    fetch('/feed/comment/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'comment_id=' + encodeURIComponent(commentId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Verwijder comment uit DOM
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) {
                commentElement.remove();
            }
            showNotification('Reactie verwijderd', 'success');
        } else {
            alert('Fout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er ging iets mis bij het verwijderen van de reactie');
    });
}

// Comment like toggle functie
function toggleCommentLike(commentId, button) {
    if (button.disabled) return;
    
    button.disabled = true;
    const likeCount = button.querySelector('.like-count');
    const likeIcon = button.querySelector('.like-icon');
    
    fetch('/feed/comment/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'comment_id=' + encodeURIComponent(commentId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            likeCount.textContent = data.like_count;
            
            // Update button appearance
            if (data.action === 'liked') {
                button.classList.add('liked', 'text-blue-600');
                button.classList.remove('text-gray-500');
            } else {
                button.classList.remove('liked', 'text-blue-600');
                button.classList.add('text-gray-500');
            }
        } else {
            alert('Fout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er ging iets mis bij het liken van deze reactie');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Notification functie (hergebruik van bestaande functie)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}
</script>