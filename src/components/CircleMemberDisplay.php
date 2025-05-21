<?php
require_once COMPONENTS_PATH . '/CustomH3.php';
require_once COMPONENTS_PATH . "/ProfileImg.php";
require_once COMPONENTS_PATH . "/ShowDetailButton.php";
require_once CONTROLLERS_PATH . '/GetCircleMembers.php';

function CircleMembersDisplay(PDO $dbh, string $circle_id, string $text)
{
    $members = GetCircleMembers($dbh, $circle_id);
    if (empty($members)) {
        return '';
    }
    ob_start();
?>
    <div class="relative pt-16 w-full h-fit flex flex-col justify-between items-center">
        <?= CustomH3(text: $text, extraCSS: "absolute top-0 left-0"); ?>
        <div class="flex flex-col justify-center">
            <? foreach ($members as $member) { ?>
                <? $is_user = $member['user_id'] == $_SESSION['user']['user_id'];?>
                <div class="flex gap-5 p-9 w-[916px] items-center justify-between bg-white shadow-md rounded-lg mb-4">
                    <?= ProfileImg(img: $member['user_image'], path: "?page=profile/user&id={$member['user_id']}"); ?>
                    <div class="text-center w-44">
                        <span><strong>名前:</strong></span>
                        <p class="text-xl font-semibold"><?= htmlspecialchars($is_user ? "あなた" : $member['user_name']) ?></p>
                    </div>
                    <div class="text-center w-44">
                        <span><strong>大学:</strong></span>
                        <p><?= htmlspecialchars($member['user_college']) ?></p>
                    </div>
                    <div class="text-center w-44">
                        <span><strong>レベル:</strong></span>
                        <p><?= htmlspecialchars($member['user_level']) ?></p>
                    </div>
                    <? if ($is_user) {
                        echo showDetailButton();
                    } else {
                        $teaming = isset($_SESSION['teaming']) && $_SESSION['teaming'] ? "&invited_circle_member_id={$member['circle_member_id']}" : ""; 
                        echo showDetailButton("profile/user{$teaming}", $member['user_id']); 
                    }?>
                </div>
            <? } ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}
