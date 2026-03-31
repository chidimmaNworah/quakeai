<?php
session_start();
require_once __DIR__ . '/../config.php';

define('CREDS_FILE_13PARTNERS', __DIR__ . '/credentials.json');

function load_credentials() {
    if (file_exists(CREDS_FILE_13PARTNERS)) {
        $json = json_decode(file_get_contents(CREDS_FILE_13PARTNERS), true);
        if ($json && isset($json['username'], $json['password_hash'])) {
            return $json;
        }
    }
    return null;
}

function get_admin_username() {
    $creds = load_credentials();
    return $creds ? $creds['username'] : ADMIN_USERNAME;
}

function verify_admin_password($password) {
    $creds = load_credentials();
    if ($creds) {
        return password_verify($password, $creds['password_hash']);
    }
    return password_verify($password, ADMIN_PASSWORD_HASH);
}

function save_credentials($username, $password) {
    $data = [
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ];
    return file_put_contents(CREDS_FILE_13PARTNERS, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

function is_authenticated() {
    return isset($_SESSION['partners13_logged_in']) && $_SESSION['partners13_logged_in'] === true;
}

function require_auth() {
    if (!is_authenticated()) {
        header('Location: login.php');
        exit;
    }
}
