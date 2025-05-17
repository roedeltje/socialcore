<?php
// We hoeven hier niets te doen, omdat de controller nu rechtstreeks naar de admin layout zal verwijzen
// Dit bestand kan eigenlijk leeg blijven of je kunt een fallback toevoegen voor het geval de controller 
// niet correct is aangepast

// Fallback content (optioneel)
echo "<div class='admin-fallback'>Dashboard wordt geladen via de admin layout...</div>";

// Optioneel: automatisch doorverwijzen naar de correcte route
echo "<script>window.location.href = '" . base_url('?route=dashboard') . "';</script>";