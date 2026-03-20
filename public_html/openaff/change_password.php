<?php
require_once __DIR__ . '/auth_check.php';
require_auth();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$currentPassword = $input['current_password'] ?? '';
$newUsername = trim($input['new_username'] ?? '');
$newPassword = $input['new_password'] ?? '';

// Verify current password
if (!verify_admin_password($currentPassword)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
    exit;
}

if (strlen($newUsername) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Username must be at least 3 characters.']);
    exit;
}

if (strlen($newPassword) < 4) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 4 characters.']);
    exit;
}

if (save_credentials($newUsername, $newPassword)) {
    // Update session with new username
    $_SESSION['admin_user'] = $newUsername;
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save credentials.']);
}
