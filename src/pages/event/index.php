<?php
require_once DBCONNECT;
require_once JS_PATH . '/RenderEventPopUpSplideScript.php';
require_once JS_PATH . '/RenderCountDownScript.php';
$event_id = $_GET['id'];

// Query to fetch event details and associated images
$stmt = $dbh->prepare("SELECT e.id, e.title, e.submission_deadline, e.presentation_date, ei.image_path, ei.image_type
                        FROM events e
                        LEFT JOIN event_images ei ON e.id = ei.event_id
                        WHERE e.id = :event_id");
$stmt->bindValue(':event_id', $event_id);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$event = [
    'images' => []
];

if (!empty($results)) {
    $event['id'] = $results[0]['id'];
    $event['title'] = $results[0]['title'];
    $event['submission_deadline'] = $results[0]['submission_deadline'];
    $event['presentation_date'] = $results[0]['presentation_date'];

    foreach ($results as $row) {
        if ($row['image_type'] === 'メインビジュアル') {
            $event['images'][] = [
                'image_path' => $row['image_path'],
                'image_type' => $row['image_type']
            ];
        }
    }
}

$deadline = new DateTime($event['submission_deadline']);
$presentation = new DateTime($event['presentation_date']);
?>

<section class="flex flex-col items-center gap-4">
    <h1 class="text-themeYellow max-h-20 max-w-[1000px] truncate"><?= htmlspecialchars($event['title']) ?></h1>

    <div id="image-slider" class="splide">
        <div class="splide__track">
            <ul class="splide__list">
                <?php
                if ($event['images']) :
                    foreach ($event['images'] as $image) {
                ?>
                        <li class="splide__slide !h-fit">
                            <img src="/<?= htmlspecialchars($image['image_path']) ?>" class="w-144 mb-4" alt="<?= htmlspecialchars($image['image_type']) ?> image">
                        </li>
                <?php
                    };
                endif;
                ?>
            </ul>
        </div>
    </div>

    <?= RenderEventPopUpSplideScript() ?>

    <div>
        <strong class="text-themeYellow">提出締切:</strong> <?= $event['submission_deadline'] ?> <br>
        <strong class="text-themeYellow">発表日:</strong> <?= $event['presentation_date'] ?>
    </div>

    <div id="countdown" class="text-lg text-red-500 font-bold"></div>

    <a href="?page=event/join&id=<?= $event['id'] ?>" class="bg-themeYellow text-white rounded-md h-8 py-1 px-6">参加する</a>
</section>

<?= RenderCountdownScript($deadline->format('Y-m-d H:i:s'), 'countdown') ?>
