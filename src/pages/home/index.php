<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Profile.php";
require_once COMPONENTS_PATH . "/LevelDisplay.php";
require_once COMPONENTS_PATH . "/EventDisplay.php";
require_once COMPONENTS_PATH . "/RequestSection.php";
require_once CONTROLLERS_PATH . '/UpdateUserData.php';

// Check if the session exists
UpdateUserData($dbh);
$user_info = $_SESSION['user'] ?? null;
$requests = $_SESSION['requests'] ?? null;
$events = $_SESSION['user_event_detail'] ?? null;

$exp = $user_info['user_exp_point'] ?? 0;
$level = $user_info['user_level'] ?? 0;
?>

<div class="flex flex-col gap-8">
    <?= Profile(user_info: $user_info, dbh: $dbh)?>
    <?= LevelDisplay($exp, $level)?>
    <?= RequestSection($dbh, $requests, $user_info['user_id']); ?>
    <?= EventsDisplay($events); ?>
</div>
