<?php
function Submissions(PDO $dbh, string $request_id): ?array
{
    $submissions_stmt = $dbh->prepare('
        SELECT 
            s.id as submission_id,
            s.submission,
            c.name as circle_name,
            c.image as circle_image
        FROM submissions s
        INNER JOIN circle_members cm ON s.circle_member_id = cm.id
        INNER JOIN circles c ON cm.circle_id = c.id
        WHERE s.request_id = :request_id 
        AND s.submission IS NOT NULL
    ');

    $submissions_stmt->bindValue(":request_id", $request_id);
    if ($submissions_stmt->execute()) {
        return $submissions_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return null;
    }
}
