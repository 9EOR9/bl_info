<?php 
// Zentrale Steuerung der Rundennummer
$runde = isset($_GET['runde']) ? intval($_GET['runde']) : 13;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Schach Bundesliga Endrunde - Lageplan</title>
    <style>
        @font-face {
            font-family: 'Roboto Condensed';
            src: url('fonts/RobotoCondensed-VariableFont_wght.ttf') format('truetype-variations');
            font-weight: 100 900;
        }

        :root {
            --bg-overlay: rgba(10, 10, 10, 0.9);
            --glass-bg: rgba(15, 15, 15, 0.85);
            --glass-blur: 2px;
            --accent-light: rgba(255, 255, 255, 0.25);
            --line-thickness: 1px;
            --accent-gold: #c5a059;
            --text-black: #000000;
            --border-radius: 40px;
            --header-white: #ffffff;
            --gold: #c5a059;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Roboto Condensed', sans-serif;
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)), url('img/wbh.jpg') center/cover no-repeat;
            width: 2160px; 
            height: 3840px;
            overflow: hidden;
            display: flex; 
            flex-direction: column; 
            line-height: 1.1;
        }

        /* --- HEADER --- */
        .header-bar {
            width: 100%; 
            background: var(--header-white);
            padding: 60px 0; 
            text-align: center;
            border-bottom: 20px solid var(--gold);
            box-shadow: 0 15px 50px rgba(0,0,0,0.6);
            z-index: 100;
        }
        .header-bar h1 { color: #000000; font-size: 140px; text-transform: uppercase; letter-spacing: 12px; }
        .header-bar h2 { color: #000000; font-size: 90px; text-transform: uppercase; margin-top: 10px; font-weight: 300; }

        /* --- FOOTER --- */
        .footer-bar {
            width: 100%; 
            background: var(--header-white);
            padding: 40px 0; 
            text-align: center;
            border-top: 20px solid var(--gold);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: auto;
        }
        .logo-uka-footer { height: 230px; width: auto; object-fit: contain; }

        /* --- LAYOUT GRID --- */
        .content-wrapper {
            display: flex;
            flex: 1;
            padding: 20px 50px;
            gap: 30px;
        }
        .column-left { width: 50%; display: flex; align-items: center; justify-content: center; }
        .lageplan-img { width: 100%; height: auto; max-height: 90%; object-fit: contain; filter: drop-shadow(0 30px 60px rgba(0,0,0,0.7)); }
        
        /* ABSTAND RECHTS VERGRÖSSERT */
        .column-right { 
            width: 50%; 
            display: flex; 
            flex-direction: column; 
            gap: 70px; /* Von 15px auf 40px erhöht */
            justify-content: center; 
        }

        /* --- GLASS BOX STYLE --- */
        .glass-box {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border: var(--line-thickness) solid var(--accent-light);
            border-radius: var(--border-radius);
            overflow: hidden; 
            display: flex; 
            flex-direction: column;
        }
        .section-header {
            background: linear-gradient(to bottom, rgba(235, 235, 235, 0.5) 0%, rgba(255, 255, 255, 0.78) 100%) !important;
            color: var(--text-black) !important;
            font-size: 3rem; font-weight: 700; padding: 10px; text-align: center; text-transform: uppercase;
        }
        .content-area { padding: 15px 25px; color: white; }

        .info-line { 
            color: var(--accent-gold); 
            font-weight: 700; 
            display: block; 
            padding-bottom: 10px;
            margin-bottom: 15px; 
            font-size: 2.2rem; 
            text-align: center; 
            border-bottom: 3px solid var(--gold);
        }

        /* --- TABELLE --- */
        .match-table { width: 100%; border-collapse: collapse; }
        .match-table tr:nth-child(even) { background: rgba(255, 255, 255, 0.05); }
        .match-table td {
            padding: 2px 10px; 
            vertical-align: middle;
            font-size: 2.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .team-name { font-weight: 700; }
        .logo-col { width: 70px; text-align: center; }
        .team-logo-small { height: 55px; width: auto; object-fit: contain; }

        .score-box-cell { width: 160px; text-align: center; }
        .score-box {
            display: inline-block;
            width: 130px; height: 50px; line-height: 50px;
            text-align: center; background: rgba(0, 0, 0, 0.8);
            border-radius: 8px; border: 2px solid var(--accent-gold);
            font-size: 2.2rem; font-weight: bold; color: var(--accent-gold);
        }

        .portrait-rabiega { width: 220px; height: auto; border-radius: 20px; margin: 10px 0; border: 2px solid var(--accent-gold); display: block; }
        .kommentierung-content { font-size: 2.2rem; text-align: center; line-height: 1.2; }
    </style>
</head>
<body>

    <div class="header-bar">
        <h1>Schachbundesliga<br>Zentrale Endrunde</h1>
        <h2>Willy-Brandt-Haus &bull; Berlin</h2> 
    </div>

    <div class="content-wrapper">
        <div class="column-left">
            <img src="img/wbh_lageplan.png" class="lageplan-img" alt="Lageplan">
        </div>

        <div class="column-right">
            <div class="glass-box">
                <div class="section-header">Hans-Jochen Vogel Saal</div>
                <div class="content-area">
                    <b class="info-line" id="info-vogel">Lade Daten...</b>
                    <table class="match-table" id="list-vogel"></table>
                </div>
            </div>

            <div class="glass-box">
                <div class="section-header">Casino</div>
                <div class="content-area" style="text-align: center; font-size: 2.2rem; line-height: 1.2;">
                    <b class="info-line">Freitag 24.04 16:30</b>
                    UKA-Cup für geladene Gäste<br><br>
                    <b class="info-line">Samstag 25.04 14:30</b>
                    Festveranstaltung 125 Jahre Berliner Schachverband
                </div>
            </div>

            <div class="glass-box">
                <div class="section-header">Live-Kommentierung</div>
                <div class="content-area kommentierung-content">
                    <b class="info-line">Analyse</b>
                    <b>Live-Kommentierung mit GM Robert Rabiega</b>
                    <center><img src="img/rabiega.jpg" class="portrait-rabiega" alt="GM Robert Rabiega"></center>
                    Start jeweils 15 Minuten nach Rundenbeginn
                </div>
            </div>

            <div class="glass-box">
                <div class="section-header">Atrium</div>
                <div class="content-area">
                    <b class="info-line" id="info-atrium">Lade Daten...</b>
                    <table class="match-table" id="list-atrium"></table>
                </div>
            </div>

            <div class="glass-box">
                <div class="section-header">Eingang</div>
                <div class="content-area" style="text-align: center; font-size: 2.1rem; line-height: 1.2;">
                    <b class="info-line">Registrierung und Info-Desk</b>
                    Besucher:innen dürfen im Willy-Brandt-Haus keine Taschen, Rucksäcke oder Koffer mitführen, die die Maße 32 cm x 45 cm x 18 cm überschreiten. Wir bitten um Verständnis, dass es vor Ort keine Möglichkeit der Abgabe von größeren Taschen oder Gepäckstücken gibt. Alle Taschen werden beim Einlass durch das Sicherheitspersonal kontrolliert. Besucher:innen müssen einen amtlichen Lichtbildausweis mitführen.<br><br>
                    <b>Bitte sorgen Sie dafür, dass Ihr Mobiltelefon ausgeschaltet ist!</b><br><br>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bar">
        <img src="img/UKA_Logo.png" class="logo-uka-footer" alt="UKA Logo">
    </div>

    <script>
        const runde = <?php echo $runde; ?>;
        const rundenTermine = { 13: "Freitag, 24.04 - 16:00 Uhr", 14: "Samstag, 25.04 - 14:00 Uhr", 15: "Sonntag, 26.04 - 10:00 Uhr" };
        const datumText = rundenTermine[runde] || "";
        const teamLogos = { "Bayern München": "bayern.png", "Düsseldorfer SK": "dsk.png", "SC H-W-Neuwied": "neuwied.png", "SG Solingen": "solingen.png", "Sfr. Wolfhagen": "wolfhagen.png", "SV Deggendorf": "deggendorf.png", "Hamburger SK": "hsk.png", "Baden-Baden": "osg.png", "SF Deizisau": "deizisau.png", "SK Kirchweyhe": "kirchweihe.png", "FC St. Pauli": "pauli.png", "SC Viernheim": "viernheim.png", "USV TU Dresden": "dresden.png", "MSA Zugzwang": "msaz.png", "SF Berlin": "sfberlin.png", "Werder Bremen": "werder.png" };

        function getLogoHtml(teamName) {
            const filename = teamLogos[teamName];
            return filename ? `<img src="logos/${filename}" class="team-logo-small">` : `<div style="width:50px"></div>`;
        }

        function updateLageplan() {
            fetch(`data_loader.php?runde=${runde}`)
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.begegnungen) return;
                    const vogelTable = document.getElementById('list-vogel');
                    const atriumTable = document.getElementById('list-atrium');
                    document.getElementById('info-vogel').innerText = `Runde ${runde} - ${datumText}`;
                    document.getElementById('info-atrium').innerText = `Runde ${runde} - ${datumText}`;
                    let htmlVogel = ""; let htmlAtrium = "";
                    data.begegnungen.forEach((m, index) => {
                        const scoreH = m.score_heim !== null ? m.score_heim.toString().replace('.5', '½') : "-";
                        const scoreG = m.score_gast !== null ? m.score_gast.toString().replace('.5', '½') : "-";
                        const rowHtml = `<tr><td class="logo-col">${getLogoHtml(m.team_heim)}</td><td class="team-name" style="text-align:left; padding-left:15px;">${m.team_heim}</td><td class="score-box-cell"><div class="score-box">${scoreH} : ${scoreG}</div></td><td class="team-name" style="text-align:right; padding-right:15px;">${m.team_gast}</td><td class="logo-col">${getLogoHtml(m.team_gast)}</td></tr>`;
                        if (index > 3) htmlAtrium += rowHtml; else htmlVogel += rowHtml;
                    });
                    atriumTable.innerHTML = htmlAtrium;
                    vogelTable.innerHTML = htmlVogel;
                });
        }
        updateLageplan();
        setInterval(updateLageplan, 60000);
    </script>
</body>
</html>
