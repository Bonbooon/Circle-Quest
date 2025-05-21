<?php
function UserRequest(PDO $dbh, $user_id)
{
    // Requests the user has asked for
    $requestAskedStmt = $dbh
        ->prepare("SELECT 
            r.id,
            r.title,
            r.is_completed,
            u.id AS requester_id,
            c.image AS circle_image,
            c.name AS circle_name,
            cm.id AS circle_member_id,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM submissions s 
                    WHERE s.request_id = r.id 
                    AND s.status = 'selected'
                ) THEN 'selected'
                WHEN EXISTS (
                    SELECT 1 FROM submissions s 
                    WHERE s.request_id = r.id
                ) THEN 'pending'
                ELSE 'no_submission'
            END as request_status,
            (
                SELECT COUNT(*) 
                FROM submissions s 
                WHERE s.request_id = r.id
            ) as submission_count
        FROM requests r
        JOIN circle_members cm ON r.circle_member_id = cm.id
        JOIN users u ON cm.user_id = u.id
        JOIN circles c ON cm.circle_id = c.id
        WHERE u.id = :user_id
        ORDER BY r.created_at DESC
    ");

    // Requests the user is working on
    $requestsWorkingOnStmt = $dbh
        ->prepare("SELECT 
            r.id,
            r.title,
            r.is_completed,
            s.status AS request_status,
            requester_c.image AS circle_image,
            requester_c.name AS circle_name,
            requester_cm.id AS circle_member_id,
            s.submitted_at,
            s.submission
        FROM requests r
        JOIN submissions s ON r.id = s.request_id
        JOIN circle_members cm ON s.circle_member_id = cm.id
        JOIN users u ON cm.user_id = u.id
        JOIN circle_members requester_cm ON r.circle_member_id = requester_cm.id
        JOIN circles requester_c ON requester_cm.circle_id = requester_c.id
        WHERE u.id = :user_id
        ORDER BY s.submitted_at DESC
    ");

    $requestAskedStmt->bindValue(':user_id', $user_id);
    $requestsWorkingOnStmt->bindValue(':user_id', $user_id);

    $requestAskedStmt->execute();
    $requestsWorkingOnStmt->execute();

    $requestAsked = $requestAskedStmt->fetchAll(PDO::FETCH_ASSOC);
    $requestsWorkingOn = $requestsWorkingOnStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        "requestsAsked" => $requestAsked,
        "requestsWorkingOn" => $requestsWorkingOn
    ];
};
