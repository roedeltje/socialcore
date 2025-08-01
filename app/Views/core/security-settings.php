<?php
/**
 * Core Security Settings View
 * Consistent styling with Core Friends and Core Login
 */

// Prevent direct access
// if (!defined('SOCIALCORE_LOADED')) {
//     exit('Direct access not allowed');
// }
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

    <div class="core-container">
        <!-- Core Badge -->
        <div class="core-badge">CORE VIEW</div>
        
        <!-- Security Card -->
        <div class="security-card">
            <!-- Header -->
            <div class="security-header">
                <h1 class="security-title">🔒 Beveiligingsinstellingen</h1>
                <p class="security-subtitle">Bescherm je account en gegevens</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Password Management Section -->
            <div class="security-section">
                <div class="section-header">
                    <span class="section-icon">🔑</span>
                    <h2 class="section-title">Wachtwoord Beheer</h2>
                </div>
                <p class="section-description">
                    Houd je account veilig met een sterk wachtwoord
                </p>

                <form action="/?route=security/update" method="POST" id="passwordForm">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Huidig wachtwoord</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-input"
                               placeholder="Voer je huidige wachtwoord in"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">Nieuw wachtwoord</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-input"
                               placeholder="Minimaal 8 karakters"
                               required>
                        
                        <!-- Password Strength Indicator -->
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <span id="strengthText">Voer een wachtwoord in</span>
                        </div>

                        <!-- Password Requirements -->
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">
                                <span class="requirement-icon requirement-unmet">✗</span>
                                Minimaal 8 karakters
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <span class="requirement-icon requirement-unmet">✗</span>
                                Minimaal 1 hoofdletter
                            </div>
                            <div class="requirement" id="req-number">
                                <span class="requirement-icon requirement-unmet">✗</span>
                                Minimaal 1 cijfer
                            </div>
                            <div class="requirement" id="req-special">
                                <span class="requirement-icon requirement-unmet">✗</span>
                                Minimaal 1 speciaal teken
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Bevestig nieuw wachtwoord</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-input"
                               placeholder="Herhaal je nieuwe wachtwoord"
                               required>
                    </div>

                    <button type="submit" class="btn-primary">
                        🔒 Wachtwoord Wijzigen
                    </button>
                </form>
            </div>

            <!-- Account Security Section -->
            <div class="security-section">
                <div class="section-header">
                    <span class="section-icon">🛡️</span>
                    <h2 class="section-title">Account Beveiliging</h2>
                </div>
                <p class="section-description">
                    Extra beveiligingsopties voor je account
                </p>

                <div class="form-group">
                    <label class="form-label">Laatste login activiteit</label>
                    <p style="color: #666; font-size: 0.9rem;">
                        📅 Laatst ingelogd: <strong><?= date('d-m-Y H:i') ?></strong><br>
                        🌐 IP-adres: <strong><?= $_SERVER['REMOTE_ADDR'] ?? 'Onbekend' ?></strong><br>
                        💻 Browser: <strong><?= $_SERVER['HTTP_USER_AGENT'] ? substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . '...' : 'Onbekend' ?></strong>
                    </p>
                </div>
            </div>

            <!-- Navigation -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="<?= is_core_mode() ? '/?route=profile' : '/?route=profile' ?>" class="btn-secondary">
                    ← Terug naar Profiel
                </a>
            </div>
        </div>
    </div>

    <!-- Password Strength JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            const requirements = {
                length: document.getElementById('req-length'),
                uppercase: document.getElementById('req-uppercase'),
                number: document.getElementById('req-number'),
                special: document.getElementById('req-special')
            };

            function updateRequirement(element, met) {
                const icon = element.querySelector('.requirement-icon');
                if (met) {
                    icon.textContent = '✓';
                    icon.className = 'requirement-icon requirement-met';
                } else {
                    icon.textContent = '✗';
                    icon.className = 'requirement-icon requirement-unmet';
                }
            }

            function checkPasswordStrength(password) {
                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };

                // Update requirement indicators
                updateRequirement(requirements.length, checks.length);
                updateRequirement(requirements.uppercase, checks.uppercase);
                updateRequirement(requirements.number, checks.number);
                updateRequirement(requirements.special, checks.special);

                // Calculate strength
                const metCount = Object.values(checks).filter(Boolean).length;
                
                if (password.length === 0) {
                    strengthFill.className = 'strength-fill';
                    strengthText.textContent = 'Voer een wachtwoord in';
                } else if (metCount === 1) {
                    strengthFill.className = 'strength-fill strength-weak';
                    strengthText.textContent = 'Zwak wachtwoord';
                } else if (metCount === 2) {
                    strengthFill.className = 'strength-fill strength-fair';
                    strengthText.textContent = 'Redelijk wachtwoord';
                } else if (metCount === 3) {
                    strengthFill.className = 'strength-fill strength-good';
                    strengthText.textContent = 'Goed wachtwoord';
                } else if (metCount === 4) {
                    strengthFill.className = 'strength-fill strength-strong';
                    strengthText.textContent = 'Sterk wachtwoord';
                }
            }

            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });

            // Form validation
            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('De nieuwe wachtwoorden komen niet overeen');
                    return false;
                }

                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('Het nieuwe wachtwoord moet minimaal 8 karakters bevatten');
                    return false;
                }
            });
        });
    </script>
</body>
</html>