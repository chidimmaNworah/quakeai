<?php
require_once __DIR__ . '/auth_check.php';

if (!is_authenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

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

$URL = ALTSTREAM_TB_URL . '/api/pull/customers';

// Build date range from filter
$date = $parsedData['date'] ?? date('Y-m-d');
$from = $date . ' 00:00:00';
$to   = $date . ' 23:59:59';

// type: 2 = Only Leads, 3 = Leads + Deposits, 4 = Only Deposits
$type = '3'; // default: leads + deposits
if (!empty($parsedData['type'])) {
    $type = $parsedData['type'];
}

$page = $parsedData['page'] ?? '0';

$body = [
    'from' => $from,
    'to'   => $to,
    'type' => $type,
    'page' => $page,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$headers = [
    'x-trackbox-username: ' . ALTSTREAM_TB_USERNAME,
    'x-trackbox-password: ' . ALTSTREAM_TB_PASSWORD,
    'x-api-key: ' . ALTSTREAM_TB_PULL_API_KEY,
    'Content-Type: application/json',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header_remove();
header('Content-Type: application/json; charset=UTF-8');
http_response_code($code);
echo $response;
