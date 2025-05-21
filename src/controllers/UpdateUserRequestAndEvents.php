<?php
require_once CONTROLLERS_PATH . '/UserRequest.php';
require_once CONTROLLERS_PATH . '/GetAllEvents.php';
require_once CONTROLLERS_PATH . '/UserEventDetail.php';
function UpdateUserRequestAndEvents(PDO $dbh) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user_id = $_SESSION['user']['user_id'];
    $_SESSION['requests'] = UserRequest($dbh, $user_id);
    $_SESSION['events'] = GetAllEvents($dbh);
    $_SESSION['user_event_detail'] = UserEventDetail($dbh, $user_id);
}
