<?php
function GetCircleMembers(PDO $dbh, string $circle_id) {
    // Prepare the query to retrieve only the required fields for circle members
    $stmt = $dbh->prepare('
        SELECT u.id as user_id,
                cm.id as circle_member_id, 
                u.name as user_name, 
                u.image as user_image, 
                u.level as user_level, 
                u.college as user_college
        FROM users u
        INNER JOIN circle_members cm ON u.id = cm.user_id
        WHERE cm.circle_id = :circle_id
    ');
    
    // Bind the circle_id parameter
    $stmt->bindValue(':circle_id', $circle_id);
    
    // Execute the query
    if ($stmt->execute()) {
        // Return the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return null;  // Return null if the query fails
    }
}
