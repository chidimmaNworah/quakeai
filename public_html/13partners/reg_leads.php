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

$URL = PARTNERS13_API_URL . '/api/leads';

// Build full_name from first + last
$firstName = trim($parsedData['first_name'] ?? '');
$lastName  = trim($parsedData['last_name'] ?? '');
$fullName  = trim($firstName . ' ' . $lastName);

// Build form data for 13Partners API (application/x-www-form-urlencoded)
$apiData = [
    'full_name'    => $fullName,
    'country'      => $parsedData['country'] ?? '',
    'email'        => $parsedData['email'] ?? '',
    'landing'      => PARTNERS13_LANDING,
    'landing_name' => PARTNERS13_LANDING_NAME,
    'phone'        => ($parsedData['phonecc'] ?? '') . ($parsedData['phone'] ?? ''),
    'user_id'      => PARTNERS13_USER_ID,
    'ip'           => $parsedData['user_ip'] ?? $_SERVER['REMOTE_ADDR'],
    'source'       => PARTNERS13_SOURCE,
];

// Optional fields
if (!empty($parsedData['keitaro_id'])) {
    $apiData['keitaro_id'] = $parsedData['keitaro_id'];
}
if (!empty($parsedData['description'])) {
    $apiData['description'] = $parsedData['description'];
}

// Pass UTM params if provided
foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $utm) {
    if (!empty($parsedData[$utm])) {
        $apiData[$utm] = $parsedData[$utm];
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));

$headers = [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
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

// Try to decode response; if 13partners returns JSON, pass it through
$decoded = json_decode($response, true);
if ($decoded !== null) {
    echo $response;
} else {
    // Wrap non-JSON response
    echo json_encode([
        'success' => ($code >= 200 && $code < 300),
        'message' => $response,
        'status_code' => $code,
    ]);
}
