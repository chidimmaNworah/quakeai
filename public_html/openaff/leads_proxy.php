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

$bearerToken = OPENAFF_BEARER_TOKEN;

$URL = 'https://tracker.openaff.com/api/get_client_conversions';

$params = [];

if (!empty($parsedData['date'])) {
    $params['date'] = $parsedData['date'];
}

if (!empty($parsedData['all_statuses'])) {
    $params['all_statuses'] = 1;
}

if (!empty($parsedData['type'])) {
    $params['type'] = $parsedData['type'];
}

$requestUrl = $URL;
if (!empty($params)) {
    $requestUrl .= '?' . http_build_query($params);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$headers = [
    'Authorization: Bearer ' . $bearerToken,
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
