<?php
require_once DBCONNECT;


$circle_member_id = $_SESSION['user']['circle_member_id'];
$event_id = $_GET['event_id'];

$stmt = $dbh->prepare("SELECT es.id AS submission_id, e.title AS event_title, es.submission
                        FROM event_submissions es
                        JOIN events e ON es.event_id = e.id
                        WHERE es.event_id = ?");
$stmt->execute([$event_id]);
$submissions = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['scores'] as $submission_id => $score) {
        $stmt = $dbh->prepare("REPLACE INTO event_votes (event_id, submission_id, voted_by_circle_member_id, score)
                                VALUES (?, ?, ?, ?)");
        $stmt->execute([$event_id, $submission_id, $circle_member_id, $score]);
    }
    echo "✅ 投票完了しました！";
    exit;
}
?>

<h1><?= $submissions[0]['event_title'] ?> - 投票</h1>

<form method="POST">
    <?php foreach ($submissions as $s): ?>
        <div class="mb-4">
            <p>提出内容: <?= htmlspecialchars($s['submission']) ?></p>
            <label>スコア (1〜5):</label>
            <input type="number" name="scores[<?= $s['submission_id'] ?>]" min="1" max="5" required>
        </div>
    <?php endforeach; ?>
    <button type="submit">投票を送信</button>
</form>
