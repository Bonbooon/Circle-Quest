<?php
require_once DBCONNECT;
require_once JS_PATH . '/RenderJoinEventScript.php';
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/ShowMessage.php';
require_once COMPONENTS_PATH . '/DisplaySubmission.php';
require_once CONTROLLERS_PATH . '/Submit.php';
require_once CONTROLLERS_PATH . '/EventDetail.php';
require_once CONTROLLERS_PATH . '/NotifyUser.php';
require_once CONTROLLERS_PATH . '/NotifyMultipleUsers.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once VALIDATIONS_PATH . "/Submission/handleFileUpload.php";
require_once VALIDATIONS_PATH . "/event/ValidateEventFormData.php";

$user = $_SESSION['user'];
$user_id = $user['user_id'];
$user_name = $user['user_name'];

// Retrieve event details
$event = EventDetail($dbh, $user_id, $_GET['id']);
$event_id = $event['id'];
$event_title = $event['title'];

// Check submission status
$submission_id = $event['submission_id'] ?? null;
$submission = $event['submission'] ?? null;
$has_applied = isset($event['submitted_at']) ?? false;
// Check if team exists
$team_id = $event['team_id'];

if (isset($event['team_members']) && !empty($event['team_members'])) {
    $team_members = $event['team_members'];
    $team_id = $team_members[0]['team_id'];
    $team_name = $team_members[0]['team_name'];
    $team_members_id = array_map(function ($member) {
        return $member['user_id'];
    }, $team_members);
}

$is_team_created = isset($team_id) && isset($team_members) && count($team_members) > 0;
$has_pending_invitation = $event['pending_invitation_count'] > 0;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $dbh->beginTransaction();

        // Check if it's a new application (first-time submission)
        if (!$has_applied) {
            ValidateEventFormData($_POST); // Validate form data for first submission
            $circle_member_id = $_POST['circle_member_id'];
            $submission = null;
            $team_id = isset($_POST['team']) ? $team_id : null;
            $redirect_to = 'Location: ' . $_SERVER['REQUEST_URI'];
        } else {
            $file = $_FILES['submission'] ?? null;

            if (!isValidFile($file['type'])) {
                throw new Exception('Invalid file type');
            }

            if ($file) {
                $circle_member_id = $event['circle_member_id'];
                $submission_file_path = $submission ? SUBMISSION_PATH . '/' . $submission : null;

                // Delete old submission file if it exists
                if ($submission_file_path && file_exists($submission_file_path)) {
                    unlink($submission_file_path);
                }

                // Handle file upload and get new file path
                $submission = handleFileUpload($file);
                $redirect_to = HEADER_HP_PATH;
            } else {
                throw new Exception("No file submitted");
            }
        }

        // Insert submission or update it if resubmitting
        if (!Submit($dbh, $event_id, $circle_member_id, $submission, true, $team_id)) {
            throw new Exception("Failed to insert into submissions table.");
        }

        $dbh->commit();
        UpdateUserRequestAndEvents($dbh);

        $is_first_submission = !isset($event['submission']);
        if (!$has_applied) {
            $notification = "{$user_name}が{$event_title}に応募をしました";
        } elseif ($is_first_submission) {
            $notification = "{$user_name}が{$event_title}に提出しました";
        } else {
            $notification = "{$user_name}が{$event_title}に再提出しました";
        }

        if (!isset($team_members_id)) {
            NotifyUser(
                dbh: $dbh,
                notification: $notification,
                notifyAdmin: true
            );
        } else {
            NotifyMultipleUsers(
                dbh: $dbh,
                userIds: $team_members_id,
                notification: "{$team_name}: {$notification}",
                notifyAdmin: true
            );
        }
        header($redirect_to);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        if ($submission_file_path && file_exists($submission_file_path)) {
            unlink($submission_file_path); // Clean up if there's an error
        }
        $message = $e->getMessage(); // Show error message
    }
}
?>
<div class="flex flex-col items-center gap-8">
    <div class="flex flex-col gap-4">
        <?= ShowMessage($message); ?>
        <h2 class="text-center text-xl font-semibold <?= $has_applied && !is_null($submission) ? 'hidden' : '' ?>">
            <?= $has_applied ? "作品を添付し提出してください" : "応募するには以下の項目を確認し「応募する」ボタンを押してください。" ?>
        </h2>
        <div class="flex flex-col items-center gap-4 mb-4">
            <div class="flex justify-center gap-4">
                <button id="btn-users" onclick="toggleButtonStyles('users')" class="ranking-toggle bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                    自分たちで
                </button>
                <button id="btn-circles" onclick="toggleButtonStyles('circles')" class="ranking-toggle bg-green-500 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                    チームで
                </button>
            </div>
            <a id="create-team-btn" href="?page=join&teaming=1&event_id=<?= $event['id']; ?><?= isset($team_id) ? '&team_id=' . $team_id : ''; ?>" class="-ml-[1px] px-6 py-2 rounded bg-yellow-500 text-white hidden">
                チームを作る
            </a>
        </div>
        <div class="flex flex-col items-center justify-center">
            <h2 class="text-center text-xl font-semibold">イベント:</h2>
            <h2 class="w-[512px] text-center text-4xl text-nowrap leading-none truncate">
                <?= htmlspecialchars($event['title']) ?>
            </h2>
        </div>
    </div>
    <form action="" class="flex flex-col items-center justify-center gap-8 pb-14" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="team" value="0">
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="event" name="event" value="1" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="event">イベント内容</label>
            </div>
            <div class="submit-form-description rounded-lg">
                <p><?= htmlspecialchars($event['description']) ?></p>
            </div>
        </fieldset>
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="event" name="submission_deadline" value="1" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="event" >応募デッドライン</label>
            </div>
            <div class="submit-form-description rounded-lg">
                <p><?= htmlspecialchars($event['submission_deadline']) ?></p>
            </div>
        </fieldset>
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="event" name="presentation_date" value="1" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="event">発表日</label>
            </div>
            <div class="submit-form-description rounded-lg">
                <p><?= htmlspecialchars($event['presentation_date']) ?></p>
            </div>
        </fieldset>
        <fieldset class="submit-form-fieldset">
            <div class="submit-form-checkbox">
                <input type="checkbox" id="event" name="prizes" value="1" required <?= $has_applied ? "checked disabled" : "" ?>>
                <label for="event">優勝商品</label>
            </div>
            <div class="submit-form-description rounded-lg">
                <p><?= htmlspecialchars($event['prizes']) ?></p>
            </div>
        </fieldset>

        <fieldset class="submit-form-fieldset">
            <div class="w-[512px] p-6 flex justify-center items-center bg-themeGray rounded-lg">
                <select name="circle_member_id" id="circle_member_id" class="bg-themeGray" required <?= $has_applied ? "disabled" : "" ?>>
                    <?php foreach ($user['circles'] as $circle) { ?>
                        <option value="<?= $circle['circle_member_id'] ?>"><?= $circle['circle_name'] ?></option>
                    <?php } ?>
                </select>
                <label for="circle_member_id">：として</label>
            </div>
        </fieldset>

        <?= DisplaySubmission($submission_id, $submission); ?>

        <?php if (!$has_applied) { ?>
            <input type="hidden" name="submission_type" value="apply">
            <button id="apply-btn" type="submit" class="px-6 py-2 rounded bg-blue-600 text-white w-40">
                応募する
            </button>
        <?php } else { ?>
            <input type="hidden" name="submission_type" value="draft">
            <input type="file" name="submission" id="submission" class="border p-2 w-80" required>
            <?= Button(is_null($submission) ? "提出する" : "再提出する") ?>
        <?php } ?>
    </form>
</div>
<?= RenderJoinEventScript($is_team_created, $has_applied, $has_pending_invitation) ?>
