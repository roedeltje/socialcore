<div class="dashboard-wrapper">
    <div class="welcome-panel">
        <h2>Welkom bij SocialCore!</h2>
        <p>Dit is je dashboard waar je je site kunt beheren. Gebruik de sidebar om naar verschillende secties te navigeren.</p>
    </div>
    
    <div class="dashboard-widgets">
        <div class="widget-row">
            <div class="widget">
                <div class="widget-header">
                    <h3>Site overzicht</h3>
                </div>
                <div class="widget-content">
                    <ul>
                        <li><span class="stat">0</span> Gebruikers</li>
                        <li><span class="stat">0</span> Berichten</li>
                        <li><span class="stat">0</span> Reacties</li>
                        <li><span class="stat">0</span> Actieve plugins</li>
                    </ul>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h3>Recente activiteit</h3>
                </div>
                <div class="widget-content">
                    <p>Nog geen activiteit geregistreerd.</p>
                </div>
            </div>
        </div>
        
        <div class="widget-row">
            <div class="widget">
                <div class="widget-header">
                    <h3>Snelle acties</h3>
                </div>
                <div class="widget-content">
                    <div class="quick-actions">
                        <a href="<?= base_url('?route=admin/users/add') ?>" class="button">Nieuwe gebruiker</a>
                        <a href="<?= base_url('?route=admin/settings') ?>" class="button">Instellingen wijzigen</a>
                        <a href="<?= base_url('?route=admin/plugins') ?>" class="button">Plugins beheren</a>
                    </div>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h3>Systeeminfo</h3>
                </div>
                <div class="widget-content">
                    <ul>
                        <li>SocialCore versie: 0.1</li>
                        <li>PHP versie: <?= phpversion() ?></li>
                        <li>Actief thema: Default</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>