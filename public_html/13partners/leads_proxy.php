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

// Build request body per API docs:
// curl --request GET 'url' --header 'Content-Type: application/json'
//   --data-raw '{"page":1,"per_page":1000,"date_start":"...","date_end":"..."}'
$bodyParams = [
    'page'     => (int)($parsedData['page'] ?? 1),
    'per_page' => (int)($parsedData['per_page'] ?? 1000),
];

if (!empty($parsedData['date_start'])) {
    $bodyParams['date_start'] = $parsedData['date_start'];
}
if (!empty($parsedData['date_end'])) {
    $bodyParams['date_end'] = $parsedData['date_end'];
}

// If a single 'date' is passed, use it for both start and end
if (!empty($parsedData['date']) && empty($parsedData['date_start'])) {
    $bodyParams['date_start'] = $parsedData['date'];
    $bodyParams['date_end'] = $parsedData['date'];
}

$bodyJson = json_encode($bodyParams);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $bearerToken,
    'Content-Type: application/json',
    'Content-Length: ' . strlen($bodyJson),
]);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $error, 'debug' => 'cURL failed']);
    exit;
}

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header_remove();
header('Content-Type: application/json; charset=UTF-8');

// If the upstream returned an error, pass it through with debug context
if ($code < 200 || $code >= 300) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'http_code' => $code,
        'upstream_response' => json_decode($response, true) ?? $response,
        'request_url' => $URL,
        'request_body' => $bodyParams,
    ]);
    exit;
}

echo $response;
