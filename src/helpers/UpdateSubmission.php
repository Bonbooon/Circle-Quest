<?php
/**
 * Updates an existing submission
 * @param PDO $dbh Database connection
 * @param string $table The table to update (event_submissions or submissions)
 * @param int $submission_id The ID of the existing submission
 * @param string|null $submission The file path for the submission (optional)
 * @param string $request_id The request or event ID
 * @param string $circle_member_id The circle member ID
 * @return bool Success status of the update
 */
function UpdateSubmission(PDO $dbh, string $table, int $submission_id, ?string $submission): bool
{
    $update_query = "UPDATE $table 
                    SET submission = :submission, 
                        submitted_at = NOW() 
                    WHERE id = :id";

    $update_stmt = $dbh->prepare($update_query);
    $update_stmt->bindValue(':submission', $submission);
    $update_stmt->bindValue(':id', $submission_id);

    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update submission');
    }

    return true;
}
