<?php
function UserNotifications(PDO $dbh, string $user_id): ?array {
    $stmt = $dbh->prepare('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->bindValue(":user_id", $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
