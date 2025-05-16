</main>

    <footer class="site-footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>SocialCore</h3>
            <p class="footer-tagline">Een open source sociaal netwerkplatform</p>
            <p class="footer-description">Gebouwd met passie in Nederland, SocialCore biedt een moderne en flexibele basis voor online communities.</p>
            <div class="social-icons">
                <a href="#" class="social-icon" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon" title="Discord"><i class="fab fa-discord"></i></a>
            </div>
        </div>
        
        <div class="footer-column">
            <h3>Links</h3>
            <nav class="footer-nav">
                <ul>
                    <li><a href="<?php echo base_url('?route=home'); ?>">Home</a></li>
                    <li><a href="<?php echo base_url('?route=about'); ?>">Over ons</a></li>
                    <li><a href="<?php echo base_url('?route=login'); ?>">Login</a></li>
                    <li><a href="<?php echo base_url('?route=register'); ?>">Registreren</a></li>
                    <li><a href="#">Documentatie</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="footer-column">
            <h3>Resources</h3>
            <nav class="footer-nav">
                <ul>
                    <li><a href="#">Forum</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Voorwaarden</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="footer-column">
            <h3>Nieuwsbrief</h3>
            <p>Blijf op de hoogte van de nieuwste updates</p>
            <form class="newsletter-form">
                <input type="email" placeholder="E-mailadres" required>
                <button type="submit">Aanmelden</button>
            </form>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="copyright">
            &copy; 2025 SocialCore Project. Alle rechten voorbehouden.
        </div>
        <div class="attributions">
            <p>Illustraties: <a href="https://undraw.co" target="_blank">unDraw</a> | 
            Iconen: <a href="https://fontawesome.com" target="_blank">Font Awesome</a> |
            Gemaakt met <span class="heart">❤</span> in Nederland</p>
        </div>
    </div>
</footer>
    <!-- Optioneel: voeg hier thema-specifieke JavaScript toe -->
    <script src="<?= base_url('theme-assets/default/js/theme.js') ?>"></script>
</body>
</html>