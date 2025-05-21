<?php
require_once DBCONNECT;
require_once CONTROLLERS_PATH . '/Is_admin.php';
Is_admin();

if (!$_SESSION['user']['is_admin']) {
    header("Location: /unauthorized");
    exit;
}

$stmt = $dbh->query("SELECT * FROM events ORDER BY created_at DESC");
$events = $stmt->fetchAll();
?>

<h1 class="text-2xl font-bold mb-4">イベント管理</h1>

<table class="w-full table-auto border">
    <thead>
        <tr>
            <th>タイトル</th>
            <th>締切</th>
            <th>発表日</th>
            <th>状態</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr>
            <td><?= htmlspecialchars($event['title']) ?></td>
            <td><?= $event['submission_deadline'] ?></td>
            <td><?= $event['presentation_date'] ?></td>
            <td><?= $event['is_closed'] ? '終了' : '進行中' ?></td>
            <td>
                <a href="?page=admin/edit-event.php?id=<?= $event['id'] ?>" class="text-blue-500">編集</a> |
                <a href="?page=event/<?= $event['id'] ?>" class="text-green-500">詳細</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
