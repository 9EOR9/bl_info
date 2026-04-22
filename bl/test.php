<?php
error_reporting(0);
ini_set('display_errors', 0);

// Konfiguration
$api_id = "ab67dcf205474aaabb416783decbbbbb";
$saison = "2025"; 
$liga   = "BL";

function fetchJSON($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output);
}

// Hilfsfunktion für Flaggen-Konvertierung (API GER -> de)
function convertNation($nat) {
    $nat = strtoupper(trim($nat));
    $map = [
        'GER' => 'de', 'AUT' => 'at', 'SUI' => 'ch', 'FRA' => 'fr', 'POL' => 'pl',
        'NED' => 'nl', 'SRB' => 'rs', 'CRO' => 'hr', 'HUN' => 'hu', 'ESP' => 'es',
        'IRI' => 'ir', 'SLO' => 'si', 'AZE' => 'az', 'IND' => 'in', 'BUL' => 'bg',
        'UKR' => 'ua', 'LAT' => 'lv', 'UZB' => 'uz', 'ISL' => 'is', 'BIH' => 'ba',
        'FID' => 'fide', 'GRE' => 'gr', 'ARM' => 'am', 'AZE' => 'az', 'KGZ' => 'kg'
    ];
    return isset($map[$nat]) ? $map[$nat] : strtolower($nat);
}

// Hilfsfunktion für Team-Mapping (kürzt Namen für das Dashboard)
function mapTeamName($name) {
    $map = [
        "Schachfreunde Deizisau" => "SF Deizisau",
        "SC Heimbach-Weis-Neuwied" => "SC H-W-Neuwied",
        "FC Bayern München" => "Bayern München",
        "SV Werder Bremen" => "Werder Bremen",
        "Hamburger SK" => "Hamburger SK",
        "SG Solingen" => "SG Solingen",
        "SF Berlin" => "SF Berlin",
        "OSG Baden-Baden" => "Baden-Baden",
        "USV TU Dresden" => "USV TU Dresden",
        "SC Viernheim" => "SC Viernheim",
        "Sfr. Wolfhagen" => "Sfr. Wolfhagen",
        "FC St. Pauli" => "FC St. Pauli",
        "MSA Zugzwang" => "MSA Zugzwang",
        "SV Deggendorf" => "SV Deggendorf",
        "SK Kirchweyhe" => "SK Kirchweyhe",
        "Düsseldorfer SK" => "Düsseldorfer SK"
    ];
    return isset($map[$name]) ? $map[$name] : $name;
}

function getSchachData($runde = 12) {
    global $api_id, $saison, $liga;
    $data = ['begegnungen' => [], 'tabelle' => []];

    // 1. ANSETZUNGEN & ERGEBNISSE
    $url = "https://ergebnisdienst.schachbund.de/json/ergebnisse.php?i=$api_id&s=$saison&l=$liga&r=$runde";
    $api_res = fetchJSON($url);

    if ($api_res && isset($api_res->Ansetzungen_Daten)) {
        foreach ($api_res->Ansetzungen_Daten as $p) {
            $match = [
                'team_heim'  => mapTeamName($p->Ansetzung_Heim_Name),
                'team_gast'  => mapTeamName($p->Ansetzung_Gast_Name),
                'score_heim' => $p->Ansetzung_Heim_BP,
                'score_gast' => $p->Ansetzung_Gast_BP,
                'bretter'    => []
            ];

            if (isset($p->Ergebnisse_Daten)) {
                foreach ($p->Ergebnisse_Daten as $b) {
                    $match['bretter'][] = [
                        'titel_h'   => $b->Ergebnis_Heim_Spieler_FIDE_Titel ?? "",
                        'country_h' => convertNation($b->Ergebnis_Heim_Spieler_FIDE_Land),
                        'name_h'    => $b->Ergebnis_Heim_Spieler_Nachname,
                        'erg_h'     => str_replace('½', '0.5', $b->Ergebnis_Heim_Ergebnis),
                        'erg_g'     => str_replace('½', '0.5', $b->Ergebnis_Gast_Ergebnis),
                        'name_g'    => $b->Ergebnis_Gast_Spieler_Nachname,
                        'country_g' => convertNation($b->Ergebnis_Gast_Spieler_FIDE_Land),
                        'titel_g'   => $b->Ergebnis_Gast_Spieler_FIDE_Titel ?? ""
                    ];
                }
            }
            $data['begegnungen'][] = $match;
        }
    }

    // 2. TABELLE
    $url = "https://ergebnisdienst.schachbund.de/json/tabelle.php?i=$api_id&s=$saison&l=$liga&r=$runde";
    $api_tab = fetchJSON($url);

    if ($api_tab && isset($api_tab->Tabelle_Daten)) {
        foreach ($api_tab->Tabelle_Daten as $t) {
            $data['tabelle'][] = [
                'platz' => $t->Tabelle_Platz,
                'team'  => mapTeamName($t->Tabelle_Mannschaft_Name),
                'mp'    => $t->Tabelle_MP,
                'bp'    => number_format((float)$t->Tabelle_BP, 1, '.', '')
            ];
        }
    }
    return $data;
}

header('Content-Type: application/json; charset=utf-8');
$runde = isset($_GET['runde']) ? intval($_GET['runde']) : 12;
if (ob_get_length()) ob_clean();
echo json_encode(getSchachData($runde), JSON_UNESCAPED_UNICODE);
exit;
