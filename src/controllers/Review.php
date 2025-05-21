<?php
require_once CONTROLLERS_PATH . '/RetrieveReviewCriteria.php';

function Review(PDO $dbh, array $post, string $from, string $reviewee) {
    // Retrieve criteria list
    $criteriaList = RetrieveReviewCriteria($dbh, $from);
    if (!$criteriaList) {
        return 0;
    }

    // Insert or update overall comment
    $stmtComment = $dbh->prepare("
        INSERT INTO review_comments (
            submission_id, 
            reviewer_circle_member_id, 
            comment
        ) VALUES (
            :submission_id, 
            :reviewer_circle_member_id, 
            :comment
        )
        ON DUPLICATE KEY UPDATE comment = VALUES(comment)
    ");

    $stmtComment->bindValue(':submission_id', $post['submission_id'], PDO::PARAM_INT);
    $stmtComment->bindValue(':reviewer_circle_member_id', $post['reviewer_circle_member_id'], PDO::PARAM_INT);
    $stmtComment->bindValue(':comment', $post['comment'] ?? null, PDO::PARAM_STR);
    $stmtComment->execute();

    // Prepare statement for inserting each criterion score
    $stmtReview = $dbh->prepare("
        INSERT INTO reviews (
            submission_id, 
            reviewer_circle_member_id, 
            reviewee_circle_member_id, 
            review_criteria_id, 
            point, 
            towards
        ) VALUES (
            :submission_id, 
            :reviewer_circle_member_id, 
            :reviewee_circle_member_id, 
            :review_criteria_id, 
            :point, 
            :towards
        )
    ");

    foreach ($criteriaList as $criteria) {
        $criteria_id = $criteria['id'];
        if (isset($post["criteria_$criteria_id"])) {
            $stmtReview->bindValue(':submission_id', $post['submission_id'], PDO::PARAM_INT);
            $stmtReview->bindValue(':reviewer_circle_member_id', $post['reviewer_circle_member_id'], PDO::PARAM_INT);
            $stmtReview->bindValue(':reviewee_circle_member_id', $reviewee, PDO::PARAM_INT);  // Bind the reviewee ID
            $stmtReview->bindValue(':review_criteria_id', $criteria_id, PDO::PARAM_INT);
            $stmtReview->bindValue(':point', $post["criteria_$criteria_id"], PDO::PARAM_INT);
            $stmtReview->bindValue(':towards', $post['towards'], PDO::PARAM_STR);
            $stmtReview->execute();
        }
    }
}
