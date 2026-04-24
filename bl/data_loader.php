<?php
error_reporting(0);
ini_set('display_errors', 0);

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

function convertNation($nat) {
    $nat = strtoupper(trim($nat));
    $map = [
        'GER'=>'de','AUT'=>'at','SUI'=>'ch','FRA'=>'fr','POL'=>'pl','NED'=>'nl','SRB'=>'rs','CRO'=>'hr','HUN'=>'hu','ESP'=>'es','IRI'=>'ir','SLO'=>'si','AZE'=>'az','IND'=>'in','BUL'=>'bg','UKR'=>'ua','LAT'=>'lv','ENG' => 'gb-eng', 'AUS' => 'au', 'POR' => 'pt', 'MDA' => 'md', 'UZB'=>'uz','ISL'=>'is','BIH'=>'ba','FID'=>'fide','GRE'=>'gr','ARM'=>'am','KGZ'=>'kg', 'EGY' => "eg", "DEN" => 'dk', "TUR" => 'tr', "ROU" => 'ro', 'WLS' => 'gb-wls'
    ];
    return isset($map[$nat]) ? $map[$nat] : strtolower($nat);
}

function mapTeamName($name) {
    $map = [
        "Schachfreunde Deizisau"=>"SF Deizisau",
        "SC Heimbach-Weis-Neuwied"=>"SC H-W-Neuwied",
        "FC Bayern München"=>"Bayern München",
        "SV Werder Bremen"=>"Werder Bremen",
        "OSG Baden-Baden"=>"Baden-Baden"
    ];
    return isset($map[$name]) ? $map[$name] : $name;
}

function getSchachData($runde = 12) {
    global $api_id, $saison, $liga;
    $data = ['begegnungen' => [], 'tabelle' => []];

    // 1. Ergebnisse abrufen
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
                        'name_h'    => trim($b->Ergebnis_Heim_Spieler_Nachname . ", " . $b->Ergebnis_Heim_Spieler_Vorname),
                        'erg_h'     => ($b->Ergebnis_Heim_Ergebnis === "H") ? "" : $b->Ergebnis_Heim_Ergebnis,
                        'erg_g'     => ($b->Ergebnis_Gast_Ergebnis === "H") ? "" : $b->Ergebnis_Gast_Ergebnis,
                        'name_g'    => trim($b->Ergebnis_Gast_Spieler_Nachname . ", " . $b->Ergebnis_Gast_Spieler_Vorname),
                        'country_g' => convertNation($b->Ergebnis_Gast_Spieler_FIDE_Land),
                        'titel_g'   => $b->Ergebnis_Gast_Spieler_FIDE_Titel ?? ""
                    ];
                }
            }
            $data['begegnungen'][] = $match;
        }

        // --- SORTIERLOGIK ---
        if ($runde >= 13) {
            // Liste der Teams, die im oberen Raum (Atrium) spielen sollen
            $obererRaum = [
                "SF Berlin", 
                "USV TU Dresden", 
                "SK Kirchweyhe", 
                "SC Viernheim", 
                "Sfr. Wolfhagen", 
                "Baden-Baden", 
                "SC H-W-Neuwied", 
                "Werder Bremen"
            ];

            usort($data['begegnungen'], function($a, $b) use ($obererRaum) {
                // Prüfen, ob Heim- ODER Gast-Team im oberen Raum spielt
                $aInOben = (in_array($a['team_heim'], $obererRaum) || in_array($a['team_gast'], $obererRaum)) ? 0 : 1;
                $bInOben = (in_array($b['team_heim'], $obererRaum) || in_array($b['team_gast'], $obererRaum)) ? 0 : 1;

                if ($aInOben !== $bInOben) {
                    return $aInOben - $bInOben; // Atrium-Paarungen (0) vor Vogel-Saal (1)
                }
                return 0; // Innerhalb der Gruppen Reihenfolge der API beibehalten
            });
        }
    }

    // 2. Tabelle abrufen
    $url = "https://ergebnisdienst.schachbund.de/json/tabelle.php?i=$api_id&s=$saison&l=$liga&r=$runde";
    $api_tab = fetchJSON($url);
    if ($api_tab && isset($api_tab->Tabelle_Daten)) {
        foreach ($api_tab->Tabelle_Daten as $t) {
            $data['tabelle'][] = [
                'platz' => (int)$t->Tabelle_Platz,
                'team'  => mapTeamName($t->Tabelle_Mannschaft_Name),
                'mp'    => $t->Tabelle_MP,
                'bp'    => $t->Tabelle_BP 
            ];
        }
        usort($data['tabelle'], function($a, $b) {
            return $a['platz'] - $b['platz'];
        });
    }

    return $data;
}

header('Content-Type: application/json; charset=utf-8');
$runde = isset($_GET['runde']) ? intval($_GET['runde']) : 12;
if (ob_get_length()) ob_clean();
echo json_encode(getSchachData($runde), JSON_UNESCAPED_UNICODE);
exit;
