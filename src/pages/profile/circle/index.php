<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/Nothing.php';
require_once COMPONENTS_PATH . '/LevelDisplay.php';
require_once COMPONENTS_PATH . '/CircleMemberDisplay.php';
require_once CONTROLLERS_PATH . '/GetCircleInfo.php';
require_once COMPONENTS_PATH . '/DisplaySocialLinks.php';
require_once COMPONENTS_PATH . '/ProfileEditComponent.php';
require_once COMPONENTS_PATH . '/SocialLinksEditComponent.php';
require_once JS_PATH . '/RenderEditSocialLinksScript.php';
require_once JS_PATH . '/RenderEditProfileImageScript.php';

// Get the circle ID from the URL query parameter
$circle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$circle = GetCircleInfo($dbh, $circle_id);
$isMember = in_array($_SESSION['user']['user_id'],array_map(function ($member) {
    return $member['user_id'];
}, $circle['members']));
?>

<div class="flex flex-col gap-5">
    <div class="flex items-center justify-center">
        <div class="flex items-center justify-center gap-20">
            <div class="w-fit h-full flex flex-col items-center justify-center">
                <?= ProfileImg(img: $circle['image'], isUser:false) ?>
                <?= $isMember ? ProfileEditComponent('circles', $circle_id) : '' ?>
            </div>
            <h1 class="text-2xl font-bold w-fit"><?= htmlspecialchars($circle['name']) ?></h1>
        </div>
    </div>
    <div class="flex items-center justify-center gap-4">
        <div class="flex items-center justify-center gap-40">
            <div class="flex flex-col items-center gap-5">
                <div class="w-[360px] flex flex-col gap-2">
                    <div class="flex justify-between h-fit">
                        <p class="format-p w-36">大学</p>
                        <div class="text-center overflow-y-clip max-w-[200px] w-52 max-h-[44px]">
                            <p><?= htmlspecialchars(implode(', ', $circle['colleges'])) ?></p>
                        </div>
                    </div>
                    <div class="flex justify-between h-fit">
                        <p class="format-p w-36">Give回数</p>
                        <span class="text-2xl w-[212px] text-center"><?= $circle['give_count'] ?></span>
                    </div>
                    <div class="flex justify-between h-fit">
                        <p class="format-p w-36">Request回数</p>
                        <span class="text-2xl w-[212px] text-center"><?= $circle['request_count'] ?></span>
                    </div>
                    <div class="flex justify-between h-fit">
                        <p class="format-p w-36">平均評価点</p>
                        <span class="text-2xl w-[212px] text-center"><?= $circle['average_review_score'] ?>点</span>
                    </div>
                    <div class="flex justify-between h-fit">
                        <p class="format-p w-36">人数</p>
                        <span class="text-2xl w-[212px] text-center"><?= count($circle['members']) ?>人</span>
                    </div>
                </div>
            </div>
            <div class="h-40 flex items-center flex-col gap-4">
                <?= Button(text: '履歴を見る', url: "?page=history&id=$circle_id&for=circle") ?>
                <h3 class="flex items-center justify-center text-lg">[作品リンク]</h3>
                <div class="flex gap-2 mt-2" id="social-links">
                    <?= DisplaySocialLinks($dbh, $circle_id) ?>
                </div>
                <?= $isMember ? SocialLinksEditComponent() : '' ?>
            </div>
        </div>
    </div>
    <?= LevelDisplay($_SESSION['user']['user_exp_point'], $_SESSION['user']['user_level']); ?>
    <? $members = CircleMembersDisplay($dbh, $circle_id, "メンバー一覧");
        echo $members == '' ? Nothing("メンバーがいません") : $members; ?>
</div>

<?= $isMember ? RenderEditSocialLinksScript($dbh) : ''; ?>
<?= $isMember ? RenderEditProfileImageScript() : ''; ?>
