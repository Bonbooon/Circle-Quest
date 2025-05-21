<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/LevelDisplay.php";
require_once COMPONENTS_PATH . "/Profile.php";
require_once CONTROLLERS_PATH . '/UserData.php';

$user_id = isset($_GET['id']) ? $_GET['id'] : '';
$invited_circle_member_id = isset($_GET['invited_circle_member_id']) ? $_GET['invited_circle_member_id'] : '';
$user_info = UserData($dbh,'', $user_id) ?? null;
$exp = $user_info['user_exp_point'] ?? 0;
$level = $user_info['user_level'] ?? 0;
?>

<div class="flex flex-col gap-8">
    <?= Profile(dbh: $dbh, user_info: $user_info, viewing: true, invited_circle_member_id: $invited_circle_member_id)?>
    <?= LevelDisplay($exp, $level)?>
</div>
