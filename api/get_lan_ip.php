<?php
// คืนค่า IP ของเครื่อง host ในวงแลนปัจจุบัน (ไม่ใช่ 127.0.0.1) เพื่อให้พนักงานรู้ลิงก์ที่มือถือลูกค้า
// ในวงไวไฟเดียวกันเข้าถึงได้ โดยไม่ต้องเปิด cmd รัน ipconfig เอง
//
// เว็บจริงรันอยู่ใน container ของ Docker ซึ่งมี network namespace แยกจาก host เอง ถ้าตรวจ IP จากภายใน
// container ตรงๆ จะได้ IP วง docker bridge (เช่น 172.x) ซึ่งเครื่องอื่นในวงแลนเข้าไม่ถึง จึงต้องให้สคริปต์
// docker-up.ps1 (รันบน host ตอน `docker compose up`) เป็นคนตรวจ IP จริงแล้วส่งเข้ามาผ่าน env var
// HOST_LAN_IP แทน ถ้าไม่มีค่านี้ (เช่นรันตรงบน host ผ่าน XAMPP) จะ fallback ไปใช้วิธีตรวจเองแบบเดิม
$ip = getenv('HOST_LAN_IP') ?: null;

if (!$ip) {
    // fallback: "เชื่อม" ปลอมไปยัง 8.8.8.8 ผ่าน UDP (ไม่มีการส่งข้อมูลจริงออกไป) แล้วดู local address
    // ที่ระบบปฏิบัติการเลือกใช้สำหรับเส้นทางออกอินเทอร์เน็ต ใช้ได้เฉพาะตอนรันตรงบน host เท่านั้น
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
