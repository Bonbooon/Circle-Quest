<?php
function UserRequestHistory(PDO $dbh, string $user_id): array
{
    $query = "SELECT 
        r.id AS request_id, 
        r.title AS request_title, 
        AVG(rv.point) AS avg_review_score,
        r.updated_at AS request_updated_at,
        'worked_on' AS section_type,
        requester_circle.id AS circle_id,
        requester_circle.name AS circle_name,
        requester_circle.image AS circle_image
    FROM requests r
    JOIN submissions s ON r.id = s.request_id AND s.status = 'selected'
    JOIN circle_members cm ON cm.id = s.circle_member_id
    JOIN circle_members cm2 ON cm2.circle_id = r.circle_member_id
    JOIN users u ON u.id = cm.user_id
    LEFT JOIN reviews rv ON rv.submission_id = s.id AND rv.towards = 'giver'
    JOIN circles requester_circle ON requester_circle.id = cm2.circle_id
    WHERE u.id = :user_id AND r.is_completed = 1
    GROUP BY r.id, requester_circle.id, requester_circle.name, requester_circle.image

    UNION

    SELECT 
        r.id AS request_id, 
        r.title AS request_title, 
        AVG(rv.point) AS avg_review_score,
        r.updated_at AS request_updated_at,
        'requested' AS section_type,
        giver_circle.id AS circle_id,
        giver_circle.name AS circle_name,
        giver_circle.image AS circle_image
    FROM requests r
    LEFT JOIN submissions s ON r.id = s.request_id AND s.status = 'selected'
    JOIN circle_members cm ON r.circle_member_id = cm.id
    JOIN circle_members cm2 ON cm2.circle_id = s.circle_member_id
    JOIN users u ON u.id = cm.user_id
    LEFT JOIN reviews rv ON rv.submission_id = s.id AND rv.towards = 'requester'
    JOIN circles giver_circle ON giver_circle.id = cm2.circle_id
    WHERE u.id = :user_id AND r.is_completed = 1
    GROUP BY r.id, giver_circle.id, giver_circle.name, giver_circle.image;
    ";

    // Prepare the statement
    $stmt = $dbh->prepare($query);

    // Bind the user_id parameter
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
