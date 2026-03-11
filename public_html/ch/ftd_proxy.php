<?php
require_once __DIR__ . '/../config.php';
// ftd_proxy.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$apiKey = CH_API_KEY;
$apiEndpoint = 'https://trackingwebdo.com/api/v2/conversions/';

// Build URL with query string
$url = $apiEndpoint . '?' . $_SERVER['QUERY_STRING'];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Api-Key: $apiKey"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo json_encode(['status' => 'error', 'message' => $error]);
} else {
    echo $response;
}
?>