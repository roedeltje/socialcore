<!-- /app/Views/admin/users/create.php -->
<div class="admin-content">
    <div class="admin-header">
        <h1>Nieuwe Gebruiker Toevoegen</h1>
        <div class="admin-actions">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Overzicht
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h2>Gebruikersgegevens</h2>
        </div>
        
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('admin/users?action=create') ?>" method="POST" class="admin-form">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Gebruikersnaam *</label>
                        <input type="text" id="username" name="username" class="form-control" required value="<?= $_POST['username'] ?? '' ?>">
                        <small class="form-hint">Minimaal 3 tekens, alleen letters, cijfers en underscore.</small>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="display_name">Weergavenaam</label>
                        <input type="text" id="display_name" name="display_name" class="form-control" value="<?= $_POST['display_name'] ?? '' ?>">
                        <small class="form-hint">De naam die wordt weergegeven op het profiel.</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mailadres *</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?= $_POST['email'] ?? '' ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Wachtwoord *</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <small class="form-hint">Minimaal 8 tekens met tenminste één hoofdletter, cijfer en speciaal teken.</small>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="password_confirm">Wachtwoord Bevestigen *</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="role">Rol</label>
                        <select id="role" name="role" class="form-control">
                            <option value="member" <?= (isset($_POST['role']) && $_POST['role'] === 'member') ? 'selected' : '' ?>>Lid</option>
                            <option value="moderator" <?= (isset($_POST['role']) && $_POST['role'] === 'moderator') ? 'selected' : '' ?>>Moderator</option>
                            <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : '' ?>>Actief</option>
                            <option value="inactive" <?= (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : '' ?>>Inactief</option>
                            <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] === 'pending') ? 'selected' : '' ?>>Wachtend op verificatie</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="send_welcome" name="send_welcome" class="form-check-input" <?= (isset($_POST['send_welcome'])) ? 'checked' : '' ?>>
                    <label for="send_welcome" class="form-check-label">Welkomst-e-mail versturen</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Gebruiker Aanmaken
                    </button>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>