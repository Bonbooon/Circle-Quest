<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Button.php";
require_once COMPONENTS_PATH . "/DisplaySubmission.php";
require_once CONTROLLERS_PATH . '/Review.php';
require_once CONTROLLERS_PATH . '/LevelUp.php';
require_once CONTROLLERS_PATH . '/NotifyUser.php';
require_once CONTROLLERS_PATH . '/HasReviewed.php';
require_once CONTROLLERS_PATH . '/ToWhichSide.php';
require_once CONTROLLERS_PATH . '/FindPartaker.php';
require_once CONTROLLERS_PATH . '/CompleteRequest.php';
require_once CONTROLLERS_PATH . '/RecalculateRanks.php';
require_once CONTROLLERS_PATH . '/SelectedSubmission.php';
require_once CONTROLLERS_PATH . '/RetrieveReviewCriteria.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once JS_PATH . '/RenderPaymentModalScript.php';


$request_id = (int)$_GET['id'];
$user_id = $_SESSION['user']['user_id'];
$selectedSubmission = SelectedSubmission($dbh, $request_id, $user_id);
$submission_id = $selectedSubmission['id'];
$submission = $selectedSubmission['submission'];
$request_data = ($stmt = $dbh->prepare("SELECT title, is_completed FROM requests WHERE id = ?")) && $stmt->execute([$request_id]) ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
if ($request_data['is_completed'] || HasReviewed($dbh, $submission_id, $user_id)) {
    header(HEADER_HP_PATH);
    exit;
}
$request_title = $request_data['title'];
$message = '';
$FromWhomTo = ToWhichSide($dbh, $request_id, $user_id);
$toWhom = $FromWhomTo['toWhom'];
$from = $FromWhomTo['from'];
$isRequester = $from == 'requester';
$criteriaList = RetrieveReviewCriteria($dbh, $from);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $dbh->beginTransaction();

        $totalPoints = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'criteria_') === 0) {
                $totalPoints[] = (int)$value;
            }
        }   

        $user_name = $_SESSION['user']['user_name'];
        $partaker_info = FindPartaker($dbh, $request_id, $toWhom);
        $reviewee_id = $partaker_info['circle_member_id'];

        Review($dbh, $_POST, $from,$reviewee_id);
        LevelUp($dbh, $totalPoints, $partaker_info);
        RecalculateRanks($dbh);
        $request_title = mb_convert_encoding($request_title, 'UTF-8', 'auto');
        if (mb_strlen($request_title, 'UTF-8') > 15) {
            $request_title = mb_substr($request_title, 0, 15, 'UTF-8') . '...';
        }
        if ($toWhom == 'requester') {   
            NotifyUser(dbh: $dbh, userId: $partaker_info['user_id'], requestId: $request_id, notification: "{$request_title}: {$user_name}さんが評価しました！", redirectTo:'review/view&section_type=requester');
            CompleteRequest($dbh,$request_id);
        } else {
            NotifyUser(dbh: $dbh, userId: $partaker_info['user_id'], requestId: $request_id, notification: "{$request_title}: {$user_name}さんが決済・評価しました！依頼の評価をしましょう", redirectTo:'review');
            NotifyUser(dbh: $dbh, userId: $partaker_info['user_id'], requestId: $request_id, notification: "{$request_title}: {$user_name}さんの評価はこちら！", redirectTo:'review/view&section_type=worked_on');
        }
        UpdateUserRequestAndEvents($dbh);

        $dbh->commit();
        header(HEADER_HP_PATH);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        $message = "Error: " . $e->getMessage();
        var_dump($message);
    }
}
?>

<div class="flex flex-col items-center gap-3 mb-3"><?= DisplaySubmission($submission_id, $submission); ?></div>

<form method="POST" id="reviewForm" class="flex flex-col justify-center gap-8 pb-5">
    <!-- Required fields for the backend logic -->
    <input type="hidden" name="submission_id" value="<?= $submission_id ?>">
    <input type="hidden" name="reviewer_circle_member_id" value="<?= $user_id ?>">
    <input type="hidden" name="towards" value="<?= $toWhom ?>"> <!-- 'giver' or 'requester' -->

    <div class="flex flex-col items-center gap-3 mt-8">
        <h1 class="text-themeYellow">評価ページ</h1>
        <h2>評価がそのままスコアになります。相手に与えたいポイントを入力してください</h2>
    </div>

    <? foreach ($criteriaList as $criteria) { ?>
        <div class="flex items-center gap-6 justify-between w-full">
            <label class="text-[22px]"><?= htmlspecialchars($criteria['description']) ?>:</label>
            <input
                class="bg-themeGray w-40 h-10 text-center"
                type="number"
                name="criteria_<?= $criteria['id'] ?>"
                min="0" max="5"
                required>
        </div>
    <? } ?>

    <div class="flex justify-between">
        <label for="comment" class="text-[22px]">コメント:</label>
        <textarea
            name="comment"
            id="comment"
            rows="4"
            cols="15"
            class="bg-themeGray w-80 h-40"></textarea>
    </div>

    <div class="flex justify-center gap-8">
        <fieldset class="<?= $isRequester ? '' : 'hidden' ?>">
            <button type="button" id="paymentBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="event.preventDefault()">
                支払い状況を確認
            </button>
        </fieldset>
        <?= Button("送信", extraCSS: "!w-40 !h-10 font-bold rounded", extraAttribute: $isRequester ? 'disabled' : '', id: 'submitReview') ?>
    </div>
</form>

<?= RenderPaymentModalScript(); ?>
