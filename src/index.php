<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_email'])) {
    header('Location: /before/index.php');
}

$is_admin = $_SESSION['user']['is_admin'] ?? false;

require_once __DIR__ . '/config.php';

$page = $_GET['page'] ?? 'home';

$allowed_pages = ['home','create','submit','search','select','pay','review', 'review/view','notification', 'ranking', 'event', 'event/join', 'event/select', 'join','profile/circle', 'profile/user', 'history'];
if ($is_admin) {
    $allowed_pages[] = 'admin';
}

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Load page
ob_start();
include "pages/$page/index.php";
$content = ob_get_clean();

include 'layout/index.php';
?>
