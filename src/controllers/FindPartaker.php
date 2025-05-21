<?php
function FindPartaker(PDO $dbh, string $request_id, string $toWhom):?array {
    if ($toWhom == 'giver') {
        $stmt = $dbh->prepare("
            SELECT
            cm.id as circle_member_id,
            cm.user_id as user_id,
            c.id as circle_id
            FROM circle_members cm
            LEFT JOIN submissions s ON s.circle_member_id = cm.id
            INNER JOIN circles c ON cm.circle_id = c.id
            INNER JOIN users u ON cm.user_id = u.id
            WHERE s.request_id = :request_id AND s.status = 'selected'
        ");
    } else {
        $stmt = $dbh->prepare("
            SELECT
            cm.id as circle_member_id,
            cm.user_id as user_id,
            c.id as circle_id
            FROM circle_members cm
            LEFT JOIN requests r ON r.circle_member_id = cm.id
            INNER JOIN circles c ON cm.circle_id = c.id
            INNER JOIN users u ON cm.user_id = u.id
            WHERE r.id = :request_id
        ");
    }
    $stmt->bindValue(":request_id", $request_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
