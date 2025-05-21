<?php
/**
 * Handles both initial application submission and file submission for requests or events.
 * @param PDO $dbh Database connection
 * @param string $id The request/event being submitted to
 * @param string $circle_member_id The circle member submitting
 * @param string|null $submission File path for submission (null for initial application)
 * @param bool $is_event Determines if this is for an event (default is true for event submission)
 * @return bool Success status of the submission
 */

require_once HELPERS_PATH . '/CreateSubmission.php';
require_once HELPERS_PATH . '/UpdateSubmission.php';
function Submit(PDO $dbh, string $id, string $circle_member_id, ?string $submission = null, bool $is_event = false, ?string $team_id = null): bool
{
    // Determine table and ID field based on the type (event or regular request)
    $table = $is_event ? 'event_submissions' : 'submissions';
    $id_field = $is_event ? 'event_id' : 'request_id';

    // Check if a submission already exists
    $check_stmt = $dbh->prepare("SELECT id FROM $table WHERE $id_field = :id AND circle_member_id = :circle_member_id");
    $check_stmt->bindValue(':id', $id);
    $check_stmt->bindValue(':circle_member_id', $circle_member_id);
    $check_stmt->execute();

    $existing_submission = $check_stmt->fetch();

    // Handle insertion or update based on whether a submission exists
    if ($existing_submission) {
        // Update the existing submission
        return UpdateSubmission($dbh, $table, $existing_submission['id'], $submission, $team_id);
    } else {
        // Create a new submission with team_id if provided
        return CreateSubmission($dbh, $table, $id_field, $id, $circle_member_id, $submission, $team_id);
    }
}
