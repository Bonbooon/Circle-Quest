<?php
require_once DBCONNECT;  // Make sure this path is correct

header('Content-Type: application/json'); // Make sure the response is in JSON format

// Check if the search term is set
if (isset($_GET['term'])) {
    $term = "%" . $_GET['term'] . "%";

    try {
        // Prepare the SQL query with PDO
        $stmt = $dbh->prepare("SELECT id, name FROM circles WHERE name LIKE :term LIMIT 10");
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the results
        $circles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results as JSON
        echo json_encode($circles);
    } catch (PDOException $e) {
        // Log the error to see what's going wrong
        error_log($e->getMessage()); // Log the error for debugging
        echo json_encode(['error' => 'Database query failed']);
    }
} else {
    echo json_encode(['error' => 'No search term provided']);
}
?>
