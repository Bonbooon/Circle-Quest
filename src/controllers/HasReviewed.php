<?php
function HasReviewed(PDO $dbh, string $user_id, ?int $submission_id = null, ?int $request_id = null, bool $return_array = false) {
    // Get the user's circle_member_id
    $stmt = $dbh->prepare("SELECT id FROM circle_members WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $circleMember = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$circleMember) {
        return $return_array ? [''] : false;
    }

    $circle_member_id = $circleMember['id'];

    // If no submission_id, try to get it via request_id
    if (is_null($submission_id) && !empty($request_id)) {
        $stmt = $dbh->prepare("
            SELECT id FROM submissions 
            WHERE request_id = :request_id AND status = 'selected'
        ");
        $stmt->execute([
            ':request_id' => $request_id,
        ]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$submission) {
            return $return_array ? [''] : false;
        }

        $submission_id = $submission['id'];
    }

    if (is_null($submission_id)) {
        return $return_array ? [''] : false;
    }

    // Fetch reviews and comments
    $stmt = $dbh->prepare("
        SELECT 'review' AS type, rc.criteria AS criteria, r.point, r.created_at, NULL AS comment
        FROM reviews r
        JOIN review_criteria rc ON r.review_criteria_id = rc.id
        WHERE r.submission_id = :submission_id AND r.reviewer_circle_member_id = :circle_member_id

        UNION ALL

        SELECT 'comment' AS type, NULL AS criteria, NULL AS point, c.created_at, c.comment
        FROM review_comments c
        WHERE c.submission_id = :submission_id AND c.reviewer_circle_member_id = :circle_member_id
    ");
    $stmt->execute([
        ':submission_id' => $submission_id,
        ':circle_member_id' => $circle_member_id
    ]);

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $return_array ? $reviews : !empty($reviews);
}
