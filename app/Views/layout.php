<?php
// app/Views/layout.php

// Variabelen die in de header/footer worden gebruikt zijn hier beschikbaar
// omdat we extract($data) hebben aangeroepen in de controller

// Header inladen
include __DIR__ . '/layout/header.php';

// Hier voegen we de dynamische content toe
echo $content;

// Footer inladen
include __DIR__ . '/layout/footer.php';