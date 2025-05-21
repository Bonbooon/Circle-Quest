<?php
require_once HELPERS_PATH . '/CheckIfUserIsMember.php';

function JoinCircle(PDO $dbh, $circle_id) {
    $user_id = $_SESSION['user']['user_id'];
    $college = $_SESSION['user']['user_college'];

    if (!checkIfUserIsMember($user_id, $circle_id)) {
        // Insert into circle_members table
        $stmt = $dbh->prepare("INSERT INTO circle_members (user_id, circle_id) VALUES (:user_id, :circle_id)");
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':circle_id', $circle_id);
        $stmt->execute();

        // Check if the college already exists for the circle
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM circle_colleges WHERE circle_id = :circle_id AND college = :college");
        $stmt->bindValue(':circle_id', $circle_id);
        $stmt->bindValue(':college', $college);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Only insert if the college doesn't already exist for the circle
            $stmt = $dbh->prepare("INSERT INTO circle_colleges (circle_id, college) VALUES (:circle_id, :college)");
            $stmt->bindValue(':circle_id', $circle_id);
            $stmt->bindValue(':college', $college);
            $stmt->execute();
        }

        return true;
    } else {
        return false;
    }
}
?>
