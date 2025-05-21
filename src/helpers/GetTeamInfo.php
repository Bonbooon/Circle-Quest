<?php
function GetTeamInfo(PDO $dbh, ?string $team_id): array 
{
    $stmt = $dbh->prepare(" 
        SELECT 
            et.name AS event_team_name, 
            et.id AS team_id, 
            et.created_by_circle_member_id
        FROM 
            event_teams et
        LEFT JOIN 
            event_team_members etm ON et.id = etm.team_id
        WHERE 
            et.id = :team_id
    ");
    
    $stmt->bindValue(':team_id', $team_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    $teamData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teamData) {
        return []; 
    }
    
    $teamInfo['event_team_name'] = $teamData['event_team_name'];
    $teamInfo['team_id'] = $teamData['team_id'];
    $teamInfo['created_by_circle_member_id'] = $teamData['created_by_circle_member_id'];
    
    $stmt = $dbh->prepare(" 
        SELECT 
            etm.circle_member_id
        FROM 
            event_team_members etm
        WHERE 
            etm.team_id = :team_id AND 
            etm.circle_member_id != :created_by_circle_member_id
    ");
    
    $stmt->bindValue(':team_id', $team_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_by_circle_member_id', $teamData['created_by_circle_member_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    // Use FETCH_COLUMN to return only the circle_member_id values as a simple indexed array
    $teamInfo['circle_member_ids'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    return $teamInfo;
}
