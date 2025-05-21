<?php
require_once HELPERS_PATH . '/GetTeamInfo.php';
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/ShowMessage.php';
require_once COMPONENTS_PATH . '/ProfileEditComponent.php';
require_once COMPONENTS_PATH . '/SocialLinksEditComponent.php';
require_once JS_PATH . '/RenderEditSocialLinksScript.php';
require_once HELPERS_PATH . '/IsInvitedToTheTeam.php';
require_once HELPERS_PATH . '/HasEventTeam.php';
require_once CONTROLLERS_PATH . '/EventTeamInvitation.php';
require_once JS_PATH . '/RenderClearEventSessionScript.php';
require_once JS_PATH . '/RenderEditProfileImageScript.php';
require_once JS_PATH . '/RenderEditSocialLinksScript.php';

function Profile(array $user_info, ?PDO $dbh = null, bool $viewing = false, string $invited_circle_member_id = '')
{
    $message = '';
    $img_path = 'assets/img';
    $teamInfo = $_SESSION['teamInfo'] ?? null;
    $user_id = $user_info['user_id'];
    $team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : null;
    $event_id = isset($_SESSION['event_id']) ? $_SESSION['event_id'] : null;
    $teaming = isset($_SESSION['teaming']) ? $_SESSION['teaming'] : false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $dbh->beginTransaction();
            if (EventTeamInvitation($dbh, $_POST)) {
                $dbh->commit();
                $event_id = $_SESSION['event_id'];
                RenderClearEventSessionScript();
                header('Location: ?page=event/join&id=' . $event_id);
                exit;
            }
        } catch (Exception $e) {
            $dbh->rollBack();
            $message = $e->getMessage();
        }
    }
    ob_start();
?>
    <?= ShowMessage($message); ?>
    <div id="user-info-section">
        <section class="flex items-center justify-center gap-28">
            <div class="flex flex-col items-center gap-5">
                <section class="flex flex-col items-center gap-2">
                    <?= ProfileImg($user_info['user_image']) ?>
                    <?php if (!$viewing) { ?>
                        <?= ProfileEditComponent('users', $user_id) ?>
                    <?php } ?>
                </section>
                <section class="flex flex-col items-center gap-2">
                    <div class="flex gap-2 mt-2" id="social-links">
                        <a data-platform="line" href="<?= $user_info['line_link'] ?>" target="_blank">
                            <img src="<?= $img_path . '/line.svg' ?>" alt="line">
                        </a>
                        <a data-platform="instagram" href="<?= $user_info['instagram_link'] ?>" target="_blank">
                            <img src="<?= $img_path . '/instagram.svg' ?>" alt="instagram">
                        </a>
                        <a data-platform="twitter" href="<?= $user_info['twitter_link'] ?>" target="_blank">
                            <img src="<?= $img_path . '/twitter.svg' ?>" alt="twitter">
                        </a>
                    </div>
                    <? if (!$viewing) { ?>
                        <?= SocialLinksEditComponent() ?>
                    <? } ?>
                </section>
                <div>
                    <p class="h-fit">Give回数: <span><?= $user_info['user_give_count'] ?></span></p>
                    <p class="h-fit">Request回数: <span><?= $user_info['user_request_count'] ?></span></p>
                </div>
            </div>

            <div class="flex flex-col items-center gap-5">
                <p class="text-center h-fit text-2xl"><?= $user_info['user_name']; ?></p>
                <p class="text-center h-fit text-2xl"><?= $user_info['user_college']; ?></p>

                <div class="max-h-[100px] overflow-y-auto">
                    <div class="flex flex-col gap-4 justify-center items-center text-2xl" id="user-circles">
                        <?php foreach ($user_info['circles'] as $circle) { ?>
                            <a href="?page=profile/circle&id=<?= $circle['circle_id'] ?>">
                                <p class="text-center h-fit w-fit"><?= $circle['circle_name'] ?></p>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?= Button(text: "履歴を見る", url: "?page=history&id=$user_id&for=user") ?>

                <? if (!$viewing) { ?>
                    <?= Button(text: "サークルに参加する", url: '?page=join') ?>
                <? } elseif (!isset($_SESSION['teaming']) && !isset($_SESSION['event_id'])) { ?>
                <? } elseif (HasEventTeam($dbh, $invited_circle_member_id, $team_id)) { ?>
                    <p>すでにイベントに参加しています</p>
                <? } elseif (isset($teamInfo) && IsInvitedToTheTeam($dbh, $invited_circle_member_id)) { ?>
                    <p>イベントにはすでに招待しております</p>
                <? } elseif (isset($teamInfo) && $teamInfo['created_by_circle_member_id'] == $invited_circle_member_id) { ?>
                    <p><?= $teamInfo['event_team_name'] ?>のチームを作った人です</p>
                <? } elseif (isset($teamInfo) && in_array($invited_circle_member_id, $teamInfo['circle_member_ids']) && isset($teamInfo['team_id'])) { ?>
                    <p><?= $teamInfo['event_team_name'] ?>にはすでに所属しています</p>
                <? } elseif ($teaming && $event_id) { ?>
                    <form method="POST" class="flex flex-col gap-4 items-center">
                        <input type="hidden" name="invited_circle_member_id" value="<?= $invited_circle_member_id ?>">
                        <input type="hidden" name="event_id" value="<?= $event_id ?>">
                        <input type="hidden" name="team_id" value="<?= $team_id ?>">
                        <input type="hidden" name="team_name" value="<?= !is_null($teamInfo) ? $teamInfo['event_team_name'] : '' ?>">
            </div>
            <div class="flex flex-col items-center gap-5">

                <fieldset>
                    <label for="team_name">チーム名:</label>
                    <input class="text-center bg-themeGray" type="text" name="team_name" required <?= (isset($teamInfo['event_team_name']) && $teamInfo['team_id'] !== null) ? ' disabled value="' . $teamInfo['event_team_name'] . '"' : '' ?>>
                </fieldset>

                <?php if (!isset($teamInfo['team_id'])) { ?>
                    <fieldset class="flex flex-col gap-4">
                        <div class="w-[260px] p-6 flex flex-col justify-center items-center bg-themeGray">
                            <select name="creator_circle_member_id" id="creator_circle_member_id" class="bg-themeGray" required>
                                <?php foreach ($_SESSION['user']['circles'] as $circle) { ?>
                                    <option value="<?= $circle['circle_member_id'] ?>"><?= $circle['circle_name'] ?></option>
                                <?php } ?>
                            </select>
                            <label for="creator_circle_member_id">：として</label>
                        </div>
                    </fieldset>
                <?php } else { ?>
                    <input type="hidden" name="creator_circle_member_id" value="<?= $teamInfo['created_by_circle_member_id'] ?>">
                <?php } ?>
                <?= Button('チームを組む') ?>
                </form>
            <? } ?>
            </div>
        </section>
    </div>

    <?= !$viewing ? RenderEditSocialLinksScript($dbh) : ''; ?>
    <?= !$viewing ? RenderEditProfileImageScript() : ''; ?>
    <?= RenderClearEventSessionScript() ?>
<?php
    return ob_get_clean();
}
?>
