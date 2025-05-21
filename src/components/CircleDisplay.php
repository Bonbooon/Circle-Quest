<?php
require_once COMPONENTS_PATH . "/ProfileImg.php";
require_once COMPONENTS_PATH . '/showDetailButton.php';
require_once COMPONENTS_PATH . '/CustomH3.php';
require_once HELPERS_PATH . '/CheckIfUserIsMember.php';
require_once JS_PATH . '/RenderCircleDisplayScript.php';

function CirclesDisplay($circles, $text, $redirects_to)
{
    if (empty($circles)) {
        return '';
    }

    $teaming = $_SESSION['teaming'];

    ob_start();
?>
    <div class="relative pt-16 w-full h-fit flex flex-col justify-between items-center">
        <?= CustomH3(text: $text, extraCSS: "absolute top-0 left-0"); ?>
        <div class="flex flex-col justify-center">
            <? foreach ($circles as $circle) { ?>
                <div class="flex gap-5 p-9 w-[780px] items-center justify-between bg-white shadow-md rounded-lg mb-4">
                    <?= ProfileImg(img: $circle['image'], path: "?page=profile/circle&id={$circle['id']}"); ?>
                    <div class="text-center w-44">
                        <p class="text-xl font-semibold"><?= htmlspecialchars($circle['name']) ?></p>
                    </div>
                    <?= showDetailButton($redirects_to, $circle['id']) ?>

                    <?if (!$teaming) {
                        if (!CheckIfUserIsMember($_SESSION['user']['user_id'], $circle['id'])) { ?>
                            <form method="POST" class="flex justify-center items-center" id="join-form-<?= $circle['id'] ?>" action="">
                                <input type="hidden" name='join' value="1">
                                <input type="hidden" name="circle_id" value="<?= $circle['id'] ?>">
                                <button type="submit" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600 focus:outline-none">
                                    参加する
                                </button>
                            </form>
                        <? } else { ?>
                            <form method="POST" class="flex justify-center items-center" id="leave-form-<?= $circle['id'] ?>" action="">
                                <input type="hidden" name='leave' value="1">
                                <input type="hidden" name="circle_id" value="<?= $circle['id'] ?>">
                                <button type="submit" class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-600 focus:outline-none">
                                    脱退する
                                </button>
                            </form>
                        <? } ?>
                    <? } ?>
                </div>
            <? } ?>
        </div>
    </div>

    <?= RenderCircleDisplayScript(); ?>

<?php
    return ob_get_clean();
}
?>
