<?php
function ReadNotifications(PDO $dbh, string $user_id) {
    $stmt = $dbh->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
    $stmt->bindValue(':user_id', $user_id);
    return $stmt->execute();
}
