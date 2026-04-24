<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Regie-Steuerung (Split-View)</title>
    <style>
        body { font-family: 'Roboto Condensed', sans-serif; background: #111; color: white; margin: 0; display: flex; flex-direction: column; height: 100vh; }
        
        /* Header */
        .header { background: #222; padding: 10px; text-align: center; border-bottom: 2px solid #444; }
        
        /* Video Container */
        .video-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 10px; flex-shrink: 0; }
        .cam-box { border: 3px solid #333; border-radius: 8px; overflow: hidden; position: relative; background: #000; cursor: pointer; }
        .cam-box.active { border-color: #c5a059; box-shadow: 0 0 15px rgba(197, 160, 89, 0.5); }
        .cam-box img { width: 100%; display: block; aspect-ratio: 16/9; object-fit: cover; }
        .label { position: absolute; top: 5px; left: 5px; background: rgba(0,0,0,0.7); padding: 2px 8px; font-size: 0.9rem; border-radius: 4px; }

        /* Controls Area */
        .controls-area { flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #1a1a1a; padding: 20px; }
        .status-msg { margin-bottom: 15px; color: #c5a059; font-weight: bold; font-size: 1.2rem; }
        
        .ptz-grid { display: grid; grid-template-columns: repeat(3, 80px); gap: 10px; }
        .btn { background: #333; border: 1px solid #555; color: white; padding: 20px; border-radius: 12px; font-size: 1.5rem; cursor: pointer; touch-action: manipulation; }
        .btn:active { background: #c5a059; color: black; }
        .btn-stop { color: #ff4444; font-weight: bold; }
        
        .zoom-row { margin-top: 25px; display: flex; gap: 15px; }
        .btn-zoom { width: 100px; font-size: 1.1rem; padding: 15px; }
    </style>
</head>
<body>

<div class="header">SBL ZENTRALE ENDRUNDE - KAMERASTEUERUNG</div>

<div class="video-grid">
    <div id="box-1" class="cam-box active" onclick="setActive(1, 'pi5-1')">
        <div class="label">Atrium (Pi5-1)</div>
        <img src="http://192.168.0.29:8080/stream" onerror="this.src='img/cam_offline.jpg'">
    </div>
    <div id="box-2" class="cam-box" onclick="setActive(2, 'pi5-2')">
        <div class="label">5. OG (Pi5-2)</div>
        <img src="http://192.168.0.18:8080/stream" onerror="this.src='img/cam_offline.jpg'">
    </div>
</div>

<div class="controls-area">
    <div class="status-msg" id="status">Steuerung: ATRIUM</div>
    
    <div class="ptz-grid">
        <div></div><button class="btn" onclick="send('tilt', 6)">▲</button><div></div>
        <button class="btn" onclick="send('pan', -6)">◀</button>
        <button class="btn btn-stop" onclick="send('stop', 0)">■</button>
        <button class="btn" onclick="send('pan', 6)">▶</button>
        <div></div><button class="btn" onclick="send('tilt', -6)">▼</button><div></div>
    </div>

    <div class="zoom-row">
        <button class="btn btn-zoom" onclick="send('zoom', 1)">Zoom -</button>
        <button class="btn btn-zoom" onclick="send('zoom', 50)">Reset</button>
        <button class="btn btn-zoom" onclick="send('zoom', 100)">Zoom +</button>
    </div>
</div>

<script>
    let activePi = 'pi5-1'; // Start-Pi
    const statusText = document.getElementById('status');

    function setActive(id, hostname) {
        activePi = hostname;
        // Optisches Feedback
        document.querySelectorAll('.cam-box').forEach(box => box.classList.remove('active'));
        document.getElementById('box-' + id).classList.add('active');
        // Text-Update
        statusText.innerText = "Steuerung: " + (id === 1 ? "ATRIUM" : "5. OG");
    }

    function send(cmd, val) {
        // Der Befehl wird immer an den aktuell ausgewählten Pi gesendet
        // Wir nutzen hier den Tailscale-Hostnamen dynamisch
        const url = `http://${activePi}/bl/c_control.php?cmd=${cmd}&val=${val}`;
        fetch(url, { mode: 'no-cors' }); // no-cors verhindert Browser-Sperren bei Cross-Origin
    }
</script>

</body>
</html>
