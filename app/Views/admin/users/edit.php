<div class="admin-content">
    <div class="admin-header">
        <h1>Gebruiker Bewerken</h1>
        <div class="admin-actions">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Overzicht
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h2>Gebruiker: <?= $user['username'] ?></h2>
        </div>
        
        <div class="card-body">
            <form action="<?= base_url('admin/users?action=edit&id=' . $user['id']) ?>" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="username">Gebruikersnaam *</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= $user['username'] ?>" required>
                    <small class="form-hint">Minimaal 3 tekens, alleen letters, cijfers en underscore.</small>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mailadres *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Nieuw Wachtwoord</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <small class="form-hint">Laat leeg om wachtwoord niet te wijzigen.</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Wachtwoord Bevestigen</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control">
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="role">Rol</label>
                        <select id="role" name="role" class="form-control">
                            <option value="member" <?= $user['role'] === 'member' ? 'selected' : '' ?>>Lid</option>
                            <option value="moderator" <?= $user['role'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Actief</option>
                            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactief</option>
                            <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Geblokkeerd</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Wijzigingen Opslaan
                    </button>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Annuleren</a>
                </div>
            </form>
        </div>
        
        <div class="card-footer">
            <h3>Gebruikersinfo</h3>
            <div class="user-info-grid">
                <div class="info-item">
                    <span class="info-label">Gebruiker ID:</span>
                    <span class="info-value"><?= $user['id'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Aangemaakt op:</span>
                    <span class="info-value"><?= $user['created_at'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Laatste login:</span>
                    <span class="info-value">Nog niet beschikbaar</span>
                </div>
            </div>
        </div>
    </div>
</div>