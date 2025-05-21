<?php
function Is_admin () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
        header(HEADER_HP_PATH);
        exit;
    }
}
?>
