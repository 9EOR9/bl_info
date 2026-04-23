/**
 * FOYER JS - STABLE VERSION 2026
 */

// 1. GLOBALE VARIABLEN & DATEN
const playerPool = [
    "20170719-wladimir_fedoseev_3.webp", "duda.webp", "indjic.webp", "martirosyan rosen.webp", "nguyen.webp", "svane fred dsb.webp",
    "abdusattorov ootes1.webp", "eljanov shotourman fide2.webp", "jacek_tomczak_0.webp", "mateusz_bartel_2.webp", "nielsen_0.webp", "theodorou.webp",
    "anand riga.webp", "emil_schmidek_0.webp", "kamsky.webp", "matthias_bluebaum_3.webp", "Nils Grandelius.webp", "van wely kniest.webp",
    "andre_volokitin_2.webp", "erigaisi.webp", "keymer.webp", "murzin_0.webp", "pavlidis.webp",
    "bjerre bonhage.webp", "fabiano-caruana.webp", "livaic schmidt.webp", "mvl.webp", "pentala_harikrishna.webp",
    "bologan_0.webp", "firouzja walusza thumb.webp", "livie-dieter_nisipeanu.webp", "navara.webp", "ponomariov.webp",
    "carlsen ootes fide3.webp", "giri werner.webp", "luke_mcshane_1.webp", "neiksans_arturs_1.webp", "SCV_Shakhriyar-Mamedyarov.webp"
];

const teamLogos = { 
    "Bayern München": "bayern.png", "Düsseldorfer SK": "dsk.png", "SC H-W-Neuwied": "neuwied.png", 
    "SG Solingen": "solingen.png", "Sfr. Wolfhagen": "wolfhagen.png", "SV Deggendorf": "deggendorf.png", 
    "Hamburger SK": "hsk.png", "Baden-Baden": "osg.png", "SF Deizisau": "deizisau.png", 
    "SK Kirchweyhe": "kirchweihe.png", "FC St. Pauli": "pauli.png", "SC Viernheim": "viernheim.png", 
    "USV TU Dresden": "dresden.png", "MSA Zugzwang": "msaz.png", "SF Berlin": "sfberlin.png", "Werder Bremen": "werder.png" 
};

let currentQueue = [];
const slides = document.querySelectorAll('.slide');
let currentSlideIndex = 0;

// HELPER: Logo Pfad
function getLogoHtml(teamName) {
    const filename = teamLogos[teamName];
    return filename ? `<img src="logos/${filename}" class="team-logo-small">` : `<div style="width:50px"></div>`;
}

// HELPER: Nächste Spieler aus Pool ziehen
function getNextPlayers(count) {
    let selected = [];
    for (let i = 0; i < count; i++) {
        if (currentQueue.length === 0) {
            currentQueue = [...playerPool].sort(() => Math.random() - 0.5);
        }
        selected.push(currentQueue.shift());
    }
    return selected;
}

// 2. FUNKTION: SPIELER-BILDER AKTUALISIEREN (Slide 1)
function updatePlayers() {
    const nextSet = getNextPlayers(10);
    for (let i = 1; i <= 10; i++) {
        const container = document.getElementById('c' + i);
        if (!container) continue;

        const oldImg = container.querySelector('img.active');
        const newImg = document.createElement('img');
        newImg.src = "img/players/" + nextSet[i-1];
        container.appendChild(newImg);

        setTimeout(() => {
            newImg.classList.add('active');
            if (oldImg) {
                oldImg.classList.remove('active');
                // Altes Bild nach der Transition entfernen (Wichtig gegen Memory-Leak!)
                setTimeout(() => { 
                    if(oldImg.parentNode === container) container.removeChild(oldImg); 
                }, 2000);
            }
        }, 100);
    }
}

// 3. FUNKTION: LAGEPLAN DATEN FETCHEN (Slide 2)
function updateLageplan() {
    const vogelTable = document.getElementById('list-vogel');
    const atriumTable = document.getElementById('list-atrium');
    if (!vogelTable || !atriumTable) return;

    // Nutze RUNDE (muss in foyer.php als const definiert sein)
    fetch(`data_loader.php?runde=${RUNDE}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.begegnungen) return;

            document.getElementById('info-vogel').innerText = `Runde ${RUNDE}`;
            document.getElementById('info-atrium').innerText = `Runde ${RUNDE}`;

            let htmlVogel = ""; 
            let htmlAtrium = "";

            data.begegnungen.forEach((m, index) => {
                const scoreH = m.score_heim !== null ? m.score_heim.toString().replace('.5', '½') : "-";
                const scoreG = m.score_gast !== null ? m.score_gast.toString().replace('.5', '½') : "-";

                const rowHtml = `
                    <tr>
                        <td class="logo-col">${getLogoHtml(m.team_heim)}</td>
                        <td class="team-name" style="text-align:left; padding-left:15px;">${m.team_heim}</td>
                        <td class="score-box-cell"><div class="score-box">${scoreH} : ${scoreG}</div></td>
                        <td class="team-name" style="text-align:right; padding-right:15px;">${m.team_gast}</td>
                        <td class="logo-col">${getLogoHtml(m.team_gast)}</td>
                    </tr>`;

                if (index > 3) htmlAtrium += rowHtml; else htmlVogel += rowHtml;
            });

            atriumTable.innerHTML = htmlAtrium;
            vogelTable.innerHTML = htmlVogel;
        })
        .catch(err => console.error("Daten-Ladefehler:", err));
}

// 4. FUNKTION: DYNAMISCHE SEITEN-ROTATION
function rotateSlides() {
    // Aktuellen Slide ausblenden
    slides[currentSlideIndex].classList.remove('active');
    
    // Index berechnen
    currentSlideIndex = (currentSlideIndex + 1) % slides.length;
    
    // Nächsten Slide einblenden
    const nextSlide = slides[currentSlideIndex];
    nextSlide.classList.add('active');

    // Trigger: Was soll beim Erscheinen passieren?
    if (nextSlide.id === 'slide-1') updatePlayers();
    if (nextSlide.id === 'slide-2') updateLageplan();

    // Nächsten Wechsel planen (Rekursion)
    const duration = parseInt(nextSlide.getAttribute('data-duration')) || 10000;
    setTimeout(rotateSlides, duration);
}

// 5. INITIALISIERUNG
window.addEventListener('DOMContentLoaded', () => {
    // 1. Einmal Daten laden
    updatePlayers();
    updateLageplan();
    
    // 2. Ersten Slide aktivieren
    slides[0].classList.add('active');
    
    // 3. Rotation starten
    const firstDuration = parseInt(slides[0].getAttribute('data-duration')) || 8000;
    setTimeout(rotateSlides, firstDuration);
    
    // 4. Hintergrund-Update für die Tabellen (alle 60 Sek)
    // Aber NICHT für updatePlayers (das triggert rotateSlides selbst)!
    setInterval(updateLageplan, 60000);
});
