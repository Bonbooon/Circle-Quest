<?php
function SelectedSubmission(PDO $dbh, int $request_id): array | bool
{
    $stmt = $dbh->prepare("
        SELECT id, submission 
        FROM submissions 
        WHERE request_id = :request_id 
            AND status = 'selected'
        LIMIT 1
    ");
    $stmt->bindValue(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
