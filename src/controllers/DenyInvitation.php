<?php
require_once CONTROLLERS_PATH . '/NotifyUser.php';

function DenyInvitation(PDO $dbh, int $teamId, int $eventID, int $invitedCircleMemberId, int $creatorCircleMemberId) {
    // Find the user name from the invited circle member id
    $stmt = $dbh->prepare('SELECT u.name FROM circle_members cm JOIN users u ON cm.user_id = u.id WHERE cm.id = :invited_circle_member_id');
    $stmt->bindParam(':invited_circle_member_id', $invitedCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();
    $invitedUserName = $stmt->fetchColumn();

    // Update the event_invitation status to 'rejected'
    $stmt = $dbh->prepare("
        UPDATE event_invitations
        SET status = 'rejected', rejected_at = NOW()
        WHERE event_id = :event_id AND invitee_id = :invitee_id AND team_id = :team_id
    ");
    $stmt->bindParam(':event_id', $eventID, PDO::PARAM_INT);
    $stmt->bindParam(':invitee_id', $invitedCircleMemberId, PDO::PARAM_INT);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update event invitation status to 'rejected'.");
    }

    // Notify the creator that the invited user denied the invitation
    $notification = "{$invitedUserName} はあなたのチームからの招待を拒否しました。";
    if (!NotifyUser(dbh: $dbh, userId: $creatorCircleMemberId, eventId: $eventID, notification: $notification)) {
        throw new Exception("Failed to send notification to the creator.");
    }

    // Check if there are no remaining members in the team and no pending or accepted invitations
    $stmt = $dbh->prepare("
        SELECT COUNT(*) AS member_count
        FROM event_team_members etm
        WHERE team_id = :team_id AND etm.circle_member_id != :circle_member_id
    ");
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':circle_member_id', $creatorCircleMemberId, PDO::PARAM_INT);
    $stmt->execute();
    $memberCount = $stmt->fetchColumn();

    $stmt = $dbh->prepare("
        SELECT COUNT(*) AS invitation_count
        FROM event_invitations
        WHERE team_id = :team_id AND status IN ('pending', 'accepted')
    ");
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
    $invitationCount = $stmt->fetchColumn();
    $noMorePendingInvitations = $memberCount == 0 && $invitationCount == 0;

    // If there are no members and no pending or accepted invitations, delete the team
    if ($noMorePendingInvitations) {
        $stmt = $dbh->prepare("
            DELETE FROM event_teams WHERE id = :team_id
        ");
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete team after invitation denial.");
        }

        // Notify the creator that the team has been deleted
        $notification = "{$invitedUserName} はあなたのチームからの招待を拒否しました。もう一度チームを作り直しましょう。";
        if (!NotifyUser(dbh: $dbh, userId: $creatorCircleMemberId, eventId: $eventID, notification: $notification)) {
            throw new Exception("Failed to send notification about team deletion.");
        }
    }

    return true; // Return true for successful denial
}
