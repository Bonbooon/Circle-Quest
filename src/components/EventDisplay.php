<?php
require_once COMPONENTS_PATH . '/CustomH3.php';
require_once COMPONENTS_PATH . "/ProfileImg.php";
require_once COMPONENTS_PATH . '/showDetailButton.php';

function EventsDisplay($events)
{
    if (empty($events)) {
        return '';
    }
    ob_start();
?>
    <div class="relative pt-7 w-full h-fit flex flex-col justify-between items-center">
        <?= CustomH3(text: 'イベント一覧', extraCSS: "absolute top-0 left-0 font-bold"); ?>
        <div class="flex flex-col justify-center">
            <?php foreach ($events as $event) { ?>
                <? if ($event['is_closed']) {
                    continue;
                } ?>
                <div class="flex gap-5 p-9 w-[780px] items-center justify-between">
                    <?= ProfileImg($event['creator_image'], "?page=profile/user&id={$event['creator_id']}"); ?>
                    <div class="text-center w-44">
                        <span><strong> 主催者:</strong></span>
                        <p><?= htmlspecialchars($event['creator_name']) ?>さん</p>
                    </div>
                    <div class="text-center w-44 text-nowrap">
                        <span><strong>イベント:</strong></span>
                        <p class="truncate">
                            <?= htmlspecialchars($event['title']) ?>
                        </p>
                    </div>
                    <?= showDetailButton('event/join', $event['event_id']) ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}
?>
