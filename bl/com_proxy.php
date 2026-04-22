<?php
// cam_proxy.php
header('Content-Type: application/json');

// --- KONFIGURATION ---
$camIp = "192.168.10.37";
$user  = "admin"; 
$pass  = "admin"; // <--- Falls geändernt, hier anpassen!

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'stop';

// --- BEFEHL-MAPPING ---
// Diese Werte (1, 2, 3, 4) sind Standard für Hisilicon/Chameye. 
// Falls es nicht klappt, schau im Network-Tab unter "Payload" nach den Zahlen.
$commands = [
    'up'      => ["cmd" => 1, "param" => 1, "speed" => 40],
    'down'    => ["cmd" => 1, "param" => 2, "speed" => 40],
    'left'    => ["cmd" => 1, "param" => 3, "speed" => 40],
    'right'   => ["cmd" => 1, "param" => 4, "speed" => 40],
    'stop'    => ["cmd" => 1, "param" => 0, "speed" => 0],
    'zoomIn'  => ["cmd" => 2, "param" => 1, "speed" => 40],
    'zoomOut' => ["cmd" => 2, "param" => 2, "speed" => 40],
];

if (isset($commands[$cmd])) {
    $url = "http://{$camIp}/cmdparse";
    
    // Daten für den POST-Body vorbereiten (z.B. cmd=1&param=4&speed=40)
    $postData = http_build_query($commands[$cmd]);
    $auth = base64_encode("$user:$pass");

    $opts = [
        "http" => [
            "method"  => "POST",
            "header"  => "Authorization: Basic $auth\r\n" .
                         "Content-Type: application/x-www-form-urlencoded\r\n" .
                         "Content-Length: " . strlen($postData) . "\r\n",
            "content" => $postData,
            "timeout" => 2
        ]
    ];

    $context = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        echo json_encode(["status" => "error", "msg" => $error['message'] ?: "Kamera antwortet nicht"]);
    } else {
        // Die Kamera antwortet oft mit "OK" oder einem XML-Schnipsel
        echo json_encode(["status" => "ok", "sent" => $postData, "cam_response" => strip_tags($result)]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Befehl unbekannt"]);
}
