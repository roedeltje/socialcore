<div class="admin-content-header">
    <h1><?= $title ?></h1>
    <div class="admin-actions">
        <a href="<?= base_url('admin/users') ?>" class="admin-btn secondary">
            <i class="fa fa-arrow-left"></i> Terug naar overzicht
        </a>
    </div>
</div>

<?= $form->showGeneralErrors() ?>

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
                <?= $form->input('text', 'username', [
                    'id' => 'username',
                    'required' => 'required',
                    'placeholder' => 'Voer gebruikersnaam in'
                ]) ?>
                <small class="form-text text-muted">Letters, cijfers en underscores toegestaan. Min. 3 tekens.</small>
            </div>
            
            <div class="form-group">
                <label for="email">E-mailadres *</label>
                <?= $form->input('email', 'email', [
                    'id' => 'email',
                    'required' => 'required',
                    'placeholder' => 'Voer e-mailadres in'
                ]) ?>
            </div>
            
            <div class="form-group">
                <label for="display_name">Weergavenaam</label>
                <?= $form->input('text', 'display_name', [
                    'id' => 'display_name',
                    'placeholder' => 'Voer weergavenaam in'
                ]) ?>
                <small class="form-text text-muted">Naam die wordt weergegeven in het systeem.</small>
            </div>
            
            <!-- Wachtwoord - optioneel bij bewerken -->
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <?= $form->input('password', 'password', [
                    'id' => 'password',
                    'placeholder' => 'Laat leeg om ongewijzigd te laten'
                ]) ?>
                <small class="form-text text-muted">Laat leeg om het wachtwoord niet te wijzigen.</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Wachtwoord bevestigen</label>
                <?= $form->input('password', 'password_confirm', [
                    'id' => 'password_confirm',
                    'placeholder' => 'Bevestig nieuw wachtwoord'
                ]) ?>
            </div>
            
            <!-- Rol en status -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">Rol *</label>
                    <?= $form->select('role', [
                        'admin' => 'Administrator',
                        'moderator' => 'Moderator',
                        'member' => 'Lid'
                    ], ['id' => 'role', 'required' => 'required']) ?>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="status">Status *</label>
                    <?= $form->select('status', [
                        'active' => 'Actief',
                        'inactive' => 'Inactief',
                        'banned' => 'Geblokkeerd'
                    ], ['id' => 'status', 'required' => 'required']) ?>
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