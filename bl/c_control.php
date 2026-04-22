<?php
// camera_control.php
if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    $val = intval($_GET['val'] ?? 0);

    switch ($cmd) {
        case 'pan':
            shell_exec("v4l2-ctl -d /dev/video0 --set-ctrl=pan_speed=$val");
            break;
        case 'tilt':
            shell_exec("v4l2-ctl -d /dev/video0 --set-ctrl=tilt_speed=$val");
            break;
        case 'stop':
            shell_exec("v4l2-ctl -d /dev/video0 --set-ctrl=pan_speed=0");
            shell_exec("v4l2-ctl -d /dev/video0 --set-ctrl=tilt_speed=0");
            break;
        case 'zoom':
            shell_exec("v4l2-ctl -d /dev/video0 --set-ctrl=zoom_absolute=$val");
            break;
    }
    echo "OK";
}
?>
