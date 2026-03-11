<?php
require_once __DIR__ . '/../config.php';
// Enable CORS for your frontend domain
header('Access-Control-Allow-Origin: https://quakeai.live');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');

// Read the input from the JavaScript
$inputData = file_get_contents('php://input');
$parsedData = json_decode($inputData, true);

// Define the AlgoLead API URL
$apiUrl = 'https://communication.algolead.org/api.php';

// Set the service parameter for FTDs
$parsedData['Service'] = 'DepositsList';

// Inject sensitive credentials server-side (never trust client-sent values)
$parsedData['PartnerID'] = ALGO_PARTNER_ID;
$parsedData['Auth'] = ALGO_AUTH;
$parsedData['TrackingID'] = ALGO_TRACKING_ID;
$parsedData['Token'] = ALGO_TOKEN;
$parsedData['SubCampaignID'] = ALGO_SUBCAMPAIGN_ID;
// Include CreateTimeFrom, CreateTimeTo, and AccountIDs if they are part of the request

// Construct the API request URL with query parameters
$apiRequestUrl = $apiUrl . '?' . http_build_query($parsedData);

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
