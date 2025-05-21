<?php
function RequestDetail(PDO $dbh, string $user_id, string $request_id)
{
    // Get request details
    $request_stmt = $dbh->prepare('SELECT 
        r.id,
        r.circle_member_id AS requester_id,
        r.title,
        r.request,
        r.pay,
        r.due_date,
        r.is_completed,
        r.comment,
        cm.id AS circle_member_id,
        u.id AS requester_id,
        u.name AS requester_name,
        c.name AS requester_circle,
        c.image AS circle_image,
        c.level AS requester_circle_level,
        cat.category
    FROM requests r
    JOIN circle_members cm ON r.circle_member_id = cm.id
    JOIN users u ON cm.user_id = u.id
    JOIN circles c ON cm.circle_id = c.id
    JOIN categories cat ON r.category_id = cat.id
    WHERE r.id = :request_id');

    // Get submission details if exists
    $submission_stmt = $dbh->prepare('SELECT 
        s.id AS submission_id,
        s.submission,
        s.status,
        s.circle_member_id,
        CASE 
            WHEN s.circle_member_id IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS has_applied
    FROM submissions s
    JOIN circle_members cm ON s.circle_member_id = cm.id
    JOIN users u ON cm.user_id = u.id
    WHERE u.id = :user_id 
    AND s.request_id = :request_id');

    $request_stmt->bindValue(':request_id', $request_id);

    $submission_stmt->bindValue(':request_id', $request_id);
    $submission_stmt->bindValue(':user_id', $user_id);

    if ($request_stmt->execute()) {
        $request = $request_stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($submission_stmt->execute()) {
        $submission = $submission_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    return array_merge($request ?? [], $submission ?? []);
}
