<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$licenseKey = $data['key'] ?? '';
$serial = $data['serial'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];

$licenses = json_decode(file_get_contents('../licenses.json'), true);

foreach ($licenses as $license) {
    if (
        $license['key'] === $licenseKey &&
        $license['serial'] === $serial &&
        $license['status'] === 'active'
    ) {
        if (!empty($license['ip']) && $license['ip'] !== $ip) {
            echo json_encode(["success" => false, "message" => "IP uyuşmazlığı"]);
            exit;
        }

        if (strtotime($license['expires_at']) < time()) {
            echo json_encode(["success" => false, "message" => "Lisans süresi dolmuş."]);
            exit;
        }

        echo json_encode(["success" => true, "message" => "Lisans geçerli."]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "Lisans bulunamadı."]);
