<div class="admin-content">
    <div class="admin-header">
        <h1>Gebruikersbeheer</h1>
        <div class="admin-actions">
            <a href="<?= base_url('admin/users?action=create') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nieuwe Gebruiker
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h2>Gebruikersoverzicht</h2>
            <div class="card-actions">
                <input type="text" placeholder="Zoeken..." class="search-input">
            </div>
        </div>
        
        <div class="card-body">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gebruikersnaam</th>
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
                            <td colspan="7" class="text-center">Geen gebruikers gevonden</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td>
                                    <span class="badge badge-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= $user['created_at'] ?></td>
                                <td class="actions">
                                    <a href="<?= base_url('admin/users?action=edit&id=' . $user['id']) ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="pagination">
                <a href="#" class="page-link">Vorige</a>
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link">Volgende</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId) {
    if (confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
        window.location.href = '<?= base_url('admin/users?action=delete&id=') ?>' + userId;
    }
}
</script>