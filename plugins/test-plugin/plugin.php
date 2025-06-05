<?php
/*
Plugin Name: Test Plugin voor SocialCore
Description: Een eenvoudige test plugin om het plugin systeem van SocialCore te testen. Voegt een welkomstbericht toe aan de homepage.
Version: 1.2.0
Author: SocialCore Ontwikkelaar
Requires: SocialCore 1.0
*/

// Voorkom directe toegang
if (!defined('BASE_PATH')) {
    exit('Direct access denied');
}

/**
 * Test Plugin - Hoofdfunctie
 */
function socialcore_test_plugin_init() {
    // Voeg een bericht toe aan de HTML head (voor testing)
    echo "<!-- Test Plugin voor SocialCore v1.2.0 is geladen -->\n";
    
    // Test functionaliteit
    socialcore_test_add_welcome_message();
}

/**
 * Welkomstbericht functie
 */
function socialcore_test_add_welcome_message() {
    // Dit zou normaal via hooks werken, maar voor nu gewoon een HTML comment
    echo "<!-- Welkomstbericht van Test Plugin actief -->\n";
}

/**
 * Plugin activatie functie (voor later gebruik)
 */
function socialcore_test_plugin_activate() {
    // Hier zou je setup code kunnen plaatsen
    error_log("Test Plugin geactiveerd!");
}

/**
 * Plugin deactivatie functie (voor later gebruik)
 */
function socialcore_test_plugin_deactivate() {
    // Hier zou je cleanup code kunnen plaatsen  
    error_log("Test Plugin gedeactiveerd!");
}

/**
 * Plugin info functie
 */
function socialcore_test_plugin_info() {
    return [
        'name' => 'Test Plugin voor SocialCore',
        'version' => '1.2.0',
        'status' => 'Werkend',
        'features' => ['Welkomstberichten', 'HTML Comments', 'Error Logging']
    ];
}

// Initialiseer de plugin (voor nu direct, later via hooks)
socialcore_test_plugin_init();

// Plugin gegevens beschikbaar maken (optioneel)
global $socialcore_plugins;
$socialcore_plugins['test-plugin'] = socialcore_test_plugin_info();

?>