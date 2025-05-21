<?php
function EventDetail(PDO $dbh, string $user_id, string $event_id)
{
    // Get event details for the given event_id
    $event_stmt = $dbh->prepare('SELECT 
        e.id,
        e.title,
        e.description,
        e.prizes,
        e.submission_deadline,
        e.presentation_date,
        e.is_public,
        e.is_closed,
        u.id AS creator_id,
        u.name AS creator_name,
        u.image AS creator_image
    FROM events e
    JOIN users u ON e.created_by = u.id
    WHERE e.id = :event_id');

    // Get team details for the given event_id and user_id (user must be part of the team)
    $team_id_stmt = $dbh->prepare("SELECT et.id 
    FROM event_teams et
    JOIN event_team_members etm ON et.id = etm.team_id
    JOIN circle_members cm ON etm.circle_member_id = cm.id 
    WHERE et.event_id = :event_id AND cm.user_id = :user_id");

    $team_stmt = $dbh->prepare('SELECT 
        et.id AS team_id,
        et.name AS team_name,
        et.created_by_circle_member_id,
        cm.user_id,
        u.name AS member_name,
        cm.circle_id
    FROM event_teams et
    JOIN event_team_members etm ON et.id = etm.team_id
    JOIN circle_members cm ON etm.circle_member_id = cm.id
    JOIN users u ON cm.user_id = u.id
    WHERE et.id = :team_id AND cm.user_id != :user_id');

    // Get submission details for the given event_id and user_id (whether submitted individually or as part of a team)
    $submission_stmt = $dbh->prepare('SELECT 
        es.id AS submission_id,
        es.submission,
        es.status,
        es.submitted_at,
        es.team_id,
        es.circle_member_id
    FROM event_submissions es
    INNER JOIN circle_members cm ON cm.id = es.circle_member_id
    WHERE es.event_id = :event_id AND cm.user_id = :user_id OR es.team_id = :team_id');

    // Get count of pending invitations for the team (based on team_id)
    $pending_invitation_count_stmt = $dbh->prepare('SELECT COUNT(*) 
    FROM event_invitations ei
    WHERE ei.event_id = :event_id AND ei.team_id = :team_id AND ei.status = "pending"');

    // Bind values
    $team_id_stmt->bindValue(':event_id', $event_id);
    $team_id_stmt->bindValue(':user_id', $user_id);
    $team_id_stmt->execute();
    $team_id = $team_id_stmt->fetchColumn();

    $event_stmt->bindValue(':event_id', $event_id);
    $team_stmt->bindValue(':team_id', $team_id);
    $team_stmt->bindValue(':user_id', $user_id);
    $submission_stmt->bindValue(':event_id', $event_id);
    $submission_stmt->bindValue(':user_id', $user_id);
    $submission_stmt->bindValue(':team_id', $team_id);
    $pending_invitation_count_stmt->bindValue(':event_id', $event_id);
    $pending_invitation_count_stmt->bindValue(':team_id', $team_id);

    // Fetch event details
    if ($event_stmt->execute()) {
        $event = $event_stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch team details for the event and user
    if ($team_stmt->execute()) {
        $team = $team_stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];  // Fetch all team members as an array
    }

    // Fetch submission details for the event and user
    if ($submission_stmt->execute()) {
        $submission = $submission_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Fetch count of pending invitations for the team
    if ($pending_invitation_count_stmt->execute()) {
        $pending_invitation_count = $pending_invitation_count_stmt->fetchColumn();
    }

    // Merge event, team (as an array of members), submission, and pending invitation count
    return array_merge(
        $event ?? [],
        ['team_id' => $team_id],
        ['team_members' => $team ?? []],
        $submission ?? [],
        ['pending_invitation_count' => $pending_invitation_count ?? 0] // Default to 0 if no pending invitations
    );
}
