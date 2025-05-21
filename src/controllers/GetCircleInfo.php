<?php
function GetCircleInfo(PDO $dbh, string $circle_id) {
    if ($circle_id === 0) {
        // Handle error, invalid ID
        echo "Invalid Circle ID!";
        exit;
    }
    
    // Fetch circle details from the database
    $query = "SELECT * FROM circles WHERE id = :circle_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(":circle_id", $circle_id, PDO::PARAM_INT);
    $stmt->execute();
    $circle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$circle) {
        // Handle error, circle not found
        echo "Circle not found!";
        exit;
    }
    
    // Fetch circle colleges
    $query = "SELECT college FROM circle_colleges WHERE circle_id = :circle_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':circle_id', $circle_id, PDO::PARAM_INT);
    $stmt->execute();
    $colleges = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch circle members
    $query = "SELECT cm.user_id, u.name FROM circle_members cm JOIN users u ON cm.user_id = u.id WHERE cm.circle_id = :circle_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':circle_id', $circle_id, PDO::PARAM_INT);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate the average review score
    $average_review_score = 0;
    $total_reviews = 0;
    $query = "
        SELECT r.point 
        FROM reviews r
        JOIN circle_members cm ON r.reviewee_circle_member_id = cm.id
        WHERE cm.circle_id = :circle_id
    ";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':circle_id', $circle_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($reviews) > 0) {
        $total_reviews = count($reviews);
        $total_score = array_sum(array_column($reviews, 'point'));
        $average_review_score = round($total_score / $total_reviews, 1);
    }
    
    return array_merge($circle, [
        'colleges' => $colleges,
        'members' => $members,
        'average_review_score' => $average_review_score,
        'total_reviews' => $total_reviews
    ]);
}
