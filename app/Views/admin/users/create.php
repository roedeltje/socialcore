<div class="admin-content">
    <div class="admin-header">
        <h1>Nieuwe Gebruiker Aanmaken</h1>
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
            <form action="<?= base_url('admin/users?action=create') ?>" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="username">Gebruikersnaam *</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                    <small class="form-hint">Minimaal 3 tekens, alleen letters, cijfers en underscore.</small>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mailadres *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Wachtwoord *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-hint">Minimaal 8 tekens met tenminste één hoofdletter, cijfer en speciaal teken.</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Wachtwoord Bevestigen *</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="role">Rol</label>
                        <select id="role" name="role" class="form-control">
                            <option value="member">Lid</option>
                            <option value="moderator">Moderator</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active">Actief</option>
                            <option value="inactive">Inactief</option>
                            <option value="banned">Geblokkeerd</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="send_welcome" name="send_welcome" class="form-check-input">
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