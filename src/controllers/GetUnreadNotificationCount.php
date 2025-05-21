<?php
function getUnreadNotificationCount(PDO $dbh, int $user_id): string {
    $stmt = $dbh->prepare("
        SELECT COUNT(*) 
        FROM notifications 
        WHERE user_id = :user_id AND is_read = 0
    ");

    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $count = (int) $stmt->fetchColumn();

    if ($count === 0) {
        return '';
    }

    return $count > 9 ? '9+' : (string) $count;
}
