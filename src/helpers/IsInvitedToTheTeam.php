<?php
function IsInvitedToTheTeam(PDO $dbh, string $invited_circle_member_id) {
    $stmt = $dbh->prepare('SELECT status
    FROM event_invitations et
    JOIN users u ON et.invitee_id = u.id
    JOIN circle_members cm ON cm.user_id = u.id
    WHERE event_id = :event_id
    AND team_id = :team_id
    AND cm.id = :invited_circle_member_id');
    $stmt->bindValue(':event_id', $_SESSION['event_id']);
    $stmt->bindValue(':team_id', $_SESSION['team_id']);
    $stmt->bindValue(':invited_circle_member_id', $invited_circle_member_id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        // Fetch the invitation status
        $invitation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $invitation['status'] === 'pending';
    } else {
        // No invitation found for the user
        return false;
    }
}
