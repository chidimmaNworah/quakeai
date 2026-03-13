<?php
require_once __DIR__ . '/../config.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$inputData = file_get_contents('php://input');
$parsedData = json_decode($inputData, true);

if (!$parsedData) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => ['request' => ['Invalid JSON input']]]);
    exit;
}

$URL = ALTSTREAM_TB_URL . '/api/signup/procform';

$apiData = [
    'ai'        => ALTSTREAM_AI,
    'ci'        => ALTSTREAM_CI,
    'gi'        => ALTSTREAM_GI,
    'userip'    => $parsedData['user_ip'] ?? $_SERVER['REMOTE_ADDR'],
    'firstname' => $parsedData['first_name'] ?? '',
    'lastname'  => $parsedData['last_name'] ?? '',
    'email'     => $parsedData['email'] ?? '',
    'password'  => $parsedData['password'] ?? '',
    'phone'     => $parsedData['phone'] ?? '',
    'so'        => $parsedData['so'] ?? '',
    'sub'       => $parsedData['sub'] ?? '',
    'MPC_1'     => $parsedData['MPC_1'] ?? '',
    'MPC_2'     => $parsedData['MPC_2'] ?? '',
    'MPC_3'     => $parsedData['MPC_3'] ?? '',
    'MPC_4'     => $parsedData['MPC_4'] ?? '',
    'MPC_5'     => $parsedData['MPC_5'] ?? '',
    'lg'        => $parsedData['lg'] ?? 'EN',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));

$headers = [
    'x-trackbox-username: ' . ALTSTREAM_TB_USERNAME,
    'x-trackbox-password: ' . ALTSTREAM_TB_PASSWORD,
    'x-api-key: ' . ALTSTREAM_TB_PUSH_API_KEY,
    'Content-Type: application/json',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['curl' => [$error]]]);
    exit;
}

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header_remove();
header('Content-Type: application/json; charset=UTF-8');
http_response_code($code);
echo $response;
