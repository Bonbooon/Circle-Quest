<?php
function UserEventDetail(PDO $dbh, string $user_id)
{
    // Get event details for all events the user is participating in (either as a creator or a team member)
    $event_stmt = $dbh->prepare('SELECT 
        e.id AS event_id,
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
    WHERE EXISTS (
        SELECT 1
        FROM event_submissions es
        JOIN circle_members cm ON es.circle_member_id = cm.id
        WHERE es.event_id = e.id AND cm.user_id = :user_id
    )
    OR EXISTS (
        SELECT 1
        FROM event_teams et
        JOIN event_team_members etm ON et.id = etm.team_id
        JOIN circle_members cm ON etm.circle_member_id = cm.id
        WHERE et.event_id = e.id AND cm.user_id = :user_id
    )');

    // Get team details for each event
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

    // Get submission details for each event
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

    $event_stmt->bindValue(':user_id', $user_id);
    $events = $event_stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = [];
    if ($event_stmt->execute()) {
        while ($event = $event_stmt->fetch(PDO::FETCH_ASSOC)) {
            // Get team details for the current event
            $team_id_stmt->bindValue(':event_id', $event['event_id']);
            $team_id_stmt->bindValue(':user_id', $user_id);
            $team_id_stmt->execute();
            $team_id = $team_id_stmt->fetchColumn();
            $team_stmt->bindValue(':team_id', $team_id);
            $team_stmt->bindValue(':user_id', $user_id);
            $team_stmt->execute();
            $team = $team_stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Get submission details for the current event
            $submission_stmt->bindValue(':event_id', $event['event_id']);
            $submission_stmt->bindValue(':user_id', $user_id);
            $submission_stmt->bindValue(':team_id', $team_id);
            $submission_stmt->execute();
            $submission = $submission_stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            // Merge event, team, and submission data
            $event_data = array_merge($event, ['team_members' => $team], $submission);
            $events[] = $event_data;
        }
    }

    return $events;
}
