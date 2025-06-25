<?php
/**
 * Privacy Settings Partial - REDIRECT naar privacy pagina
 * Dit bestand wordt niet meer gebruikt sinds we een aparte privacy controller hebben
 */
?>

<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
        Privacy Instellingen
    </h3>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-external-link-alt text-blue-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-medium text-blue-900 mb-2">
                    Privacy-instellingen zijn verhuisd!
                </h4>
                <p class="text-blue-800 mb-4">
                    Voor een betere gebruikerservaring hebben we alle privacy-instellingen naar een aparte pagina verplaatst.
                </p>
                <a href="<?= base_url('?route=privacy') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Naar Privacy Instellingen
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>