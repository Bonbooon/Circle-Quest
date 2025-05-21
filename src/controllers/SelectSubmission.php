<?php
function SelectSubmission(PDO $dbh, string $id) {
    // Get the request_id for the selected submission
    $get_request_stmt = $dbh->prepare("
        SELECT request_id 
        FROM submissions 
        WHERE id = :id
    ");
    $get_request_stmt->bindValue(':id', $id);
    $get_request_stmt->execute();
    $request_id = $get_request_stmt->fetchColumn();

    // Update the selected submission
    $select_stmt = $dbh->prepare("
        UPDATE submissions 
        SET status = 'selected' 
        WHERE id = :id
    ");
    $select_stmt->bindValue(':id', $id);
    $select_result = $select_stmt->execute();

    // Update all other submissions for this request to rejected
    $reject_stmt = $dbh->prepare("
        UPDATE submissions
        SET status = 'rejected'
        WHERE request_id = :request_id
        AND id != :id
    ");
    $reject_stmt->bindValue(':request_id', $request_id);
    $reject_stmt->bindValue(':id', $id);
    $reject_result = $reject_stmt->execute();

    return $select_result && $reject_result;
}
