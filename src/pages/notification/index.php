<?php
require_once DBCONNECT;
require_once CONTROLLERS_PATH . '/UserNotifications.php';
require_once CONTROLLERS_PATH . '/ReadNotifications.php';
require_once COMPONENTS_PATH . '/Nothing.php';
require_once COMPONENTS_PATH . '/showDetailButton.php';
// i have to make a notifier
$user_id = $_SESSION['user']['user_id'];
$notifications = UserNotifications($dbh, $user_id);
ReadNotifications($dbh,$user_id);
?>

<h1 class="">
    通知一覧
</h1>
<div class="my-12">
    <? if (!empty($notifications)) { ?>
        <? foreach ($notifications as $notif) { ?>
            <div class=" flex h-20 items-center content-center <?= !$notif['is_read'] ? 'is-unread' : '' ?>">
                <div class="h-20 flex-1 w-24 border  flex items-center justify-center">
                    <?= $notif['created_at'] ?>
                </div>
                <div class=" h-20 gap-6 w-144 border flex flex-3 items-center justify-center">
                    <p class="truncate"><?= $notif['notification'] ?></p>
                    <? if (!is_null($notif['external_link'])) {?>
                        <a class="text-xs flex flex-col items-center justify-center" href="<?= $notif['external_link']; ?>" target="_blank" rel="noopener noreferrer">
                            <p class="text-themeYellow">連絡先</p>
                            <img class="w-9 h-9" src="assets/img/line.svg" alt="line">
                        </a>
                    <? }?>
                </div>
                <div class="w-full flex flex-1 items-center justify-center gap-3">
                    <?= !is_null($notif['redirects_to']) ? showDetailButton($notif['redirects_to'], $notif['request_id'] ?? $notif['event_id']) : ""?>
                </div>
            </div>
        <? } ?>
    <? } else { ?>
        <?= Nothing('通知はまだありません') ?>
    <? } ?>
</div>
