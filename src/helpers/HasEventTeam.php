<?php
function HasEventTeam(PDO $dbh, string $circle_member_id, ?string $team_id)
{
    $stmt = $dbh->prepare('SELECT * FROM event_submissions 
                    WHERE circle_member_id = :circle_member_id 
    AND (team_id != :team_id OR team_id IS NULL);');
    $stmt->execute(['circle_member_id' => $circle_member_id, 'team_id' => $team_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}
