<?php
require_once __DIR__ . '/../config.php';
// Enable CORS for your frontend domain
header('Access-Control-Allow-Origin: https://quakeai.live');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');

// Read the input from the JavaScript
$inputData = file_get_contents('php://input');
$parsedData = json_decode($inputData, true);

// Define the API URL
$apiUrl = 'https://communication.algolead.org/api.php';

// Inject sensitive credentials server-side (never trust client-sent values)
$parsedData['Auth'] = ALGO_AUTH;
$parsedData['PartnerID'] = ALGO_PARTNER_ID;
$parsedData['TrackingID'] = ALGO_TRACKING_ID;
$parsedData['SubCampaignID'] = ALGO_SUBCAMPAIGN_ID;

// Remove any fields that should not be forwarded
unset($parsedData['apiUrl']);
unset($parsedData['Token']); // Not needed for registration

$apiRequestUrl = $apiUrl . '?' . http_build_query($parsedData);

// Initialize cURL session for the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiRequestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and get the response
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Send the API response back to the JavaScript
http_response_code($httpCode);
echo $response;
?>
