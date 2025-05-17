<!-- /app/Views/admin/users/index.php -->
<div class="admin-content">
    <div class="admin-header">
        <h1>Gebruikersbeheer</h1>
        <div class="admin-actions">
            <a href="<?= base_url('admin/users?action=create') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nieuwe Gebruiker
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="card-header">
            <h2>Gebruikersoverzicht</h2>
            <div class="card-actions">
                <input type="text" placeholder="Zoeken..." class="search-input" id="userSearchInput">
            </div>
        </div>
        
        <div class="card-body">
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gebruikersnaam</th>
                        <th>Weergavenaam</th>
                        <th>E-mail</th>
                        <th>Rol</th>
                        <th>Status</th>
                        <th>Aangemaakt op</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Geen gebruikers gevonden</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $user['role'] === 'admin' ? 'primary' : ($user['role'] === 'moderator' ? 'info' : 'secondary') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= $user['created_at'] ?></td>
                                <td class="actions">
                                    <a href="<?= base_url('admin/users?action=edit&id=' . $user['id']) ?>" class="btn btn-sm btn-secondary" title="Bewerken">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-sm btn-danger" title="Verwijderen">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId) {
    if (confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
        window.location.href = '<?= base_url('admin/users?action=delete&id=') ?>' + userId;
    }
}

// Eenvoudige zoekfunctie voor de gebruikerstabel
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearchInput');
    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent.toLowerCase();
            rows[i].style.display = rowText.includes(searchTerm) ? '' : 'none';
        }
    });
});
</script>