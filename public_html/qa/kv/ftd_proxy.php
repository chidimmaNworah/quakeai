<?php
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Set the default time zone to UTC
date_default_timezone_set('UTC');

// Retrieve 'created_from', 'created_to', and 'goal_type_uuid' from the URL query parameters
$created_from = $_GET['created_from'] ?? null;  // e.g., "2023-06-22T00:00:00Z"
$created_to = $_GET['created_to'] ?? null;      // e.g., "2023-06-23T00:00:00Z"
$goal_type_uuid = $_GET['goal_type_uuid'] ?? '2535cb68-5d23-403c-ab20-2c06d0fb7f30'; // Default goal_type_uuid if not provided

// Check if 'created_from' and 'created_to' are provided
if (!$created_from || !$created_to) {
    echo json_encode(['error' => 'Missing created_from or created_to']);
    http_response_code(400);
    exit();
}

// Validate the date format (Expected format: "YYYY-MM-DDThh:mm:ssZ")
if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $created_from) || 
    !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $created_to)) {
    echo json_encode(['error' => 'Invalid date format. Expected format: YYYY-MM-DDThh:mm:ssZ']);
    http_response_code(400);
    exit();
}

// API Token
$token = KV_FTD_TOKEN;

// Base URL for the API
$apiUrl = "https://kamehamedia-ld.irev.com/api/affiliates/v2/leads";
$queryParams = http_build_query([
    'created_from' => $created_from,
    'created_to' => $created_to,
    'goal_type_uuid' => $goal_type_uuid
]);

// Initialize cURL session
$ch = curl_init("$apiUrl?$queryParams");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the API request
$response = curl_exec($ch);

// Handle the API response
if ($response === false) {
    error_log("Error fetching data from API: " . curl_error($ch));
    echo json_encode(['error' => 'Failed to fetch data']);
    http_response_code(500);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        error_log("API returned status code $httpCode");
        echo json_encode(['error' => "API error, status code: $httpCode"]);
        http_response_code($httpCode);
    } else {
        // Output the API response as-is
        echo $response;
    }
}

// Close the cURL session
curl_close($ch);
?>
