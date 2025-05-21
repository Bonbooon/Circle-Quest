<?php
require_once DBCONNECT;
require_once CONTROLLERS_PATH . '/GetReviews.php';

$request_id = $_GET['id'];

$towards = $_GET['section_type'];
if ($towards !== 'worked_on' && $towards !== 'requester') {
    die('Invalid section type');
} else {
    $towards = $towards === 'worked_on' ? 'giver' : 'requester';
}

$reviews = GetReviews($dbh, $request_id, $towards);
?>

<div class="reviews-container">
    <h1 class="text-3xl font-bold mb-6">レビュー</h1>
    <div class="mb-4">
        <div class="w-96 flex justify-between items-center">
            <p class="format-p w-60">レビューしたユーザー</p>
            <span class="text-gray-700"><?= htmlspecialchars($reviews[0]['reviewer_name']) ?></span>
        </div>
        <div class="w-96 flex justify-between items-center">
            <p class="format-p w-60">レビュー日時</p>
            <span class="text-gray-700"><?= htmlspecialchars(date('Y-m-d', strtotime($reviews[0]['created_at']))) ?></span>
        </div>
    </div>

    <?php if (isset($reviews) && count($reviews) > 0) { ?>
        <?php if ($reviews[0]['comment'] !== null) { ?>
            <div class="review-comment my-4 p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">コメント</h2>
                <p class="text-gray-700"><?= htmlspecialchars($reviews[0]['comment']) ?></p>
            </div>
        <?php } ?>
        <?php foreach ($reviews as $review) { ?>
            <div class="review-card p-6 mb-4 bg-white border border-gray-300 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold"><?= htmlspecialchars($review['criteria']) ?></h2>
                <p class="text-gray-700"><?= htmlspecialchars($review['description']) ?></p>
                <div class="review-rating mt-4">
                    <span class="text-lg font-medium text-blue-600">評価: <?= htmlspecialchars($review['point']) ?>/<?= htmlspecialchars($review['point_range']) ?></span>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p class="text-gray-500">このリクエストにはレビューがありません。</p>
    <?php } ?>
</div>
