<?php

require_once CONTROLLERS_PATH . '/NotifyMultipleUsers.php';
require_once CONTROLLERS_PATH . '/NotifyUser.php';

/**
 * Notifies one user that their submission has been selected, and
 * notifies all other submitters that they didn't get selected.
 *
 * @param PDO $dbh           The PDO database connection
 * @param string $request_id The request in question
 * @param string $submission_id The submission that has been selected
 */
function SelectionAnnouncement(PDO $dbh, string $request_id, string $submission_id, string $user_id, string $request_title) {
    $stmt = $dbh->prepare("
        SELECT circle_member_id
        FROM submissions
        WHERE id = :submission_id
        LIMIT 1
    ");
    $stmt->execute([':submission_id' => $submission_id]);
    $selectedCircleMemberId = $stmt->fetchColumn();
    
    $stmt = $dbh->prepare("
        SELECT user_id
        FROM circle_members
        WHERE id = :circle_member_id
        LIMIT 1
    ");
    $stmt->execute([':circle_member_id' => $selectedCircleMemberId]);
    $selectedUserId = $stmt->fetchColumn();

    $stmt = $dbh->prepare("
        SELECT line_link
        FROM user_socials
        WHERE user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([':user_id' => $user_id]);
    $line_link = $stmt->fetchColumn();

    NotifyUser(
        dbh: $dbh,
        userId: $selectedUserId,
        requestId: $request_id,
        notification: "{$request_title}: おめでとうございます! あなたが選ばれました！",
        redirectTo: "submit",
        externalLink: $line_link
    );

    $stmt = $dbh->prepare("
        SELECT cm.user_id
        FROM submissions s
        JOIN circle_members cm ON cm.id = s.circle_member_id
        WHERE s.request_id = :request_id
            AND s.id != :submission_id
    ");
    $stmt->execute([
        ':request_id'    => $request_id,
        ':submission_id' => $submission_id
    ]);
    $losingUserIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($losingUserIds)) {
        NotifyMultipleUsers(
            dbh: $dbh,
            userIds: $losingUserIds,
            requestId: $request_id,
            notification: "{$request_title}: 残念ながら今回はお見送りさせていただきます.",
        );
    }
}
