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

$bearerToken = PARTNERS13_BEARER_TOKEN;
$URL = PARTNERS13_API_URL . '/api/web-master/leads';

// Build query params
$page    = $parsedData['page'] ?? 1;
$perPage = $parsedData['per_page'] ?? 1000;

$queryParams = [
    'page'     => $page,
    'per_page' => $perPage,
];

// Date range
if (!empty($parsedData['date_start'])) {
    $queryParams['date_start'] = $parsedData['date_start'];
}
if (!empty($parsedData['date_end'])) {
    $queryParams['date_end'] = $parsedData['date_end'];
}

// If a single 'date' is passed, use it for both start and end
if (!empty($parsedData['date']) && empty($parsedData['date_start'])) {
    $queryParams['date_start'] = $parsedData['date'];
    $queryParams['date_end'] = $parsedData['date'];
}

$requestUrl = $URL . '?' . http_build_query($queryParams);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// 13Partners uses Bearer token + JSON body for GET (per docs)
// But params go as query string; body is sent as JSON for compatibility
$bodyData = json_encode($queryParams);

curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyData);

$headers = [
    'Authorization: Bearer ' . $bearerToken,
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
