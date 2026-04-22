<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBL Infoscreen - Saal 1</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&display=swap');

        :root {
            --bg-page: #000000; 
            --header-bg-gradient: linear-gradient(to bottom, #d1d1d1 0%, #ffffff 100%);
            --text-black: #000000;
            --accent-gold: #ffcc00;
            --border-radius: 25px;
            --col-nr-width: 60px;
            --col-logo-space: 140px; 
            --col-res-width: 180px;
            --line-thickness: 2px;
            --header-font-size: 2.8rem;
            
            /* Glass-Look Variablen */
            --glass-blur: 5px;
            --bg-overlay: rgba(0, 0, 0, 0.2);
        }

        html, body {
            margin: 0; padding: 0;
            /* Hintergrundbild-Logik */
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)), 
                        url('img/wbh.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto Condensed', sans-serif;
            color: white;
            overflow-x: hidden;
        }

        body { display: flex; flex-direction: column; padding: 20px; box-sizing: border-box; }

				/* --- SECTION HEADER IN DEN INCLUDES (MIT TRANSPARENZ) --- */
				.section-header {
						/* Verlauf von fast weißem Grau zu Weiß, jeweils mit 85% Deckkraft */
						background: linear-gradient(
								to bottom, 
								rgba(209, 209, 209, 0.75) 0%, 
								rgba(255, 255, 255, 0.85) 100%
						) !important;
						
						/* Der Blur-Effekt wirkt auch durch den Header-Balken */
						backdrop-filter: blur(5px);
						-webkit-backdrop-filter: blur(5px);

						color: var(--text-black);
						font-size: var(--header-font-size);
						font-weight: 700;
						padding: 10px 20px;
						text-transform: uppercase;
						border-bottom: var(--line-thickness) solid var(--accent-gold);
						text-align: center;
				}

        .header-section-wrapper {
            border: var(--line-thickness) solid var(--accent-gold);
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 25px;
            background: var(--header-bg-gradient);
            padding: 20px 40px;
        }
        .header-content { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .header-logo { height: 90px; width: auto; }
        .header-main-title { font-size: 4.5rem; font-weight: 700; text-transform: uppercase; margin: 0; color: var(--text-black); text-align: center; flex: 1; }
        .header-black-line { width: 100%; height: 3px; background-color: #000; margin: 10px 0; }
        .header-subtitle { font-size: 2rem; font-weight: 400; color: #000; text-align: center; margin: 0; }

        /* --- DEINE SEKTIONS-WRAPPER MIT ZUSÄTZLICHEM GLASS-EFFEKT --- */
        .atrium-section-wrapper, 
        .main-matches-box, 
        .table-block-wrapper {
            /* Wir behalten deine Struktur, fügen aber Transparenz hinzu */
            background-color: rgba(26, 26, 26, 0.8) !important; 
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: var(--line-thickness) solid var(--accent-gold);
            border-radius: 15px; 
            overflow: hidden; 
            margin-bottom: 25px;
        }

        /* --- DEINE ORIGINALEN GRID- UND TABELLEN-STYLES (Unverändert) --- */
        .atrium-content-row { 
            display: grid; 
            grid-template-columns: 800px 1fr 60px; 
            align-items: stretch; 
            height: 450px; 
        }
        .webcam-container { 
            background: #000; 
            display: flex; 
            position: relative; 
            border-right: var(--line-thickness) solid var(--accent-gold); 
            overflow: hidden; 
        }
        .webcam-container iframe {
            width: 100%; height: 100%; border: none;
            position: absolute; top: 0; left: 0;
            transform: scale(1.0); 
        }
        .saal-label { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.8); color: #fff; padding: 3px 10px; font-size: 1.2rem; font-weight: 700; border-radius: 4px; z-index: 10; }
        
        .atrium-table { width: 100%; border-collapse: collapse; font-size: 2.6rem; background-color: transparent; height: 100%; font-weight: bold;}
        .atrium-table td { padding: 8px 15px; border-bottom: 1px solid #252525; color: #eee; vertical-align: middle; }
        .atrium-table tr:nth-child(even) { background-color: rgba(255,255,255,0.05); }
        .atrium-logo { width: 55px; height: 55px; object-fit: contain; }
        .atrium-score { text-align: center; color: var(--accent-gold) !important; background: rgba(0,0,0,0.4); width: 150px; border-left: 1px solid #333; border-right: 1px solid #333; font-weight: bold; }

        .match-grid-row { 
            display: grid; 
            grid-template-columns: var(--col-nr-width) var(--col-logo-space) 1.2fr var(--col-res-width) 1.2fr var(--col-logo-space) var(--col-nr-width); 
            align-items: center; width: 100%; 
        }
        .match-header-row { background: linear-gradient(to bottom, #3a3a3a 0%, #1e1e1e 100%); height: 90px; border-bottom: var(--line-thickness) solid var(--accent-gold); }
        .match-header-row.with-top-line { border-top: var(--line-thickness) solid var(--accent-gold); }
        .match-team-logo { width: 65px; height: 65px; object-fit: contain; }
        .match-team-name-header { font-size: 2.5rem; font-weight: 700; color: #fff; }
        .match-score-badge { background: #000; color: var(--accent-gold); font-size: 3rem; font-weight: 700; height: 100%; display: flex; align-items: center; justify-content: center; border-left: 1px solid #333; border-right: 1px solid #333; }
        
        .board-row { border-bottom: 1px solid #252525; height: 50px; }
        .board-row:nth-child(even) { background-color: rgba(255,255,255,0.03); }
        .col-nr { color: #888; text-align: center; font-size: 1.1rem; }
        .col-flag-container { display: flex; justify-content: center; align-items: center; }
        .col-name-l { color: #eee; font-size: 1.9rem; text-align: left; padding-left: 10px; white-space: nowrap; overflow: hidden; }
        .col-name-r { color: #eee; font-size: 1.9rem; text-align: right; padding-right: 10px; white-space: nowrap; overflow: hidden; }
        .col-res { background: rgba(0,0,0,0.5); height: 100%; display: flex; align-items: center; justify-content: center; color: var(--accent-gold); font-weight: 700; font-size: 1.9rem; border-left: 1px solid #333; border-right: 1px solid #333; }
        .flag { width: 28px; border-radius: 2px; }

        .split-table-container { display: grid; grid-template-columns: 1fr 1fr; }
        .split-table-container > div:first-child { border-right: var(--line-thickness) solid var(--accent-gold); }
        .league-table { width: 100%; border-collapse: collapse; font-size: 2rem; background-color: transparent; }
        .league-table th { font-size: 1.2rem; color: #888; font-weight: 400; text-transform: uppercase; padding: 8px 15px; border-bottom: 1px solid #444; }
        .league-table td { padding: 6px 15px; border-bottom: 1px solid #252525; color: #eee; }
        .col-rank { width: 40px; text-align: center !important; color: var(--accent-gold); font-weight: 700; }
        .col-points { text-align: right; width: 60px; }
        .relegation-line td { border-top: 3px solid #ffffff !important; }
        
        .table-footer { background: var(--header-bg-gradient); color: var(--text-black); padding: 10px 40px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; border-top: var(--line-thickness) solid var(--accent-gold); }
        .sponsor-logo { height: 55px; margin-left: 30px; }
    </style>
</head>
<body>

    <div class="header-section-wrapper">
        <div class="header-content">
            <img src="sfb.png" class="header-logo">
            <h1 class="header-main-title">Schachbundesliga - Zentrale Endrunde</h1>
            <img src="sbl.png" class="header-logo">
        </div>
        <div class="header-black-line"></div>
        <p class="header-subtitle">24. - 26. April 2026 im Willy-Brandt-Haus Berlin</p>
    </div>

    <?php require "atrium.html" ?>
    <?php require "saal2.html" ?>
    <?php require "tabelle.html" ?>

    <div class="table-block-wrapper" style="margin-top: -25px; border-top: none; border-radius: 0 0 15px 15px;">
        <div class="table-footer">mit freundlicher Unterstützung von <img src="logos/uka.png" class="sponsor-logo"></div>
    </div>
</body>
</html>
