<?php
// Bepaal de huidige route voor actieve link styling
$currentRoute = $_GET['route'] ?? 'home';
?>
</main>

<!-- Hyves-stijl Footer -->
<footer class="hyves-footer">
    <!-- Hoofd footer sectie -->
    <div class="hyves-footer-main">
        <div class="footer-container">
            <!-- Logo en beschrijving -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?= base_url('theme-assets/default/images/logo.png') ?>" alt="SocialCore Logo" class="footer-logo-img">
                    <div class="footer-brand-text">
                        <h3>SocialCore</h3>
                        <p class="footer-tagline">your community, your rules, always connected</p>
                    </div>
                </div>
                <p class="footer-description">Een open source sociaal netwerkplatform gebouwd met passie in Nederland. SocialCore biedt een moderne en flexibele basis voor online communities.</p>
                
                <!-- Social media iconen - Hyves stijl -->
                <div class="footer-social">
                    <span class="social-label">Volg ons:</span>
                    <div class="social-buttons">
                        <a href="#" class="social-btn github" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-btn twitter" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-btn discord" title="Discord">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="social-btn youtube" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Navigatie links -->
            <div class="footer-nav-section">
                <h4>Navigatie</h4>
                <ul class="footer-nav-list">
                    <li><a href="<?= base_url('/') ?>" class="<?= $currentRoute === 'home' ? 'active' : '' ?>">🏠 Home</a></li>
                    <li><a href="<?= base_url('?route=feed') ?>" class="<?= strpos($currentRoute, 'feed') === 0 ? 'active' : '' ?>">📰 Nieuwsfeed</a></li>
                    <li><a href="<?= base_url('?route=friends') ?>" class="<?= strpos($currentRoute, 'friends') === 0 ? 'active' : '' ?>">👥 Vrienden</a></li>
                    <li><a href="<?= base_url('?route=messages') ?>" class="<?= strpos($currentRoute, 'messages') === 0 ? 'active' : '' ?>">💬 Berichten</a></li>
                    <li><a href="<?= base_url('?route=photos') ?>">📷 Foto's</a></li>
                </ul>
            </div>
            
            <!-- Community links -->
            <div class="footer-nav-section">
                <h4>Community</h4>
                <ul class="footer-nav-list">
                    <li><a href="<?= base_url('?route=about') ?>">ℹ️ Over SocialCore</a></li>
                    <li><a href="#">📖 Documentatie</a></li>
                    <li><a href="#">💬 Forum</a></li>
                    <li><a href="#">❓ FAQ</a></li>
                    <li><a href="#">📝 Blog</a></li>
                </ul>
            </div>
            
            <!-- Support & Info -->
            <div class="footer-nav-section">
                <h4>Support & Info</h4>
                <ul class="footer-nav-list">
                    <li><a href="#">🛡️ Privacy</a></li>
                    <li><a href="#">📋 Voorwaarden</a></li>
                    <li><a href="#">🔐 Beveiliging</a></li>
                    <li><a href="#">📞 Contact</a></li>
                    <li><a href="#">🚀 Bijdragen</a></li>
                </ul>
            </div>
            
            <!-- Nieuwsbrief - Hyves stijl -->
            <div class="footer-newsletter">
                <h4>📬 Blijf op de hoogte!</h4>
                <p>Ontvang de laatste updates over nieuwe features en community nieuws.</p>
                <form class="newsletter-form-hyves" action="#" method="POST">
                    <div class="newsletter-input-group">
                        <input type="email" name="email" placeholder="Je e-mailadres" required>
                        <button type="submit" class="newsletter-btn">
                            <i class="fas fa-paper-plane"></i>
                            Aanmelden
                        </button>
                    </div>
                    <label class="newsletter-checkbox">
                        <input type="checkbox" name="agree" required>
                        <span class="checkmark"></span>
                        Ik ga akkoord met de <a href="#" class="privacy-link">privacyvoorwaarden</a>
                    </label>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer bottom - Hyves blauw -->
    <div class="hyves-footer-bottom">
        <div class="footer-bottom-container">
            <div class="footer-bottom-left">
                <div class="copyright">
                    &copy; 2025 <strong>SocialCore Project</strong>. Alle rechten voorbehouden.
                </div>
                <div class="made-in-nl">
                    🇳🇱 Proudly made in Nederland
                </div>
            </div>
            
            <div class="footer-bottom-center">
                <div class="footer-stats">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Stats voor ingelogde gebruikers -->
                        <span class="stat-item">👥 Online nu: <strong>127</strong></span>
                        <span class="stat-divider">|</span>
                        <span class="stat-item">📝 Berichten vandaag: <strong>1.234</strong></span>
                    <?php else: ?>
                        <!-- Stats voor bezoekers -->
                        <span class="stat-item">👥 <strong>12.345</strong> leden</span>
                        <span class="stat-divider">|</span>
                        <span class="stat-item">💬 <strong>567.890</strong> berichten</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-bottom-right">
                <div class="footer-credits">
                    <span class="credits-item">Iconen: <a href="https://fontawesome.com" target="_blank">Font Awesome</a></span>
                    <span class="credits-divider">•</span>
                    <span class="credits-item">Gemaakt met <span class="heart">❤️</span></span>
                </div>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Snelle login link voor bezoekers -->
                    <div class="quick-actions">
                        <a href="<?= base_url('?route=login') ?>" class="quick-login">
                            <i class="fas fa-sign-in-alt"></i> Inloggen
                        </a>
                        <a href="<?= base_url('?route=register') ?>" class="quick-register">
                            <i class="fas fa-user-plus"></i> Lid worden
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Scroll to top button - Hyves stijl -->
    <button id="scrollToTop" class="scroll-to-top" title="Terug naar boven">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

<!-- JavaScript voor footer interactiviteit -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to top functionaliteit
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    });
    
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Newsletter form handling
    const newsletterForm = document.querySelector('.newsletter-form-hyves');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[name="email"]').value;
            const agree = this.querySelector('input[name="agree"]').checked;
            
            if (email && agree) {
                // Hier zou je AJAX call maken naar server
                alert('Bedankt voor je aanmelding! We houden je op de hoogte. 📬');
                this.reset();
            } else if (!agree) {
                alert('Je moet akkoord gaan met de privacyvoorwaarden.');
            }
        });
    }
    
    // Social buttons hover effect
    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.1)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Footer nav links hover effect
    document.querySelectorAll('.footer-nav-list a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.paddingLeft = '8px';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.paddingLeft = '0';
        });
    });
});
</script>

<!-- Optioneel: voeg hier thema-specifieke JavaScript toe -->
<script src="<?= base_url('theme-assets/default/js/theme.js') ?>"></script>
</body>
</html>