<?php
session_start();
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';

Auth::logout();
redirect('/login.php');
