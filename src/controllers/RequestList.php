<?php
function RequestList(PDO $dbh)
{
    // Prepare the SQL statement with dynamic placeholders for circle_member_ids and circle_ids
    $requests_stmt = $dbh->prepare('SELECT 
        r.id,
        r.circle_member_id,
        r.is_completed,
        r.request,
        r.title,
        c.id AS circle_id,
        c.name AS circle_name,
        c.image AS circle_image,
        cat.category
    FROM requests r
    JOIN circle_members cm ON cm.id = r.circle_member_id
    JOIN circles c ON c.id = cm.circle_id
    JOIN categories cat ON cat.id = r.category_id
    LEFT JOIN submissions s ON s.request_id = r.id
    WHERE r.circle_member_id NOT IN (' . implode(',', array_fill(0, count($_SESSION['user']['circles']), '?')) . ') 
    AND c.id NOT IN (' . implode(',', array_fill(0, count($_SESSION['user']['circles']), '?')) . ') 
    AND r.is_completed = FALSE
    AND NOT EXISTS (
        SELECT 1 
        FROM submissions sub
        WHERE sub.circle_member_id IN (' . implode(',', array_fill(0, count($_SESSION['user']['circles']), '?')) . ') 
        AND sub.request_id = r.id
    )
    AND NOT EXISTS (
        SELECT 1 
        FROM submissions sel
        WHERE sel.request_id = r.id 
        AND sel.status = "selected"
    )
    GROUP BY r.id
    ORDER BY r.created_at DESC;
    ');

    // Prepare the circle_member_ids and circle_ids arrays
    $cm_ids = array_map(fn($circle) => $circle['circle_member_id'], $_SESSION['user']['circles']);
    $c_ids = array_map(fn($circle) => $circle['circle_id'], $_SESSION['user']['circles']);
    $sub_ids = array_merge($cm_ids, $c_ids, $cm_ids);  // Merge all to reuse in the NOT IN clauses
    
    // Bind the values for each placeholder
    $index = 1;
    foreach ($sub_ids as $id) {
        $requests_stmt->bindValue($index++, $id, PDO::PARAM_INT);
    }

    // Execute the query
    if ($requests_stmt->execute()) {
        return $requests_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return null;
    }
}
