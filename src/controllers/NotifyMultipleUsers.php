<?php
function NotifyMultipleUsers(
    PDO $dbh,
    array $userIds,
    string $notification,
    ?string $senderId = null,
    ?string $requestId = null,
    ?string $eventId = null,
    ?string $eventTeamId = null,
    ?string $action = null,
    ?string $redirectTo = null,
    ?string $externalLink = null,
    bool $notifyAdmin = false
) {
    // If no userIds are provided, fetch them
    if (empty($userIds)) {
        $sql = "SELECT id FROM users WHERE is_admin = 0"; // Default to non-admin users
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return "Error fetching non-admin users: " . $e->getMessage();
        }
    }

    // If notifyAdmin is true, get the admin users and merge them with userIds
    if ($notifyAdmin) {
        try {
            $stmt = $dbh->prepare("SELECT id FROM users WHERE is_admin = 1");
            $stmt->execute();
            $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Merge user IDs with admin IDs, ensuring no duplicates
            $userIds = array_unique(array_merge($userIds, $adminIds));

        } catch (PDOException $e) {
            return "Error fetching admin users: " . $e->getMessage();
        }
    }

    // If no users are found, return 0
    if (empty($userIds)) {
        return 0;
    }

    // Prepare placeholders and parameters for each user
    $placeholders = [];
    $params = [];

    foreach ($userIds as $index => $userId) {
        $placeholders[] = "(:user_id_$index, :request_id_$index, :sender_id_$index, :event_id_$index, :eventTeam_id_$index, :action_$index, :notification_$index, :redirects_to_$index, :external_link_$index)";

        $params[":user_id_$index"]      = $userId;
        $params[":request_id_$index"]   = $requestId;
        $params[":sender_id_$index"]   = $senderId;
        $params[":event_id_$index"]   = $eventId;
        $params[":eventTeam_id_$index"]   = $eventTeamId;
        $params[":action_$index"]   = $action;
        $params[":notification_$index"] = $notification;
        $params[":redirects_to_$index"]  = $redirectTo;
        $params[":external_link_$index"]  = $externalLink;
    }

    // Prepare and execute the SQL query to insert notifications
    $sql = "
        INSERT INTO notifications (user_id, request_id, sender_id, event_id, event_team_id, action, notification, redirects_to, external_link)
        VALUES " . implode(", ", $placeholders) . ";";

    try {
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        return count($userIds);
    } catch (PDOException $e) {
        return "Bulk notification error: " . $e->getMessage();
    }
}
