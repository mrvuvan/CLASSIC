<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Nhận dữ liệu từ JSON hoặc Form POST/GET
$rawData = file_get_contents('php://input');
$account = '';
$password = '';

if (!empty($rawData)) {
    $decoded = json_decode($rawData, true);
    if ($decoded) {
        $account = isset($decoded['account']) ? $decoded['account'] : '';
        $password = isset($decoded['password']) ? $decoded['password'] : '';
    }
}

if (empty($account) && isset($_POST['account'])) {
    $account = $_POST['account'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';
}

if (empty($account) && isset($_GET['account'])) {
    $account = $_GET['account'];
    $password = isset($_GET['password']) ? $_GET['password'] : '';
}

if (empty($account) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'Vui long nhap day du tai khoan va mat khau!'));
    exit();
}

$payload = json_encode(array(
    'account' => trim($account),
    'password' => trim($password)
));

// 2. Gửi dữ liệu từ Hosting sang Windows VPS
$ch = curl_init('http://160.191.244.129:3005/api/register-direct');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_TIMEOUT, 6);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(array('success' => false, 'message' => 'Loi ket noi tu Hosting den VPS: ' . $error));
} else {
    echo $response;
}
?>