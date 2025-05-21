<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/DisplaySubmission.php';
require_once COMPONENTS_PATH . '/ShowMessage.php';
require_once CONTROLLERS_PATH . '/Submit.php';
require_once CONTROLLERS_PATH . '/NotifyUser.php';
require_once CONTROLLERS_PATH . '/RequestDetail.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once VALIDATIONS_PATH . "/Submission/handleFileUpload.php";
require_once VALIDATIONS_PATH . "/Submission/validateSubmissionData.php";

$request_id = $_GET['id'];
$user = $_SESSION['user'];
$user_id = $user['user_id'];
$user_name = $user['user_name'];

$request = RequestDetail($dbh, $user_id, $request_id);
$request_id = $request['id'];
$request_title = mb_convert_encoding($request['title'], 'UTF-8', 'auto');
$requester_id = $request['requester_id'];

// Check if request exists and user has permission to view it
if (!$request || $request['is_completed'] || $request['requester_id'] == $user_id) {
    header(HEADER_HP_PATH);
    exit;
}

$submission_id = $request['submission_id'] ?? null;
$submission = $request['submission'] ?? null;
$is_first_submission = !isset($request['submission']);
$submission_file_path = $submission ? SUBMISSION_PATH . '/' . $submission : null;
$has_applied = $request['has_applied'] ?? false;
$is_selected = isset($request['status']) && $request['status'] === 'selected';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $dbh->beginTransaction();

        if (!$has_applied) {
            validateSubmissionData($_POST);
            $circle_member_id = $_POST['circle_member_id'];
            $submission = null;
            $redirect_to = 'Location: ' . $_SERVER['REQUEST_URI'];
        } else {
            $file = $_FILES['submission'] ?? null;

            if (!isValidFile($file['type'])) {
                throw new Exception('この拡張子は受け付けられません');
            }

            if ($file) {
                $circle_member_id = $request['circle_member_id'];
                if ($submission_file_path && file_exists($submission_file_path)) {
                    unlink($submission_file_path);
                }
                $submission = handleFileUpload($file);
                $redirect_to = HEADER_HP_PATH;
            } else {
                throw new Exception("ファイルが提出できていません");
            }
        }

        if (!Submit(dbh: $dbh, id: $request_id, circle_member_id: $circle_member_id, submission: $submission)) {
            throw new Exception("Failed to insert into submissions table.");
        }

        $dbh->commit();
        UpdateUserRequestAndEvents($dbh);

        $submission_type = $_POST['submission_type'];
        $request_title = mb_convert_encoding($request_title, 'UTF-8', 'auto');

        if (mb_strlen($request_title, 'UTF-8') > 15) {
            $request_title = mb_substr($request_title, 0, 15, 'UTF-8') . '...';
        }

        if ($submission_type == 'apply') {
            $notification = "あなたの依頼({$request_title})に興味を持ってる人がいます: {$user_name}";
        } elseif ($submission_type == 'draft' && $is_first_submission) {
            $redirects_to = 'select';
            $notification = "あなたの依頼({$request_title})に参加した人がいます: {$user_name}";
        } elseif ($submission_type == 'final') {
            $redirects_to = 'review';
            $notification = "{$request_title}: {$user_name}が最終成果物を納品しました！";
        }
        if (isset($notification)) {
            NotifyUser(dbh: $dbh, userId: $requester_id, requestId: $request_id, notification: $notification, redirectTo: $redirects_to);
        }

        header($redirect_to);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        if ($submission_file_path && file_exists($submission_file_path)) {
            unlink($submission_file_path);
        }
        $message = $e->getMessage();
    }
}
?>

<div class="flex flex-col items-center gap-8">
    <div>
        <h2 class="text-center text-xl font-bold text-black">依頼者:</h2>
        <div class="flex items-center justify-between w-144">
            <div class="flex items-center gap-8">
                <?= ProfileImg(img: $request['circle_image']) ?>
                <p class="font-bold"><?= htmlspecialchars($request['requester_circle']); ?></p>
            </div>
            <p class="font-bold">LV. <?= htmlspecialchars($request['requester_circle_level']); ?></p>
        </div>
    </div>
    <div class="flex flex-col gap-4">
        <?= ShowMessage($message); ?>
        <h2 class="text-center text-xl font-semibold text-themeYellow <?= $has_applied && !is_null($submission) ? 'hidden' : '' ?>">
            <?= $has_applied ? "作品を添付し提出してください" : "応募するには以下の項目を確認し「応募する」ボタンを押してください。" ?>
        </h2>
        <div class="flex flex-col items-center justify-center">
            <h2 class="text-center text-xl font-bold">依頼:</h2>
            <h2 class="w-[512px] text-center text-4xl text-nowrap leading-none truncate">
                <?= htmlspecialchars($request['title']) ?>
            </h2>
        </div>
    </div>
    <form action="" class="flex flex-col items-center justify-center gap-8 pb-14" method="POST" enctype="multipart/form-data">
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="request" name="request" value="1" class="w-9" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="request" class="text-2xl text-left text-nowrap leading-none font-bold">依頼内容</label>
            </div>
            <div class="submit-form-description">
                <p><?= htmlspecialchars($request['request']) ?></p>
            </div>
        </fieldset>
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="request" name="due_date" value="1" class="w-9" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="request" class="text-2xl text-left leading-none w-18 font-bold">納期</label>
            </div>
            <div class="submit-form-description">
                <p><?= htmlspecialchars($request['due_date']) ?></p>
            </div>
        </fieldset>
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="request" name="pay" value="1" class="w-9" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="request" class="text-2xl text-left leading-none w-18 font-bold">金額</label>
            </div>
            <div class="submit-form-description">
                <p><?= htmlspecialchars($request['pay']) ?></p>
            </div>
        </fieldset>

        <? if ($request['comment']) { ?>
            <div class="submit-form-fieldset">
                <div class="submit-form-checkbox">
                    <input type="checkbox" id="request" name="comment" value="1" class="w-9" required <?= $has_applied ? "checked disabled" : "" ?>>
                    <label for="request" class="text-2xl text-left leading-none w-18 text-nowrap font-bold">コメント</label>
                </div>
                <div class="submit-form-description">
                    <p><?= htmlspecialchars($request['comment']) ?></p>
                </div>
            </div>
        <? } ?>

        <fieldset class="submit-form-fieldset">
            <div class="w-[512px] p-6 flex justify-center items-center bg-themeGray">
                <select name="circle_member_id" id="circle_member_id" class="bg-themeGray font-bold" required <?= $has_applied ? "disabled" : "" ?>>
                    <?php foreach ($user['circles'] as $circle) { ?>
                        <option value="<?= $circle['circle_member_id'] ?>"><?= $circle['circle_name'] ?></option>
                    <? } ?>
                </select>
                <label for="circle_member_id">：として</label>
            </div>
        </fieldset>

        <?= DisplaySubmission($submission_id, $submission); ?>

        <?php if (!$has_applied) { ?>
            <input type="hidden" name="submission_type" value="apply">
            <?= Button("応募する") ?>
        <?php } elseif ($is_selected) { ?>
            <input type="hidden" name="submission_type" value="final">
            <input type="file" name="submission" id="submission" class="border p-2 w-80" required>
            <?= Button(text: "最終成果物を提出する", extraCSS: "text-wrap w-[156px] h-auto") ?>
        <?php } else { ?>
            <input type="hidden" name="submission_type" value="draft">
            <input type="file" name="submission" id="submission" class="border p-2 w-80" required>
            <?= Button(is_null($submission) ? "提出する" : "再提出する") ?>
        <?php } ?>
    </form>
</div>
