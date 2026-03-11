<?php
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Set the default timezone to UTC
date_default_timezone_set('UTC');

// Retrieve 'created_from' and 'created_to' from the query parameters
$created_from = $_GET['created_from'] ?? null;  // Example: "2024-11-01T00:00:00.000Z"
$created_to = $_GET['created_to'] ?? null;      // Example: "2024-11-15T23:59:59.999Z"

// Check if required parameters are provided
if (!$created_from || !$created_to) {
    echo json_encode(['error' => 'Missing created_from or created_to']);
    http_response_code(400);
    exit();
}

// Validate the date format
$datetime_regex = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/';
if (!preg_match($datetime_regex, $created_from) || !preg_match($datetime_regex, $created_to)) {
    echo json_encode(['error' => 'Invalid date format. Expected format: YYYY-MM-DDThh:mm:ss.sssZ']);
    http_response_code(400);
    exit();
}

// API Configuration
$apiKey = KV_API_KEY;
$baseUrl = "https://yourbestnetwork.com/api/external/integration/lead"; // ✅ Fixed typo in domain

$params = http_build_query([
    'skip' => 0,
    'take' => 250,
    'from' => $created_from,
    'to' => $created_to
]);

// Full API URL
$apiUrl = "$baseUrl?$params";

// Initialize cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-key: $apiKey"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Handle errors
if ($response === false) {
    error_log("cURL error: " . curl_error($ch));
    echo json_encode(['error' => 'Failed to fetch data from API']);
    http_response_code(500);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        error_log("API returned status $httpCode: $response");
        echo json_encode(['error' => "API error, status code: $httpCode"]);
        http_response_code($httpCode);
    } else {
        echo $response;
    }
}

curl_close($ch);
?>
