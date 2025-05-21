<?php
require_once CONTROLLERS_PATH . '/NotifyUser.php';
function AcceptInvitation(PDO $dbh, int $teamId, int $eventID, int $invitedCircleMemberId, int $creatorCircleMemberId) {
    // Find the user name from the invited circle member id
    $stmt = $dbh->prepare('SELECT u.name FROM circle_members cm JOIN users u ON cm.user_id = u.id WHERE cm.id = :invited_circle_member_id');
    $stmt->bindParam(':invited_circle_member_id', $invitedCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();
    $invitedUserName = $stmt->fetchColumn();

    // Update the event_invitation status to 'accepted'
    $stmt = $dbh->prepare("
        UPDATE event_invitations
        SET status = 'accepted', accepted_at = NOW()
        WHERE event_id = :event_id AND team_id = :team_id AND invitee_id = :invitee_id
    ");
    $stmt->bindParam(':event_id', $eventID, PDO::PARAM_INT);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':invitee_id', $invitedCircleMemberId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update event invitation status.");
    }

    // Add the invited user to the event team
    $stmt = $dbh->prepare("
        INSERT INTO event_team_members (team_id, circle_member_id)
        VALUES (:team_id, :circle_member_id)
    ");
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':circle_member_id', $invitedCircleMemberId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("Failed to add user to the event team.");
    }

    // Send a notification to the team creator about the accepted invitation
    $notification = "{$invitedUserName} はあなたのチームからの招待を承諾しました.";
    $redirectUrl = 'event/join'; // Redirect after acceptance
    if (!NotifyUser(dbh: $dbh, userId: $creatorCircleMemberId, eventId: $eventID, notification: $notification, redirectTo: $redirectUrl)) {
        throw new Exception("Failed to send notification to the creator.");
    }

    return true; // Return true for successful acceptance
}
