<?php
require_once CONTROLLERS_PATH . '/UserData.php';
function UpdateUserData(PDO $dbh) {
    $_SESSION['user'] = array_filter(UserData($dbh, $_SESSION['user']['user_email']), fn($key) => $key !== "user_password", ARRAY_FILTER_USE_KEY);
}
