<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialCore</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>
    <h1>SocialCore</h1>
    <nav>
        <a href="/">Home</a> |
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/login">Login</a> |
            <a href="/register">Register</a>
        <?php else: ?>
            <a href="/dashboard">Dashboard</a> |
            <a href="/profile">Profiel</a> |
            <a href="/logout">Uitloggen</a>
        <?php endif; ?>
    </nav>
    <hr>
</header>

<main>