<?php
// runde.php
if (isset($_GET['runde'])) {
    $runde = intval($_GET['runde']);
} else {
    $heute = date('d.m'); 
    switch ($heute) {
        case '24.04': $runde = 13; break;
        case '25.04': $runde = 14; break;
        case '26.04': $runde = 15; break;
        default:      $runde = 12; break; // Vorschau/Archiv
    }
}

// Optional: Passende Texte für die Anzeige generieren
switch ($runde) {
    case 13: $datumText = "24. April – 16:00 Uhr"; break;
    case 14: $datumText = "25. April – 14:00 Uhr"; break;
    case 15: $datumText = "26. April – 10:00 Uhr"; break;
    default: $datumText = "Zentrale Endrunde 2026"; break;
}
?>
