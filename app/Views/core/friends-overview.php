<?php
// Include core header voor navigatie
if (file_exists(__DIR__ . '/../layout/header.php')) {
    include __DIR__ . '/../layout/header.php';
}

// Database integratie voor echte vrienden data
$friends = [];
$friendCount = 0;
$onlineFriends = [];
$mutualFriends = [];

if (isset($_SESSION['user_id'])) {
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        $currentUserId = $_SESSION['user_id'];
        
        // Haal alle vrienden op (gebruik de logic uit FriendsController)
        $stmt = $db->prepare("
            SELECT 
                u.id as user_id,
                u.username,
                up.display_name,
                up.avatar,
                f.created_at as friends_since,
                u.last_activity,
                CASE 
                    WHEN u.last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
                    THEN 1 ELSE 0 
                END as is_online
            FROM friendships f
            JOIN users u ON (
                CASE 
                    WHEN f.user_id = ? THEN u.id = f.friend_id
                    ELSE u.id = f.user_id
                END
            )
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE (f.user_id = ? OR f.friend_id = ?) 
            AND f.status = 'accepted'
            ORDER BY is_online DESC, up.display_name ASC
        ");
        $stmt->execute([$currentUserId, $currentUserId, $currentUserId]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process friends data
        foreach ($friends as &$friend) {
            $friend['avatar_url'] = get_avatar_url($friend['avatar']);
            
            if (empty($friend['display_name'])) {
                $friend['display_name'] = $friend['username'];
            }
            
            // Calculate friendship duration
            $friendsSince = new DateTime($friend['friends_since']);
            $now = new DateTime();
            $diff = $now->diff($friendsSince);
            
            if ($diff->days < 30) {
                $friend['friendship_duration'] = $diff->days . ' dagen';
            } elseif ($diff->days < 365) {
                $friend['friendship_duration'] = round($diff->days / 30) . ' maanden';
            } else {
                $friend['friendship_duration'] = round($diff->days / 365) . ' jaar';
            }
            
            // Separate online friends
            if ($friend['is_online']) {
                $onlineFriends[] = $friend;
            }
        }
        
        $friendCount = count($friends);
        
        // Get friend statistics
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_friends,
                SUM(CASE WHEN u.last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE) THEN 1 ELSE 0 END) as online_count,
                SUM(CASE WHEN f.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_friends
            FROM friendships f
            JOIN users u ON (
                CASE 
                    WHEN f.user_id = ? THEN u.id = f.friend_id
                    ELSE u.id = f.user_id
                END
            )
            WHERE (f.user_id = ? OR f.friend_id = ?) 
            AND f.status = 'accepted'
        ");
        $stmt->execute([$currentUserId, $currentUserId, $currentUserId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error loading friends data: " . $e->getMessage());
    }
}
?>

<!-- Core CSS styling -->
<style>
.core-container {
    min-height: calc(100vh - 80px);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.core-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
    display: inline-block;
}

.friends-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.friend-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.friend-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.friend-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    display: block;
    border: 4px solid #f3f4f6;
    transition: border-color 0.3s ease;
}

.friend-card:hover .friend-avatar {
    border-color: #667eea;
}

.online-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 12px;
    height: 12px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid white;
}

.friend-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
    text-align: center;
}

.friend-username {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    text-align: center;
}

.friend-since {
    color: #9ca3af;
    font-size: 0.75rem;
    text-align: center;
    margin-bottom: 1rem;
}

.friend-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-primary {
    background: #667eea;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
}

.empty-state {
    text-align: center;
    color: white;
    padding: 3rem;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.7;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.friend-card {
    animation: slideIn 0.3s ease forwards;
}

.friend-card:nth-child(1) { animation-delay: 0.1s; }
.friend-card:nth-child(2) { animation-delay: 0.2s; }
.friend-card:nth-child(3) { animation-delay: 0.3s; }
.friend-card:nth-child(4) { animation-delay: 0.4s; }

@media (max-width: 768px) {
    .friends-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="core-container">
    <div class="content-wrapper">
        <!-- Header -->
        <div class="mb-8">
            <span class="core-badge">CORE VIEW</span>
            <h1 class="text-4xl font-bold text-white mb-2">Mijn Vrienden</h1>
            <p class="text-white text-opacity-80">Overzicht van al je connecties op SocialCore</p>
        </div>

        <?php if (!empty($friends)): ?>
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $friendCount ?></div>
                    <div class="stat-label">Totaal Vrienden</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($onlineFriends) ?></div>
                    <div class="stat-label">Nu Online</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['recent_friends'] ?? 0 ?></div>
                    <div class="stat-label">Nieuwe Vrienden (30 dagen)</div>
                </div>
            </div>

            <!-- Friends Grid -->
            <div class="friends-grid">
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-card">
                        <?php if ($friend['is_online']): ?>
                            <div class="online-indicator" title="Online"></div>
                        <?php endif; ?>
                        
                        <img src="<?= htmlspecialchars($friend['avatar_url']) ?>" 
                             alt="<?= htmlspecialchars($friend['display_name']) ?>" 
                             class="friend-avatar">
                        
                        <div class="friend-name"><?= htmlspecialchars($friend['display_name']) ?></div>
                        <div class="friend-username">@<?= htmlspecialchars($friend['username']) ?></div>
                        <div class="friend-since">Vrienden sinds <?= htmlspecialchars($friend['friendship_duration']) ?></div>
                        
                        <div class="friend-actions">
                            <a href="/?route=profile&username=<?= urlencode($friend['username']) ?>" class="btn-primary">
                                👤 Profiel
                            </a>
                            <a href="/?route=messages/compose&to=<?= urlencode($friend['username']) ?>" class="btn-secondary">
                                💬 Bericht
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <h2 class="text-2xl font-bold mb-4">Nog geen vrienden</h2>
                <p class="text-lg mb-6 opacity-80">Begin met het toevoegen van mensen om je netwerk uit te breiden!</p>
                <a href="/?route=search" class="btn-primary" style="display: inline-block;">
                    🔍 Zoek Vrienden
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
if (file_exists(__DIR__ . '/../layout/footer.php')) {
    include __DIR__ . '/../layout/footer.php';
}
?>