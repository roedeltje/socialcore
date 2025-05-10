<?php
/**
 * Redirect to a specified path
 * 
 * @param string $path Path to redirect to
 * @return void
 */
function redirect($path) 
{
    header('Location: ' . base_url($path));
    exit;
}