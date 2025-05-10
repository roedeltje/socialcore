<?php

// ✅ Start sessie
session_start();

// ✅ Timezone en foutmeldingen voor development
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Laad globale helpers
require_once __DIR__ . '/helpers.php';

// ❌ Geen router of output hier!
