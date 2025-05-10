<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Auth::register($_POST['username'], $_POST['email'], $_POST['password']);
    
    if (isset($result['success'])) {
        redirect('/login');  // Aangepast van '/login.php' naar '/login'
    } else {
        $error = $result['error'] ?? 'Registratie mislukt';
    }
}

require_once __DIR__ . '/../core/views/auth/login.php';