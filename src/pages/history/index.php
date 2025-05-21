<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Nothing.php";
require_once COMPONENTS_PATH . "/ShowDetailButton.php";
require_once CONTROLLERS_PATH . "/UserRequestHistory.php";
require_once CONTROLLERS_PATH . "/CircleRequestHistory.php";
require_once COMPONENTS_PATH . "/ProfileImg.php";

$id = $_GET['id'];
$for = $_GET['for'];

$img_path = 'assets/img/profile/';

if ($for === 'circle') {
    $results = CircleRequestHistory($dbh, $id);
} elseif ($for === 'user') {
    $results = UserRequestHistory($dbh, $id);
}

// Display results
$worked_on_requests = [];
$requested_requests = [];

foreach ($results as $row) {
    if ($row['section_type'] == 'worked_on') {
        $worked_on_requests[] = $row;
    } else {
        $requested_requests[] = $row;
    }
}
?>

<h1 class="m-4 text-center text-themeYellow">実績・履歴一覧</h1>

<?php if (empty($worked_on_requests) && empty($requested_requests)) { ?>
    <?= Nothing('履歴がありません') ?>
<?php } ?>

<? if (!empty($worked_on_requests)) { ?>
    <section class="flex m-6 justify-center items-center flex-col gap-4">
        <h2 class="text-xl font-bold">解決したリクエスト</h2>
        <div class="flex flex-col gap-12">
            <? foreach ($worked_on_requests as $request) { ?>
                <div class="flex items-center gap-16">
                    <?= ProfileImg($request['circle_image'], "?page=profile/circle&id=" . $request['circle_id']) ?>
                    <div class="text-center p-4">
                        <p>解決日:</p>
                        <?= date('Y-m-d', strtotime($request['request_updated_at'])) ?>
                </div>
                <div class="text-center p-4">
                    <p>依頼:</p>
                    <p><?= $request['request_title'] ?></p>
                </div>
                <div class="text-center p-4">
                    <p>依頼者:</p>
                    <p><?= $request['circle_name'] ?></p>
                </div>
                <div class="text-center p-4">
                    <p>評価:</p> 
                    <p><?= round($request['avg_review_score'], 2) ?> / 5</p>
                </div>
                    <?= ShowDetailButton("review/view&section_type=worked_on", $request['request_id']) ?>
                </div>
            <? } ?>
        </div>
    </section>
<? } ?>

<? if (!empty($requested_requests)) { ?>
    <section class="flex m-6 justify-center items-center flex-col gap-4">
        <h2 class="text-xl font-bold">依頼したリクエスト</h2>
        <div class="flex flex-col gap-12">
            <? foreach ($requested_requests as $request) { ?>
                <div class="flex items-center gap-12 text-center">
                    <?= ProfileImg($request['circle_image'], "?page=profile/circle&id=" . $request['circle_id']) ?>
                    <div class="text-center p-4">
                        <p>依頼日:</p>
                        <?= date('Y-m-d', strtotime($request['request_updated_at'])) ?>
                    </div>
                    <div class="text-center p-4">
                        <p>依頼:</p>
                        <p><?= $request['request_title'] ?></p>
                    </div>
                    <div class="text-center p-4">
                        <p>依頼者:</p>
                        <p><?= $request['circle_name'] ?></p>
                    </div>
                    <div class="text-center text-wrap">
                        <p>評価:</p>
                        <p class="w-16"><?= ceil((float)$request['avg_review_score'] * 10) / 10 ?> / 5</p>
                    </div>
                    <div class="flex items-center gap-16">
                        <?= ShowDetailButton("review/view&section_type=requester", $request['request_id']) ?>
                    </div>
                </div>
            <? } ?>
        </div>
    </section>
<? } ?>
