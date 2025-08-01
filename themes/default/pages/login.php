<?php
// Include core header voor navigatie (optioneel voor login)
// if (file_exists(__DIR__ . '/../layout/header.php')) {
//     include __DIR__ . '/../layout/header.php';
// }

// Handle form submission (POST) - Use existing AuthController logic
$errors = [];
$old_input = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the existing AuthController to process login
    $authController = new \App\Controllers\AuthController();
    
    // Temporarily capture output to prevent redirects
    ob_start();
    $authController->login();
    ob_end_clean();
    
    // Check if login was successful (user would be redirected, so we're still here = failure)
    if (isset($_SESSION['error'])) {
        $errors['general'] = $_SESSION['error'];
        unset($_SESSION['error']);
    }
    
    // Keep old input on error
    $old_input = $_POST;
    unset($old_input['password']); // Never keep password
}

// Check for success messages from registration etc.
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - SocialCore</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .core-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .core-badge {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: #374151 !important;
            background-color: #ffffff !important;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .form-input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #bbf7d0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .checkbox {
            width: 1.125rem;
            height: 1.125rem;
            border: 2px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        
        .checkbox:checked {
            background: #667eea;
            border-color: #667eea;
        }
        
        .links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #f3f4f6;
        }
        
        .link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .link:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            animation: slideIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="core-container">
        <div class="login-card">
            <span class="core-badge">CORE VIEW</span>
            
            <!-- Logo -->
            <div class="logo">
                <div class="logo-text">SocialCore</div>
                <div class="logo-subtitle">Your community, your rules, always connected</div>
            </div>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>
            
            <!-- General Error -->
            <?php if (!empty($errors['general'])): ?>
                <div class="error-message" style="background: #fef2f2; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="/?route=auth/login">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Gebruikersnaam of E-mail
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input <?= !empty($errors['username']) ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($old_input['username'] ?? '') ?>"
                        placeholder="gebruikersnaam of email@example.com"
                        autocomplete="username"
                        required
                    >
                    <?php if (!empty($errors['username'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($errors['username']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Wachtwoord
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input <?= !empty($errors['password']) ? 'error' : '' ?>"
                        placeholder="Je wachtwoord"
                        required
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($errors['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="remember_me" 
                        name="remember_me" 
                        class="checkbox"
                        <?= !empty($old_input['remember_me']) ? 'checked' : '' ?>
                    >
                    <label for="remember_me" class="form-label" style="margin-bottom: 0;">
                        Ingelogd blijven
                    </label>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Inloggen
                </button>
            </form>
            
            <!-- Links -->
            <div class="links">
                <p>
                    <a href="/?route=auth/register" class="link">
                        <i class="fas fa-user-plus"></i> Account aanmaken
                    </a>
                </p>
                <p style="margin-top: 0.5rem;">
                    <a href="/?route=auth/forgot-password" class="link">
                        <i class="fas fa-key"></i> Wachtwoord vergeten?
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>