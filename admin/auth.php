<?php
function require_auth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (($_SESSION['logged_in'] ?? false) !== true) {
        header('Location: index.php');
        exit;
    }
}
