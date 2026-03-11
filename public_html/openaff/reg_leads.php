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

$URL = 'https://vip.sazoqie.com/api';

$apiData = [
    'first_name'  => $parsedData['first_name'] ?? '',
    'last_name'   => $parsedData['last_name'] ?? '',
    'email'       => $parsedData['email'] ?? '',
    'password'    => $parsedData['password'] ?? '',
    'phonecc'     => $parsedData['phonecc'] ?? '',
    'phone'       => $parsedData['phone'] ?? '',
    'country'     => $parsedData['country'] ?? '',
    'user_ip'     => $parsedData['user_ip'] ?? $_SERVER['REMOTE_ADDR'],
    'aff_sub'     => $parsedData['aff_sub'] ?? '',
    'aff_sub2'    => $parsedData['aff_sub2'] ?? '',
    'aff_sub3'    => $parsedData['aff_sub3'] ?? $_SERVER['SERVER_NAME'],
    'aff_sub4'    => $parsedData['aff_sub4'] ?? '',
    'aff_sub5'    => $parsedData['aff_sub5'] ?? '',
    'aff_id'      => OPENAFF_AFF_ID,
    'offer_id'    => OPENAFF_OFFER_ID,
    'referer'     => $parsedData['referer'] ?? '',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL . '?' . http_build_query($apiData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$headers = [
    'User-Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; rv:100.0) Gecko/20100101 Firefox/100.0'),
    'Accept-Language: ' . ($parsedData['lang'] ?? 'en'),
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
