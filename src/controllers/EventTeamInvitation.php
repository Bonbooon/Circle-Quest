<?php
require_once CONTROLLERS_PATH . '/NotifyUser.php';
require_once CONTROLLERS_PATH . '/NotifyMultipleUsers.php';

function EventTeamInvitation(PDO $dbh, array $post) {
    // Extract data from the POST request
    $creatorCircleMemberId = $post['creator_circle_member_id'];
    $eventId = $post['event_id'];
    $teamName = isset($post['team_name']) ? $post['team_name'] : null;
    $invitedCircleMemberId = $post['invited_circle_member_id'];

    // Step 1: Check if the user has a team for the event, if not create a new team
    $stmt = $dbh->prepare("
        INSERT INTO event_teams (event_id, name, created_by_circle_member_id)
        SELECT :event_id, :team_name, :creator_circle_member_id
        FROM DUAL
        WHERE NOT EXISTS (
            SELECT 1 FROM event_teams WHERE event_id = :event_id AND created_by_circle_member_id = :creator_circle_member_id
        )
    ");
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->bindParam(':team_name', $teamName, PDO::PARAM_STR);
    $stmt->bindParam(':creator_circle_member_id', $creatorCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();

    // Get the team ID (either newly created or already existing)
    $teamId = $dbh->lastInsertId();

    // If no team was created (existing team), fetch the team ID
    if (!$teamId) {
        $stmt = $dbh->prepare("
            SELECT id FROM event_teams
            WHERE event_id = :event_id AND created_by_circle_member_id = :creator_circle_member_id
        ");
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':creator_circle_member_id', $creatorCircleMemberId, PDO::PARAM_INT);
        $stmt->execute();
        $teamId = $stmt->fetchColumn();
    }

    // Step 2: Add the creator to the team if it's the first team created (first member)
    $stmt = $dbh->prepare("
        INSERT IGNORE INTO event_team_members (team_id, circle_member_id)
        VALUES (:team_id, :circle_member_id)
    ");
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':circle_member_id', $creatorCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();

    // Step 3: Send an invitation to the invited user
    $stmt = $dbh->prepare("
        INSERT INTO event_invitations (event_id, team_id, inviter_id, invitee_id, status)
        VALUES (:event_id, :team_id, :inviter_id, :invitee_id, 'pending')
    ");
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':inviter_id', $creatorCircleMemberId, PDO::PARAM_INT);
    $stmt->bindParam(':invitee_id', $invitedCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();

    // Step 4: Fetch the event name for the first notification
    $stmt = $dbh->prepare("SELECT title FROM events WHERE id = :event_id");
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $eventName = $stmt->fetchColumn();

    // Step 5: Fetch the invited circle member's name for the second notification
    $stmt = $dbh->prepare("SELECT name FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $invitedCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();
    $invitedUserName = $stmt->fetchColumn();

    // Step 6: Send a notification to the invited user
    $notification = "{$eventName}イベント: {$teamName}への招待が来ました";
    $redirectUrl = "event/select&teamId=$teamId&senderId=$creatorCircleMemberId";
    NotifyUser(
        dbh: $dbh,
        userId: $invitedCircleMemberId,
        eventId: $eventId,
        eventTeamId: $teamId,
        notification: $notification,
        redirectTo: $redirectUrl
    );

    // Step 7: Notify all team members (excluding the creator) about the new invitation
    $stmt = $dbh->prepare("
        SELECT circle_member_id FROM event_team_members etm
        JOIN circle_members cm ON etm.circle_member_id = cm.id
        JOIN users u ON cm.user_id = u.id
        WHERE etm.team_id = :team_id AND u.id != :user_id
    ");
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user']['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the user IDs of all team members (excluding the creator)
    if ($teamMembers) {
        $userIds = array_map(function ($member) {
            return $member['circle_member_id'];
        }, $teamMembers);
    
        // Step 8: Send notifications to all team members using NotifyMultipleUsers
        $notification = "新しいメンバー {$invitedUserName} がチーム {$teamName} に招待されました";

        NotifyMultipleUsers(
            dbh: $dbh,                       // Database connection
            userIds: $userIds,                // Array of user IDs to notify
            notification: $notification,      // The notification message
            senderId: $creatorCircleMemberId, // Sender ID (the creator of the team)
            eventId: $eventId,               // Event ID (the event associated with the invitation)
            eventTeamId: $teamId,            // Team ID (the team to which the user is invited)
        );
    }

    return true; // Return true for successful invitation creation
}
