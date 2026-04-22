<?php
// check_cam.php
header('Content-Type: application/json');

// Wir prüfen, ob die Kamera auf Steuerbefehle reagiert
// Das ist ein guter Indikator, ob sie im Standby ist
$output = shell_exec("v4l2-ctl -d /dev/video0 --get-ctrl=brightness 2>&1");

if (strpos($output, 'failed') !== false || $output === null) {
    echo json_encode(['status' => 'offline']);
} else {
    echo json_encode(['status' => 'online']);
}
?>
