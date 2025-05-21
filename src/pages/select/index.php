<?php
require_once DBCONNECT;
require_once CONTROLLERS_PATH . '/Submissions.php';
require_once CONTROLLERS_PATH . '/SelectSubmission.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once CONTROLLERS_PATH . '/SelectedSubmission.php';
require_once CONTROLLERS_PATH . '/SelectionAnnouncement.php';
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/Nothing.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/RenderSubmissionModal.php';
require_once COMPONENTS_PATH . '/RenderSubmissionButton.php';
require_once JS_PATH . '/RenderModalScriptOnce.php';

$request_id = (int)$_GET['id'];

if (SelectedSubmission($dbh, $request_id)) {
    header(HEADER_HP_PATH);
    exit;
}

$stmt = $dbh->prepare("SELECT title FROM requests WHERE id = :request_id");
$stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
$stmt->execute();
$request_title = $stmt->fetchColumn();

$user_id = $_SESSION['user']['user_id'];
$submissions = Submissions($dbh, $request_id);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh->beginTransaction();
        if (!is_string($_POST['submission_id'])) {
            throw new Exception('提出IDがPOSTされてません');
        }

        $submission_id = $_POST['submission_id'];

        if (!SelectSubmission($dbh, $submission_id)) {
            throw new Exception("選定できませんでした");
        }
        $dbh->commit();
        SelectionAnnouncement($dbh,$request_id,$submission_id, $user_id, $request_title);
        UpdateUserRequestAndEvents($dbh);
        header(HEADER_HP_PATH);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        $message = $e->getMessage();
    }
}
?>
<? if (empty($submissions)) { ?>
    <?= Nothing('まだ誰も提出していないようです…') ?>
<? } else { ?>
    <div class="w-full flex justify-center">
        <h1 class="text-center text-2xl font-bold mb-4 truncate max-w-[1000px]"><?= $request_title ?></h1>
    </div>
    <form action="" method="POST" class="flex flex-col gap-14 items-center">
        <? foreach ($submissions as $submission) { ?>
            <div class="flex flex-col gap-4">
                <fieldset class="w-[780px] flex gap-6 items-center justify-between">
                    <?= ProfileImg(img: $submission['circle_image']) ?>
                    <p class="w-[200px] max-w-[250px] text-center text-nowrap"><?= $submission['circle_name'] ?></p>
                    <?= RenderSubmissionButton($submission['submission_id']) ?>
                    <fieldset>
                        <input type="radio" id="<?= $submission['circle_name'] ?>" name="submission_id" value="<?= $submission['submission_id'] ?>" class="hidden peer" required>
                        <label for="<?= $submission['circle_name'] ?>" class="h-10 w-40 py-2 px-6 cursor-pointer rounded-md bg-limeGreen text-center font-semibold peer-checked:bg-limeGreenChecked">
                            お願いする
                        </label>
                    </fieldset>
                </fieldset>

                <?= RenderSubmissionModal($submission['submission_id'], $submission['submission']) ?>
            </div>
        <? } ?>
        <?= Button(text: "確定") ?>
    </form>
<? } ?>
<?= RenderModalScriptOnce() ?>
