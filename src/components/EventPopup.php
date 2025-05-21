<?php
require_once JS_PATH . '/RenderEventPopUpSplideScript.php';
function EventPopup()
{
    $events = $_SESSION['events'];
    ob_start();
?>
    <? if ($events) { ?>
        <div class="flex justify-center mb-5">
            <div class="splide max-h-48 p-4 bg-themeGray" id="image-slider">
                <div class="splide__track w-full h-full">
                    <ul class="splide__list w-full h-full">
                        <? foreach ($events as $event) { ?>
                            <? foreach (array_filter($event['images'], fn($image) => $image['image_type'] == 'バナー') as $index => $image) { ?>
                                <li class="splide__slide flex justify-center !w-full !h-full">
                                    <a  href="?page=event&id=<?= $event['id'] ?>">
                                        <img src="<?= $image['image_path'] ?>" alt="<?= $event['id'] . '' . $index ?> popup">
                                    </a>
                                </li>
                            <? } ?>
                        <? } ?>
                    </ul>
                </div>
            </div>
        </div>
<? }
    echo RenderEventPopUpSplideScript();
    return ob_get_clean();
}
