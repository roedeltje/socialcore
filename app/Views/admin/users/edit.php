<div class="admin-content-header">
    <h1><?= $title ?></h1>
    <div class="admin-actions">
        <a href="<?= base_url('admin/users') ?>" class="admin-btn secondary">
            <i class="fa fa-arrow-left"></i> Terug naar overzicht
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Fout!</strong> Controleer de volgende problemen:
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <strong>Gelukt!</strong> De gebruiker is succesvol bijgewerkt.
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>Gebruiker bewerken</h2>
        <p class="text-muted">Wijzig de gegevens van gebruiker <?= htmlspecialchars($user['username']) ?></p>
    </div>
    <div class="admin-card-body">
        <form action="<?= base_url('admin/users?action=edit&id=' . $user['id']) ?>" method="post" class="admin-form">
            <!-- Gebruiker ID -->
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            
            <!-- Basisgegevens -->
            <div class="form-group">
                <label for="username">Gebruikersnaam *</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                       class="form-control" placeholder="Voer gebruikersnaam in">
                <small class="form-text text-muted">Letters, cijfers en underscores toegestaan. Min. 3 tekens.</small>
            </div>
            
            <div class="form-group">
                <label for="email">E-mailadres *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                       class="form-control" placeholder="Voer e-mailadres in">
            </div>
            
            <div class="form-group">
                <label for="display_name">Weergavenaam</label>
                <input type="text" id="display_name" name="display_name" 
                       value="<?= htmlspecialchars($user['display_name'] ?? $user['username']) ?>"
                       class="form-control" placeholder="Voer weergavenaam in">
                <small class="form-text text-muted">Naam die wordt weergegeven in het systeem.</small>
            </div>
            
            <!-- Wachtwoord - optioneel bij bewerken -->
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password"
                       class="form-control" placeholder="Laat leeg om ongewijzigd te laten">
                <small class="form-text text-muted">Laat leeg om het wachtwoord niet te wijzigen.</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Wachtwoord bevestigen</label>
                <input type="password" id="password_confirm" name="password_confirm"
                       class="form-control" placeholder="Bevestig nieuw wachtwoord">
            </div>
            
            <!-- Rol en status -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">Rol *</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                        <option value="moderator" <?= ($user['role'] === 'moderator') ? 'selected' : '' ?>>Moderator</option>
                        <option value="member" <?= ($user['role'] === 'member') ? 'selected' : '' ?>>Lid</option>
                    </select>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Actief</option>
                        <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>Inactief</option>
                        <option value="banned" <?= ($user['status'] === 'banned') ? 'selected' : '' ?>>Geblokkeerd</option>
                    </select>
                </div>
            </div>
            
            <!-- Metadata - alleen weergeven -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Aangemaakt op</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['created_at']) ?>" disabled>
                </div>
                
                <div class="form-group col-md-6">
                    <label>Laatste update</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['updated_at'] ?? 'Nooit bijgewerkt') ?>" disabled>
                </div>
            </div>
            
            <!-- Form actions -->
            <div class="form-actions">
                <button type="submit" class="admin-btn primary">
                    <i class="fa fa-save"></i> Gebruiker opslaan
                </button>
                <a href="<?= base_url('admin/users') ?>" class="admin-btn secondary">
                    Annuleren
                </a>
            </div>
        </form>
    </div>
</div>