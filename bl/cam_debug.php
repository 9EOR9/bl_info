<?php
// cam_debug.php
$camIp = "192.168.10.37";
$auth = base64_encode("admin:admin"); // Bitte prüfen, ob Passwort stimmt!
$url = "http://{$camIp}/cmdparse";

// Test-Szenarien für "RECHTS"
$tests = [
    "Form-Data" => ["cmd" => 1, "param" => 4, "speed" => 40],
    "JSON-Body" => json_encode(["cmd" => 1, "param" => 4, "speed" => 40]),
    "Alt-Names" => ["yon_statu" => 4, "statu_h" => 2, "statu_v" => 0]
];

foreach ($tests as $name => $data) {
    echo "Teste $name... ";
    
    $isJson = ($name === "JSON-Body");
    $content = $isJson ? $data : http_build_query($data);
    $contentType = $isJson ? "application/json" : "application/x-www-form-urlencoded";

    $opts = ["http" => [
        "method" => "POST",
        "header" => "Authorization: Basic $auth\r\nContent-Type: $contentType\r\n",
        "content" => $content,
        "timeout" => 2
    ]];

    $res = @file_get_contents($url, false, stream_context_create($opts));
    echo ($res !== false) ? "✅ Antwort: " . strip_tags($res) : "❌ Fehlgeschlagen";
    echo "<br>";
}
