<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Wir versuchen, eine einzige Eigenschaft der Kamera abzufragen. 
// Wenn die Kamera "No Signal" zeigt oder aus ist, antwortet der Treiber oft mit einem Fehlercode.
exec("v4l2-ctl -d /dev/video0 --get-ctrl=brightness 2>&1", $output, $return_var);

if ($return_var !== 0) {
    echo json_encode(['status' => 'offline']);
} else {
    echo json_encode(['status' => 'online']);
}
?>
