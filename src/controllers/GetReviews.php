<?php
function GetReviews(PDO $dbh, int $request_id, string $towards)
{
    // Step 1: Retrieve the submission_id associated with the request_id
    $stmt = $dbh->prepare('SELECT id AS submission_id
            FROM submissions
            WHERE request_id = :request_id AND status = "selected"
            LIMIT 1
    ');
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the submission_id
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    // If a submission_id is found, proceed to fetch the reviews
    if ($submission) {
        $submission_id = $submission['submission_id'];

        // Step 2: Retrieve reviews along with reviewer's name and associated comments
        $stmt = $dbh->prepare('SELECT 
                r.id, 
                r.point, 
                r.created_at, 
                rc.criteria, 
                rc.description, 
                rc.point_range,
                u.name AS reviewer_name,
                review_comments.comment
            FROM reviews r
            JOIN review_criteria rc ON r.review_criteria_id = rc.id
            JOIN circle_members cm ON r.reviewer_circle_member_id = cm.id
            JOIN users u ON cm.user_id = u.id
            LEFT JOIN review_comments ON r.reviewer_circle_member_id = review_comments.reviewer_circle_member_id
            WHERE r.submission_id = :submission_id AND r.towards = :towards
            ORDER BY r.created_at DESC
        ');
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->bindParam(':towards', $towards, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the reviews
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $reviews;
    }

    return [];
}
