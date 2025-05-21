<?php
require_once COMPONENTS_PATH . '/CustomH3.php';
require_once COMPONENTS_PATH . "/ProfileImg.php";
require_once COMPONENTS_PATH . '/showDetailButton.php';

function RequestsDisplay($requests, $text, $redirects_to = null, $has_not_selected = false)
{
    if (empty($requests)) {
        return '';
    }
    ob_start();
?>
    <div class="relative pt-7 w-full h-fit flex flex-col justify-between items-center">
        <?= CustomH3(text: $text, extraCSS: "absolute top-0 left-0 font-bold"); ?>
        <div class="flex flex-col justify-center">
            <?php foreach ($requests as $request) { ?>
                <div class="flex gap-5 p-9 w-[780px] items-center justify-between">
                    <?php if (!$has_not_selected) { ?>
                        <?= ProfileImg($request['circle_image'], "?page=profile/circle&id={$request['circle_member_id']}"); ?>
                        <div class="text-center w-44">
                            <span><strong>依頼者:</strong></span>
                            <p><?= htmlspecialchars($request['circle_name']) ?></p>
                        </div>
                        <div class="text-center w-44 text-nowrap">
                            <span><strong>依頼:</strong></span>
                            <p class="truncate"><?= htmlspecialchars($request['title']) ?></p>
                        </div>
                    <?php } else { ?>
                        <div class="w-24 h-24 flex items-center justify-center rounded-[50%] bg-themeGray">
                            <p class="text-3xl"><?= isset($request['submission_count']) ? $request['submission_count'] : "0" ?></p>
                        </div>
                        <div class="text-center w-44 text-nowrap">
                            <span><strong>依頼:</strong></span>
                            <p class="truncate"><?= htmlspecialchars($request['title']) ?></p>
                        </div>
                    <?php } ?>
                    <?= isset($redirects_to) ? showDetailButton($redirects_to, $request['id']) : ''?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}
?>
