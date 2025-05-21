<?php
/**
 * Creates a new submission
 * @param PDO $dbh Database connection
 * @param string $table The table to insert into (event_submissions or submissions)
 * @param string $id_field The request or event field (request_id or event_id)
 * @param string $id The request or event ID
 * @param string $circle_member_id The circle member ID
 * @param string|null $submission The file path for the submission (optional)
 * @return bool Success status of the creation
 */
require_once HELPERS_PATH . '/UpdateSubmission.php';
function CreateSubmission(PDO $dbh, string $table, string $id_field, string $id, string $circle_member_id, ?string $submission, ?string $team_id): bool
{
    // Check if team_id exists and include it in the query
    $team_id_column = $team_id ? ', team_id' : '';
    $team_id_value = $team_id ? ', :team_id' : '';

    $query = "INSERT INTO $table ($id_field, circle_member_id, status, submitted_at $team_id_column) 
                VALUES (:id, :circle_member_id, 'pending', NOW() $team_id_value)";

    $insert_stmt = $dbh->prepare($query);
    $insert_stmt->bindValue(':id', $id);
    $insert_stmt->bindValue(':circle_member_id', $circle_member_id);

    if ($team_id) {
        $insert_stmt->bindValue(':team_id', $team_id); // Bind team_id if provided
    }

    if (!$insert_stmt->execute()) {
        throw new Exception('Failed to create submission');
    }

    $submission_id = $dbh->lastInsertId();

    // If there is a file, update the submission with the file path
    if ($submission) {
        return UpdateSubmission($dbh, $table, $submission_id, $submission, $team_id);
    }

    return true;
}
