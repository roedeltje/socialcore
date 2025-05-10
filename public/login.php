<?php
session_start();
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Auth::login($_POST['username'], $_POST['password']);

    if (isset($result['success'])) {
        redirect('/dashboard.php');
    } else {
        $error = $result['error'] ?? 'Inloggen mislukt';
    }
}

require_once __DIR__ . '/../views/auth/login.php';
