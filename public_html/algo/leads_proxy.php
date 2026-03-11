<?php
require_once __DIR__ . '/../config.php';
// Enable CORS for your frontend domain
header('Access-Control-Allow-Origin: https://quakeai.live');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');

// Read the input from the JavaScript
$inputData = file_get_contents('php://input');
$parsedData = json_decode($inputData, true);

// Define the API URL and required parameters
$apiUrl = 'https://communication.algolead.org/api.php';
$requiredParams = [
    'Service' => 'AccountsData',
    'PartnerID' => ALGO_PARTNER_ID,
    'SubCampaignID' => ALGO_SUBCAMPAIGN_ID,
    'Auth' => ALGO_AUTH,
    'TrackingID' => ALGO_TRACKING_ID,
    'Token' => ALGO_TOKEN,
    // Add or overwrite with parameters from the request
    'CreateTimeFrom' => $parsedData['CreateTimeFrom'] ?? '', // Optional: Format YYYY-MM-DD HH:mm:ss
    'CreateTimeTo' => $parsedData['CreateTimeTo'] ?? '', // Optional: Format YYYY-MM-DD HH:mm:ss
    'AccountIDs' => $parsedData['AccountIDs'] ?? '', // Optional: Comma-separated account IDs
];

// Construct the API request URL with query parameters
$apiRequestUrl = $apiUrl . '?' . http_build_query($requiredParams);

// Initialize cURL session for the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiRequestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and get the response
$response = curl_exec($ch);
if ($response === false) {
    // Handle cURL error
    $error = curl_error($ch);
    curl_close($ch);
    die(json_encode(['status' => 'Failed', 'error' => $error]));
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Send the API response back to the JavaScript
http_response_code($httpCode);
echo $response;
?>
