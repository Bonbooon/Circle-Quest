<?php
function NotifyUser(
    PDO $dbh, 
    string $notification, 
    ?string $userId = null,
    ?string $senderId = null, 
    ?string $requestId = null, 
    ?string $eventId = null,
    ?string $eventTeamId = null,
    ?string $action = null,
    ?string $redirectTo = null,
    ?string $externalLink = null,
    bool $notifyAdmin = false
) {
    try {
        $userIds = is_null($userId) ? [] : [$userId];
        
        if ($notifyAdmin) {
            $stmt = $dbh->prepare("SELECT id FROM users WHERE is_admin = 1");
            $stmt->execute();
            $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $userIds = array_unique(array_merge($userIds, $adminIds));
        }

        $placeholders = [];
        $params = [];

        foreach ($userIds as $index => $user) {
            $placeholders[] = "(:user_id_$index, :sender_id_$index, :request_id_$index, :event_id_$index, :event_team_id_$index, :action_$index, :notification_$index, :redirects_to_$index, :external_link_$index)";

            $params[":user_id_$index"] = $user;
            $params[":sender_id_$index"] = $senderId;
            $params[":request_id_$index"] = $requestId;
            $params[":event_id_$index"] = $eventId;
            $params[":event_team_id_$index"] = $eventTeamId;
            $params[":action_$index"] = $action;
            $params[":notification_$index"] = $notification;
            $params[":redirects_to_$index"] = $redirectTo;
            $params[":external_link_$index"] = $externalLink;
        }

        $sql = "
            INSERT INTO notifications (
                user_id, sender_id, request_id, event_id, event_team_id, 
                action, notification, redirects_to, external_link
            )
            VALUES " . implode(", ", $placeholders);

        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);

        return count($userIds);

    } catch (PDOException $e) {
        error_log("Notification error: " . $e->getMessage());
        return $e->getMessage();
    }
}
?>
