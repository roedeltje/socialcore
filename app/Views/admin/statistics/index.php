<!-- /app/Views/admin/statistics/index.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> Statistieken Dashboard</h1>
        <p>Uitgebreid overzicht van platform statistieken, trends en analytics.</p>
        <div class="page-actions">
            <button onclick="refreshStatistics()" class="button button-primary">
                <i class="fas fa-sync-alt"></i> Ververs Data
            </button>
            <button onclick="exportStatistics()" class="button button-secondary">
                <i class="fas fa-download"></i> Exporteer Rapport
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Quick Stats Cards -->
    <div class="stats-cards-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($user_stats['total_users']) ?></div>
                <div class="stat-label">Totaal Gebruikers</div>
                <div class="stat-change positive">
                    +<?= $user_stats['users_today'] ?> vandaag
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($content_stats['total_posts']) ?></div>
                <div class="stat-label">Totaal Berichten</div>
                <div class="stat-change positive">
                    +<?= $content_stats['posts_today'] ?> vandaag
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($content_stats['total_likes']) ?></div>
                <div class="stat-label">Totaal Likes</div>
                <div class="stat-change">
                    <?= number_format($content_stats['total_friendships']) ?> vriendschappen
                </div>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= $user_stats['active_users_week'] ?></div>
                <div class="stat-label">Actieve Gebruikers</div>
                <div class="stat-change">
                    Deze week
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-row">
            <!-- Gebruikersgroei Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-area"></i> Gebruikersgroei (12 maanden)</h3>
                    <div class="chart-controls">
                        <select onchange="updateUserGrowthChart(this.value)">
                            <option value="12">Laatste 12 maanden</option>
                            <option value="6">Laatste 6 maanden</option>
                            <option value="3">Laatste 3 maanden</option>
                        </select>
                    </div>
                </div>
                <canvas id="userGrowthChart"></canvas>
            </div>

            <!-- Activiteit Trends Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> Activiteit Trends (30 dagen)</h3>
                    <div class="chart-legend">
                        <span class="legend-item posts">■ Berichten</span>
                        <span class="legend-item likes">■ Likes</span>
                        <span class="legend-item registrations">■ Registraties</span>
                    </div>
                </div>
                <canvas id="activityTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="data-section">
        <div class="data-row">
            <!-- Top Content -->
            <div class="data-widget">
                <div class="widget-header">
                    <h3><i class="fas fa-trophy"></i> Populairste Berichten</h3>
                    <span class="widget-subtitle">Meest gelikte content</span>
                </div>
                <div class="widget-content">
                    <?php if (!empty($top_content['top_posts'])): ?>
                        <div class="top-content-list">
                            <?php foreach (array_slice($top_content['top_posts'], 0, 5) as $post): ?>
                                <div class="content-item">
                                    <div class="content-meta">
                                        <strong><?= htmlspecialchars($post['display_name'] ?? $post['username']) ?></strong>
                                        <span class="content-date"><?= date('d M', strtotime($post['created_at'])) ?></span>
                                    </div>
                                    <div class="content-text">
                                        <?= substr(htmlspecialchars($post['content']), 0, 100) ?>...
                                    </div>
                                    <div class="content-stats">
                                        <span class="likes-count">
                                            <i class="fas fa-heart"></i> <?= $post['likes_count'] ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data">Nog geen berichten beschikbaar.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Users -->
            <div class="data-widget">
                <div class="widget-header">
                    <h3><i class="fas fa-user-friends"></i> Meest Actieve Gebruikers</h3>
                    <span class="widget-subtitle">Meeste berichten geplaatst</span>
                </div>
                <div class="widget-content">
                    <?php if (!empty($top_content['top_users'])): ?>
                        <div class="top-users-list">
                            <?php foreach (array_slice($top_content['top_users'], 0, 10) as $index => $user): ?>
                                <div class="user-item">
                                    <div class="user-rank">#<?= $index + 1 ?></div>
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></strong>
                                        <span class="user-stats">
                                            <?= $user['post_count'] ?> berichten, 
                                            <?= $user['total_likes'] ?> likes
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data">Nog geen gebruikersactiviteit beschikbaar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="system-section">
        <div class="system-row">
            <!-- System Stats -->
            <div class="system-widget">
                <div class="widget-header">
                    <h3><i class="fas fa-server"></i> Systeem Informatie</h3>
                </div>
                <div class="widget-content">
                    <div class="system-grid">
                        <div class="system-item">
                            <div class="system-label">PHP Versie</div>
                            <div class="system-value"><?= $system_stats['php_version'] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">MySQL Versie</div>
                            <div class="system-value"><?= explode('-', $system_stats['mysql_version'])[0] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Geheugengebruik</div>
                            <div class="system-value"><?= $system_stats['memory_usage'] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Server Load</div>
                            <div class="system-value"><?= $system_stats['server_load'] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Database Grootte</div>
                            <div class="system-value"><?= $system_stats['database_size'] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Upload Bestanden</div>
                            <div class="system-value"><?= $system_stats['total_files'] ?> bestanden</div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Upload Grootte</div>
                            <div class="system-value"><?= $system_stats['uploads_size_formatted'] ?></div>
                        </div>
                        <div class="system-item">
                            <div class="system-label">Vrije Schijfruimte</div>
                            <div class="system-value"><?= $system_stats['disk_free'] ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activity-widget">
                <div class="widget-header">
                    <h3><i class="fas fa-clock"></i> Recente Activiteit</h3>
                </div>
                <div class="widget-content">
                    <div class="activity-tabs">
                        <button class="tab-button active" onclick="showActivityTab('posts')">
                            Nieuwe Berichten
                        </button>
                        <button class="tab-button" onclick="showActivityTab('users')">
                            Nieuwe Gebruikers
                        </button>
                    </div>
                    
                    <div id="activity-posts" class="activity-content active">
                        <?php if (!empty($recent_activity['recent_posts'])): ?>
                            <?php foreach (array_slice($recent_activity['recent_posts'], 0, 8) as $post): ?>
                                <div class="activity-item">
                                    <div class="activity-icon posts">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                    <div class="activity-details">
                                        <strong><?= htmlspecialchars($post['display_name'] ?? $post['username']) ?></strong>
                                        <span>plaatste een bericht</span>
                                        <div class="activity-time"><?= timeAgo($post['created_at']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">Geen recente berichten.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div id="activity-users" class="activity-content">
                        <?php if (!empty($recent_activity['recent_users'])): ?>
                            <?php foreach (array_slice($recent_activity['recent_users'], 0, 8) as $user): ?>
                                <div class="activity-item">
                                    <div class="activity-icon users">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-details">
                                        <strong><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></strong>
                                        <span>registreerde zich</span>
                                        <div class="activity-time"><?= timeAgo($user['created_at']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">Geen recente registraties.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Footer -->
    <div class="quick-actions-section">
        <div class="actions-header">
            <h3><i class="fas fa-tools"></i> Snelle Acties</h3>
        </div>
        <div class="actions-grid">
            <a href="<?= base_url('?route=admin/users') ?>" class="action-card">
                <i class="fas fa-users"></i>
                <span>Gebruikers Beheren</span>
            </a>
            <a href="<?= base_url('?route=admin/content/posts') ?>" class="action-card">
                <i class="fas fa-newspaper"></i>
                <span>Content Modereren</span>
            </a>
            <a href="<?= base_url('?route=admin/settings') ?>" class="action-card">
                <i class="fas fa-cog"></i>
                <span>Instellingen</span>
            </a>
            <a href="<?= base_url('?route=admin/maintenance/database') ?>" class="action-card">
                <i class="fas fa-database"></i>
                <span>Database Onderhoud</span>
            </a>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Chart Data from PHP
const userGrowthData = <?= json_encode($user_stats['user_growth']) ?>;
const activityTrends = {
    posts: <?= json_encode($activity_trends['daily_posts']) ?>,
    likes: <?= json_encode($activity_trends['daily_likes']) ?>,
    registrations: <?= json_encode($activity_trends['daily_registrations']) ?>
};

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initUserGrowthChart();
    initActivityTrendsChart();
});

function initUserGrowthChart() {
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: userGrowthData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('nl-NL', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Nieuwe Gebruikers',
                data: userGrowthData.map(item => item.count),
                borderColor: 'var(--primary-color)',
                backgroundColor: 'rgba(15, 62, 163, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'var(--border-color)'
                    }
                },
                x: {
                    grid: {
                        color: 'var(--border-color)'
                    }
                }
            }
        }
    });
}

function initActivityTrendsChart() {
    const ctx = document.getElementById('activityTrendsChart').getContext('2d');
    
    // Combine all dates and create consistent dataset
    const allDates = new Set();
    [...activityTrends.posts, ...activityTrends.likes, ...activityTrends.registrations]
        .forEach(item => allDates.add(item.date));
    
    const sortedDates = Array.from(allDates).sort();
    
    function getDataForDates(data, valueKey) {
        return sortedDates.map(date => {
            const found = data.find(item => item.date === date);
            return found ? found[valueKey] : 0;
        });
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: sortedDates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('nl-NL', { month: 'short', day: 'numeric' });
            }),
            datasets: [
                {
                    label: 'Berichten',
                    data: getDataForDates(activityTrends.posts, 'posts'),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Likes',
                    data: getDataForDates(activityTrends.likes, 'likes'),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Registraties',
                    data: getDataForDates(activityTrends.registrations, 'registrations'),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'var(--border-color)'
                    }
                },
                x: {
                    grid: {
                        color: 'var(--border-color)'
                    }
                }
            }
        }
    });
}

// Activity tabs functionality
function showActivityTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Update content
    document.querySelectorAll('.activity-content').forEach(content => content.classList.remove('active'));
    document.getElementById('activity-' + tab).classList.add('active');
}

// Utility functions
function refreshStatistics() {
    window.location.reload();
}

function exportStatistics() {
    // Placeholder voor export functionaliteit
    alert('Export functionaliteit komt binnenkort beschikbaar!');
}
</script>

<?php
// Helper function voor time ago (add this to Controller base class later)
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'zojuist';
    if ($time < 3600) return floor($time/60) . ' min geleden';
    if ($time < 86400) return floor($time/3600) . ' uur geleden';
    if ($time < 2592000) return floor($time/86400) . ' dagen geleden';
    
    return date('d M Y', strtotime($datetime));
}
?>

<style>
/* Statistics Dashboard Styling */
.admin-content-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

.stats-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-card.primary { border-left: 4px solid var(--primary-color); }
.stat-card.success { border-left: 4px solid var(--success-color); }
.stat-card.warning { border-left: 4px solid var(--accent-color); }
.stat-card.info { border-left: 4px solid #3b82f6; }

.stat-icon {
    font-size: 2.5em;
    margin-right: 15px;
    opacity: 0.7;
}

.stat-card.primary .stat-icon { color: var(--primary-color); }
.stat-card.success .stat-icon { color: var(--success-color); }
.stat-card.warning .stat-icon { color: var(--accent-color); }
.stat-card.info .stat-icon { color: #3b82f6; }

.stat-number {
    font-size: 2em;
    font-weight: 700;
    color: var(--text-color);
    line-height: 1;
}

.stat-label {
    font-size: 0.9em;
    color: var(--text-muted);
    margin: 5px 0;
}

.stat-change {
    font-size: 0.8em;
    font-weight: 500;
}

.stat-change.positive { color: var(--success-color); }

/* Charts Section */
.charts-section {
    margin-bottom: 30px;
}

.chart-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.chart-container {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 10px;
}

.chart-header h3 {
    margin: 0;
    color: var(--text-color);
    font-size: 1.1em;
}

.chart-legend {
    display: flex;
    gap: 15px;
}

.legend-item {
    font-size: 0.85em;
    font-weight: 500;
}

.legend-item.posts { color: #10b981; }
.legend-item.likes { color: #f59e0b; }
.legend-item.registrations { color: #ef4444; }

canvas {
    max-height: 300px !important;
}

/* Data Section */
.data-section {
    margin-bottom: 30px;
}

.data-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.data-widget {
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.widget-header {
    padding: 20px 20px 15px 20px;
    border-bottom: 1px solid var(--border-color);
}

.widget-header h3 {
    margin: 0 0 5px 0;
    color: var(--text-color);
    font-size: 1.1em;
}

.widget-subtitle {
    color: var(--text-muted);
    font-size: 0.85em;
}

.widget-content {
    padding: 20px;
}

.top-content-list, .top-users-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.content-item {
    padding: 12px;
    background: var(--bg-color);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.content-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.content-date {
    color: var(--text-muted);
    font-size: 0.85em;
}

.content-text {
    color: var(--text-color);
    font-size: 0.9em;
    line-height: 1.4;
    margin-bottom: 8px;
}

.content-stats {
    display: flex;
    gap: 10px;
}

.likes-count {
    color: var(--accent-color);
    font-size: 0.85em;
    font-weight: 500;
}

.user-item {
    display: flex;
    align-items: center;
    padding: 10px;
    background: var(--bg-color);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.user-rank {
    background: var(--primary-color);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85em;
    margin-right: 12px;
}

.user-info strong {
    display: block;
    color: var(--text-color);
}

.user-stats {
    color: var(--text-muted);
    font-size: 0.85em;
}

/* System Section */
.system-section {
    margin-bottom: 30px;
}

.system-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.system-widget, .activity-widget {
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.system-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.system-item {
    text-align: center;
    padding: 15px;
    background: var(--bg-color);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.system-label {
    color: var(--text-muted);
    font-size: 0.85em;
    margin-bottom: 5px;
}

.system-value {
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.95em;
}

/* Activity Tabs */
.activity-tabs {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 15px;
}

.tab-button {
    background: none;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    color: var(--text-muted);
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.tab-button.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

.activity-content {
    display: none;
}

.activity-content.active {
    display: block;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    color: white;
    font-size: 0.85em;
}

.activity-icon.posts { background: var(--success-color); }
.activity-icon.users { background: var(--primary-color); }

.activity-details strong {
    color: var(--text-color);
}

.activity-time {
    color: var(--text-muted);
    font-size: 0.8em;
    margin-top: 2px;
}

/* Quick Actions */
.quick-actions-section {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.actions-header h3 {
    margin: 0 0 15px 0;
    color: var(--text-color);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 15px;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.2s;
}

.action-card:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.action-card i {
    font-size: 1.5em;
    margin-bottom: 8px;
}

.action-card span {
    font-size: 0.9em;
    font-weight: 500;
    text-align: center;
}

.no-data {
    text-align: center;
    color: var(--text-muted);
    font-style: italic;
    padding: 20px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .chart-row,
    .data-row,
    .system-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .chart-legend {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .system-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
    
    .page-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .activity-tabs {
        flex-direction: column;
    }
    
    .tab-button {
        text-align: left;
        border-bottom: none;
        border-left: 2px solid transparent;
    }
    
    .tab-button.active {
        border-left-color: var(--primary-color);
        border-bottom-color: transparent;
    }
}

@media (max-width: 480px) {
    .admin-content-wrapper {
        padding: 10px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-icon {
        font-size: 2em;
        margin-right: 10px;
    }
    
    .stat-number {
        font-size: 1.5em;
    }
    
    .chart-container,
    .data-widget,
    .system-widget,
    .activity-widget {
        padding: 15px;
    }
    
    .widget-content {
        padding: 15px;
    }
}

/* Print Styles */
@media print {
    .page-actions,
    .quick-actions-section {
        display: none;
    }
    
    .chart-container {
        break-inside: avoid;
    }
    
    .stat-card {
        break-inside: avoid;
        margin-bottom: 10px;
    }
}

/* Dark mode support (future enhancement) */
@media (prefers-color-scheme: dark) {
    .chart-container canvas {
        filter: invert(1) hue-rotate(180deg);
    }
}

/* Animation keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.chart-container,
.data-widget,
.system-widget,
.activity-widget {
    animation: fadeInUp 0.5s ease-out;
}

/* Loading states */
.chart-container.loading {
    position: relative;
    min-height: 300px;
}

.chart-container.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    border: 4px solid var(--border-color);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Enhanced accessibility */
.stat-card:focus,
.action-card:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Status indicators */
.status-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 5px;
}

.status-indicator.online { background-color: var(--success-color); }
.status-indicator.warning { background-color: var(--accent-color); }
.status-indicator.offline { background-color: var(--danger-color); }

/* Tooltip styles */
.tooltip {
    position: relative;
    cursor: help;
}

.tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: var(--text-color);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.8em;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
    z-index: 1000;
}

.tooltip:hover::after {
    opacity: 1;
}
</style>