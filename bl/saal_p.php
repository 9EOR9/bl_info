<?php
if (isset($_GET['runde'])) {
    $runde = intval($_GET['runde']);
} else {
    $heute = date('d.m'); // Format: Tag.Monat (z.B. 24.04)
    
    switch ($heute) {
        case '24.04':
            $runde = 13;
            break;
        case '25.04':
            $runde = 14;
            break;
        case '26.04':
            $runde = 15;
            break;
        default:
            $runde = 12;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <title>SBL Infoscreen - Runde <?php echo $runde; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&display=swap');

        :root {
            --bg-overlay: rgba(0, 0, 0, 0.15);
            --glass-bg: rgba(15, 15, 15, 0.85);
            --glass-blur: 2px;
            --accent-light: rgba(255, 255, 255, 0.25);
            --line-thickness: 1px;
            --accent-gold: #c5a059;
            /* HEADER-OPTIMIERUNG: Radialer Verlauf für besseren Kontrast an den Seiten */
            --header-bg-gradient: radial-gradient(circle, rgba(255, 255, 255, 0.85) 0%, rgba(220, 220, 220, 0.85) 50%, rgba(160, 160, 160, 0.9) 100%);
            --text-black: #000000;
            --border-radius: 40px;
            --zebra-bg: rgba(255, 255, 255, 0.05);
        }

        html, body {
            margin: 0; padding: 0;
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)),
                        url('img/wbh.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto Condensed', sans-serif;
            color: white;
            height: 3840px; width: 2160px;
            overflow: hidden;
        }

        body { display: flex; flex-direction: column; padding: 35px; box-sizing: border-box; }

        /* --- HEADER --- */
        .main-page-header {
            background: var(--header-bg-gradient);
            backdrop-filter: blur(25px);
            border: var(--line-thickness) solid var(--accent-light);
            border-bottom: 5px solid var(--text-black);
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            color: var(--text-black);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px; 
        }

        .header-text-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .header-logo { 
            height: 110px; /* Leicht vergrößert für bessere Wirkung */
            width: auto; 
            vertical-align: middle; 
            /* DROP-SHADOW: Erzeugt einen feinen Schatten hinter dem Logo für maximalen Kontrast */
            filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.2));
        }

        .main-page-header h1 { font-size: 4.5rem; margin: 0; text-transform: uppercase; font-weight: 700; line-height: 1; }
        .main-page-header p { font-size: 2.5rem; margin: 10px 0 0 0; opacity: 0.9; width: 100%; text-align: center; }

        /* --- LAYOUT GRID --- */
        .dashboard-grid {
            display: grid; grid-template-columns: 2.1fr 1fr; gap: 35px; flex-grow: 1; min-height: 0;
        }

        .glass-box {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border: var(--line-thickness) solid var(--accent-light);
            border-radius: var(--border-radius);
            overflow: hidden; display: flex; flex-direction: column;
        }

        .section-header {
            background: linear-gradient(to bottom, rgba(235, 235, 235, 0.5) 0%, rgba(255, 255, 255, 0.78) 100%) !important;
            color: var(--text-black) !important;
            font-size: 3.2rem; font-weight: 700; padding: 12px;
            text-align: center; text-transform: uppercase;
        }

        .content-area { flex-grow: 1; padding: 10px 25px; overflow-y: auto; scrollbar-width: none; }

        /* --- ERGEBNISSE --- */
        .match-container {
            border-bottom: 3px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 18px;
            padding-bottom: 12px; 
        }
        .match-container:last-child { border-bottom: none; }

        .score-box {
            display: inline-block;
            width: 170px; height: 55px; line-height: 55px;
            text-align: center; background: rgba(0, 0, 0, 0.7);
            border-radius: 15px; border: 2px solid rgba(255, 255, 255, 0.4);
            font-size: 2.8rem; font-weight: bold; color: var(--accent-gold);
        }

        .team-logo { height: 45px; width: auto; vertical-align: middle; object-fit: contain; }
        .logo-heim { margin-right: 15px; } 
        .logo-gast { margin-left: 15px; }  

        .brett-row {
            display: flex; align-items: center; font-size: 1.7rem;
            padding: 3px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .brett-row:nth-child(even) { background: var(--zebra-bg); }

        /* --- SIDEBAR --- */
        .sidebar { display: flex; flex-direction: column; gap: 30px; }
        .cam-placeholder { width: 100%; aspect-ratio: 16/9; object-fit: cover; display: block; border-radius: 15px; }
        .cam-container { position: relative; width: 100%; margin-bottom: 15px; border-radius: 15px; overflow: hidden; }
        .cam-label {
            position: absolute; top: 15px; left: 15px; background: rgba(0, 0, 0, 0.6);
            color: white; padding: 5px 15px; border-radius: 10px; font-size: 1.4rem;
            font-weight: bold; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); z-index: 10;
        }

        /* --- TABELLE --- */
        thead th { font-size: 1.6rem; color: #aaa; font-weight: 400; text-transform: uppercase; padding: 10px; text-align: left; border-bottom: 2px solid rgba(255,255,255,0.2); }
        .tr-zebra { background: var(--zebra-bg); }
        .relegation-line { border-top: 5px solid rgba(255, 255, 255, 0.6) !important; }

        /* --- ZEITPLAN --- */
        .schedule-table td { 
            padding: 8px 10px; 
            font-size: 1.8rem; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            vertical-align: top; 
        }
        .time-col { color: var(--accent-gold); font-weight: bold; width: 120px; }
        .day-label { 
            color: #aaaaaa !important;
            font-weight: bold; 
            font-size: 2.8rem !important; 
            padding-bottom: 5px !important;
        }
        .spacer-row { height: 35px; border: none !important; }
    </style>
</head>
<body>

    <header class="main-page-header">
        <img src="img/uka.png" class="header-logo" alt="UKA">
        <div class="header-text-container">
            <h1>Schachbundesliga - Zentrale Endrunde</h1>
            <p>24. - 26. April 2026 · Willy-Brandt-Haus · Berlin</p>
        </div>
        <img src="img/uka.png" class="header-logo" alt="UKA">
    </header>

    <div class="dashboard-grid">
        <section class="glass-box">
            <div class="section-header" id="runde-header">Runde <?php echo $runde; ?></div>
            <div class="content-area" id="results-target"></div>
        </section>

        <aside class="sidebar">
            <div class="glass-box">
                <div class="section-header">Tabelle</div>
                <div class="content-area" id="table-target"></div>
            </div>
            <div class="glass-box">
                <div class="section-header">Live 🎥</div>
                <div class="content-area" style="padding: 15px;">
                    <div class="cam-container"><div class="cam-label">Atrium</div><img src="img/saal1.jpg" class="cam-placeholder"></div>
                    <div class="cam-container" style="margin-bottom: 0;"><div class="cam-label">H.-J. Vogel Saal</div><img src="img/saal2.jpg" class="cam-placeholder"></div>
                </div>
            </div>
            <div class="glass-box" style="flex-grow: 1;">
                <div class="section-header">Zeitplan</div>
                <div class="content-area">
                    <table class="schedule-table" style="width: 100%; border-collapse: collapse; color: #ddd;">
                        <tr><td class="day-label" colspan="2">Do. 23.04.2026</td></tr>
                        <tr><td class="time-col">19:30</td><td>1. Vorrunde Bundesliga-Blitz im Haus des Sports</td></tr>
                        <tr><td class="spacer-row" colspan="2"></td></tr>
                        <tr><td class="day-label" colspan="2">Fr. 24.04.2026</td></tr>
                        <tr><td class="time-col">16:00</td><td style="color:white; font-weight:bold;">Bundesliga Runde 13</td></tr>
                        <tr><td class="time-col">16:15</td><td>Live-Kommentierung mit GM Robert Rabiega</td></tr>
                        <tr><td class="time-col">16:30</td><td>UKA-Cup für geladene Gäste</td></tr>
                        <tr><td class="spacer-row" colspan="2"></td></tr>
                        <tr><td class="day-label" colspan="2">Sa. 25.04.2026</td></tr>
                        <tr><td class="time-col">14:00</td><td style="color:white; font-weight:bold;">Bundesliga Runde 14</td></tr>
                        <tr><td class="time-col">14:15</td><td>Live-Kommentierung mit GM Robert Rabiega</td></tr>
                        <tr><td class="time-col">14:30</td><td>Festveranstaltung 125 Jahre Berliner Schachverband</td></tr>
                        <tr><td class="time-col">19:30</td><td>2. Vorrunde Bundesliga-Blitz im Haus des Sports</td></tr>
                        <tr><td class="time-col">21:30</td><td>Halbfinale und Finale Bundesliga-Blitz</td></tr>
                        <tr><td class="spacer-row" colspan="2"></td></tr>
                        <tr><td class="day-label" colspan="2">So. 26.04.2026</td></tr>
                        <tr><td class="time-col">10:00</td><td>Kinder- und Jugendschnellturnier im Haus des Sports</td></tr>
                        <tr><td class="time-col">10:00</td><td style="color:white; font-weight:bold;">Bundesliga Runde 15</td></tr>
                        <tr><td class="time-col">10:15</td><td>Live-Kommentierung mit GM Robert Rabiega</td></tr>
                    </table>
                </div>
            </div>
        </aside>
    </div>

<script>
const aktuelleRunde = <?php echo $runde; ?>;
const teamLogos = {
    "Bayern München": "bayern.png", "Düsseldorfer SK": "dsk.png", "SC H-W-Neuwied": "neuwied.png",
    "SG Solingen": "solingen.png", "Sfr. Wolfhagen": "wolfhagen.png", "SV Deggendorf": "deggendorf.png",
    "Hamburger SK": "hsk.png", "Baden-Baden": "osg.png", "SF Deizisau": "deizisau.png",
    "SK Kirchweyhe": "kirchweihe.png", "FC St. Pauli": "pauli.png", "SC Viernheim": "viernheim.png",
    "USV TU Dresden": "dresden.png", "MSA Zugzwang": "msaz.png", "SF Berlin": "sfberlin.png", "Werder Bremen": "werder.png"
};

function formatScore(val) {
    if (val === null || val === undefined || val === "") return "-";
    return val.toString().replace('.5', '½').replace(',5', '½');
}

function getLogoHtml(teamName, pos) {
    const filename = teamLogos[teamName];
    if (!filename) return "";
    return `<img src="logos/${filename}" class="team-logo ${pos === 'heim' ? 'logo-heim' : 'logo-gast'}" alt="">`;
}

function updateDashboard() {
    fetch(`data_loader.php?runde=${aktuelleRunde}`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.error) return;

            const tableTarget = document.querySelector('#table-target');
            if (tableTarget && data.tabelle) {
                data.tabelle.sort((a, b) => parseInt(a.platz) - parseInt(b.platz));
                let html = '<table style="width: 100%; font-size: 1.8rem; border-collapse: collapse;"><thead><tr><th>Pl.</th><th>Mannschaft</th><th style="text-align: right;">MP</th><th style="text-align: right;">BP</th></tr></thead><tbody>';
                data.tabelle.forEach((t, i) => {
                    const zebra = (i % 2 === 1) ? 'class="tr-zebra"' : '';
                    const relegation = (parseInt(t.platz) === 14) ? 'relegation-line' : '';
                    
                    html += `<tr class="${zebra.includes('tr-zebra') ? 'tr-zebra' : ''} ${relegation}" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 10px 8px; color: #ffffff; font-weight: bold;">${t.platz}</td>
                        <td style="padding: 10px 8px;">${t.team}</td>
                        <td style="padding: 10px 8px; text-align: right; font-weight: bold; color: var(--accent-gold);">${t.mp}</td>
                        <td style="padding: 10px 8px; text-align: right; color: #ffffff;">${formatScore(t.bp)}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                tableTarget.innerHTML = html;
            }

            const resultsTarget = document.querySelector('#results-target');
            if (resultsTarget && data.begegnungen) {
                let html = '';
                data.begegnungen.forEach(m => {
                    const logoH = getLogoHtml(m.team_heim, 'heim');
                    const logoG = getLogoHtml(m.team_gast, 'gast');
                    const scoreH = m.score_heim !== null ? formatScore(m.score_heim) : "-";
                    const scoreG = m.score_gast !== null ? formatScore(m.score_gast) : "-";

                    html += `<div class="match-container">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 3.1rem; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px;">
                            <div style="width: 43%; display: flex; align-items: center; white-space: nowrap; overflow: hidden;">${logoH}${m.team_heim}</div>
                            <div class="score-box">${scoreH} : ${scoreG}</div>
                            <div style="width: 43%; display: flex; align-items: center; justify-content: flex-end; white-space: nowrap; overflow: hidden;">${m.team_gast}${logoG}</div>
                        </div>`;

                    if (m.bretter && m.bretter.length > 0) {
                        m.bretter.forEach(b => {
                            const flagH = b.country_h !== 'xx' ? `<img src="flags/${b.country_h}.svg" style="height:18px; margin-right:10px; vertical-align:middle; border:1px solid rgba(255,255,255,0.2);">` : '<div style="width:28px; margin-right:10px;"></div>';
                            const flagG = b.country_g !== 'xx' ? `<img src="flags/${b.country_g}.svg" style="height:18px; margin-left:10px; vertical-align:middle; border:1px solid rgba(255,255,255,0.2);">` : '<div style="width:28px; margin-left:10px;"></div>';

                            html += `<div class="brett-row" style="color: white;">
                                <div style="flex: 1; display: flex; align-items: center; white-space: nowrap;">${flagH}${b.titel_h ? '<span style="color:#bbb; font-size:1.4rem; margin-right:5px;">'+b.titel_h+'</span>' : ''}${b.name_h}</div>
                                <div style="width: 95px; text-align: center; font-weight: bold; color: white; background: rgba(255,255,255,0.05); border-radius: 5px; margin: 0 12px;">${formatScore(b.erg_h)} : ${formatScore(b.erg_g)}</div>
                                <div style="flex: 1; display: flex; align-items: center; justify-content: flex-end; white-space: nowrap;">${b.name_g}${b.titel_g ? '<span style="color:#bbb; font-size:1.4rem; margin-left:5px;">'+b.titel_g+'</span>' : ''}${flagG}</div>
                            </div>`;
                        });
                    } else {
                        for(let i=1; i<=8; i++) {
                            html += `<div class="brett-row" style="color: rgba(255,255,255,0.2);">
                                <div style="flex: 1;">${i}</div>
                                <div style="width: 95px; text-align: center;">- : -</div>
                                <div style="flex: 1; text-align: right;"></div>
                            </div>`;
                        }
                    }
                    html += `</div>`;
                });
                resultsTarget.innerHTML = html;
            }
        })
        .catch(err => console.error("Fetch Error:", err));
}
updateDashboard();
setInterval(updateDashboard, 60000);
</script>
</body>
</html>
