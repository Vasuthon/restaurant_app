<?php
// container Docker ตรวจ IP ตัวเองไม่ได้ (จะได้ IP วง docker bridge ไม่ใช่ IP วงแลนจริง)
// เลยรับค่าจาก docker-up.ps1 ผ่าน HOST_LAN_IP แทน ถ้าไม่มีค่อย fallback ไปตรวจเอง
$ip = getenv('HOST_LAN_IP') ?: null;

if (!$ip) {
    $fp = @stream_socket_client('udp://8.8.8.8:53', $errno, $errstr, 1);
    if ($fp) {
        $name = stream_socket_get_name($fp, false);
        fclose($fp);
        if ($name) {
            $parts = explode(':', $name);
            $ip = $parts[0] ?? null;
        }
    }
}

header('Content-Type: application/json');

if (!$ip || $ip === '0.0.0.0') {
    echo json_encode(["status" => "error", "message" => "ตรวจ IP วงแลนไม่สำเร็จ"]);
    exit();
}

echo json_encode(["status" => "success", "ip" => $ip]);
?>
