<?php
require_once __DIR__ . '/../../config.php';
// Allow CORS for frontend integration
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// Load incoming JSON
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

// Handle JSON parsing errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 500, 'response' => 'Invalid JSON format']);
    exit;
}

// Validate required root-level fields
$requiredRoot = ['ip', 'profile'];
foreach ($requiredRoot as $field) {
    if (empty($json_obj[$field])) {
        echo json_encode(['status' => 400, 'response' => "Missing root field: $field"]);
        exit;
    }
}

// Validate required profile fields
$profile = $json_obj['profile'];
$requiredProfile = ['firstName', 'lastName', 'email', 'password', 'phone'];
foreach ($requiredProfile as $field) {
    if (empty($profile[$field])) {
        echo json_encode(['status' => 400, 'response' => "Missing profile field: $field"]);
        exit;
    }
}

// Sanitize and extract fields
$ip = filter_var($json_obj['ip'], FILTER_VALIDATE_IP);
$email = filter_var($profile['email'], FILTER_SANITIZE_EMAIL);
$phone = preg_replace('/\D/', '', $profile['phone']);
$firstName = filter_var($profile['firstName'], FILTER_SANITIZE_STRING);
$lastName = filter_var($profile['lastName'], FILTER_SANITIZE_STRING);
$password = filter_var($profile['password'], FILTER_SANITIZE_STRING);

$geo = isset($json_obj['geo']) ? strtoupper($json_obj['geo']) : 'US';
$subId = isset($json_obj['subId']) ? $json_obj['subId'] : null;
$landingURL = $json_obj['landingURL'] ?? 'https://quantum-ai.com/';
$funnel = $json_obj['funnel'] ?? 'quantumai';
$lang = $json_obj['lang'] ?? 'en';

// Constants
$apiKey = KV_API_KEY;
$affc = KV_AFFC;
$bxc = KV_BXC;
$vtc = KV_VTC;

// Construct payload for external API
$payload = [
    "affc" => $affc,
    "bxc" => $bxc,
    "vtc" => $vtc,
    "profile" => [
        "firstName" => $firstName,
        "lastName" => $lastName,
        "email" => $email,
        "password" => $password,
        "phone" => $phone
    ],
    "ip" => $ip,
    "funnel" => $funnel,
    "landingURL" => $landingURL,
    "geo" => $geo,
    "lang" => $lang,
    "landingLang" => $lang,
    "subId" => $subId
];

// Send request to external lead API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://yourbestnetwork.com/api/external/integration/lead',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Respond with interpretation
if ($response === false) {
    echo json_encode(['status' => 500, 'response' => 'Curl error']);
} else {
    $parsed = json_decode($response, true);
    switch ($httpCode) {
        case 201:
            echo json_encode(['status' => 201, 'auto_login_url' => $parsed['auto_login_url'] ?? null]);
            break;
        case 400:
            echo json_encode(['status' => 400, 'response' => $parsed['validation_errors'] ?? 'Validation failed']);
            break;
        case 401:
            echo json_encode(['status' => 401, 'response' => 'Unauthorized - check API key']);
            break;
        case 500:
            echo json_encode(['status' => 500, 'response' => 'Lead declined by provider']);
            break;
        default:
            echo json_encode(['status' => $httpCode, 'response' => $parsed]);
            break;
    }
}
?>
