<h1>Admin Dashboard</h1>
<p>Welkom in het beheerderspaneel, <?= htmlspecialchars($_SESSION['username']) ?>!</p>

<div class="admin-tools">
    <h2>Sitebeheer</h2>
    <ul>
        <li><a href="<?= base_url('admin/users') ?>">Gebruikersbeheer</a></li>
        <li><a href="<?= base_url('admin/settings') ?>">Site-instellingen</a></li>
        <!-- Meer beheerderstools hier -->
    </ul>
</div>

<div class="site-stats">
    <h2>Site Statistieken</h2>
    <!-- Hier kun je later statistieken toevoegen over gebruikersaantallen, activiteit, etc. -->
    <p>Statistieken worden hier binnenkort weergegeven.</p>
</div>

<!-- Link terug naar de site -->
<p><a href="<?= base_url('home') ?>">Terug naar de site</a></p>