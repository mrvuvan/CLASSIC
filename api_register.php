<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Nhận dữ liệu gửi từ trình duyệt
$rawData = file_get_contents('php://input');

// Bắn dữ liệu sang Windows VPS (Server-to-Server hoàn toàn không bị chặn Mixed Content)
$ch = curl_init('http://160.191.245.2:3005/api/register-direct');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Lỗi kết nối tới VPS: ' . $error));
} else {
    http_response_code($httpCode ? $httpCode : 200);
    echo $response;
}
?>