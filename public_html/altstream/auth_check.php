<?php
session_start();
require_once __DIR__ . '/../config.php';

function is_authenticated() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_auth() {
    if (!is_authenticated()) {
        header('Location: login.php');
        exit;
    }
}
