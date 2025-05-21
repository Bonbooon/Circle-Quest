<?php
require_once COMPONENTS_PATH . "/RequestsDisplay.php";
require_once COMPONENTS_PATH . "/CustomH3.php";
require_once CONTROLLERS_PATH . "/HasReviewed.php";

function RequestSection(PDO $dbh, array $requests, int $user_id)
{
    $requests_asked = $requests['requestsAsked'] ?? [];
    $requests_working_on = $requests['requestsWorkingOn'] ?? [];

    $requests_asked_with_giver = [];
    $requests_asked_with_review = [];
    $requests_asked_without_giver = [];

    foreach ($requests_asked as $req) {
        if ($req['is_completed']) continue;

        if ($req['request_status'] === 'selected') {
            $hasReviewed = HasReviewed($dbh, $user_id, null, $req['id']);
            if ($hasReviewed) {
                $requests_asked_with_review[] = $req;
            } else {
                $requests_asked_with_giver[] = $req;
            }
        } elseif ($req['request_status'] === 'pending' || $req['request_status'] === 'no_submission') {
            $requests_asked_without_giver[] = $req;
        }
    }

    $requests_working_on_active = [];
    $requests_on_hold = [];

    foreach ($requests_working_on as $req) {
        if ($req['is_completed']) continue;

        if ($req['request_status'] === 'selected') {
            $requests_working_on_active[] = $req;
        } elseif ($req['request_status'] === 'pending') {
            $requests_on_hold[] = $req;
        }
    }

    $has_requests = !empty($requests_asked_with_giver)
        || !empty($requests_asked_without_giver)
        || !empty($requests_working_on_active)
        || !empty($requests_on_hold);

    ob_start();
    ?>
    <div class="flex flex-col gap-4">
        <?php if ($has_requests) { ?>
            <?= RequestsDisplay($requests_working_on_active, "担当中の依頼", 'submit') ?>
            <?= RequestsDisplay($requests_on_hold, "選定待ち中", 'submit') ?>
            <?= RequestsDisplay($requests_asked_without_giver, "選定中/応募待ち中の依頼", 'select', true) ?>
            <?= RequestsDisplay($requests_asked_with_giver, "納品待ち中の依頼", 'review') ?>
            <?= RequestsDisplay($requests_asked_with_review, "評価待ち中の依頼") ?>
        <?php } else { ?>
            <h2>新しい依頼をお願い・応募してみよう！</h2>
        <?php } ?>
        <div class="relative p-4 w-full h-48 <?= isset($user_info['fav']) ? "" : "hidden" ?>">
            <?= CustomH3(text: "お気に入りサークル", extraCSS: "absolute top-0 left-0") ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
