</main>

    <footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-section-title">SocialCore</h3>
                <p class="footer-text">Een open source sociaal netwerkplatform</p>
            </div>
            <div class="footer-section">
                <h3 class="footer-section-title">Links</h3>
                <ul class="footer-links">
                    <li><a href="<?= base_url('index.php?route=over') ?>">Over ons</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Voorwaarden</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> SocialCore Project. Alle rechten voorbehouden.</p>
        </div>
    </div>
</footer>
    <!-- Core Chat Configuration -->
    <script>
        window.SOCIALCORE_CHAT_MODE = 'core';
        console.log("💬 Core Chat JavaScript Mode Active");
    </script>
</body>
</html>