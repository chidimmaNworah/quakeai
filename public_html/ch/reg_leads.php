<?php
require_once __DIR__ . '/../config.php';
// reg_leads.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

$apiKey = CH_API_KEY;
$apiEndpoint = 'https://trackingwebdo.com/api/v2/leads/';

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Api-Key: $apiKey",
    "Content-Type: application/x-www-form-urlencoded"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$postData = [
    'email' => $_POST['email'],
    'firstName' => $_POST['firstName'],
    'lastName' => $_POST['lastName'],
    'password' => $_POST['password'],
    'ip' => $_POST['ip'],
    'phone' => $_POST['phone'],
    'offerName' => $_POST['offerName'],
    'offerWebsite' => $_POST['offerWebsite'],
    'custom1' => $_POST['custom1'] ?? '',
    'custom2' => $_POST['custom2'] ?? '',
    'custom3' => $_POST['custom3'] ?? '',
];

curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo json_encode(['status' => 'error', 'message' => $error]);
} else {
    echo json_encode([
        'status' => $http_code,
        'response' => json_decode($response)
    ]);
}
?>