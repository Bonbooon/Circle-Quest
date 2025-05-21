<?php
function checkIfUserIsMember($userID, $circleID) {
    global $dbh;
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM circle_members WHERE user_id = ? AND circle_id = ?");
    $stmt->execute([$userID, $circleID]);
    $result = $stmt->fetchColumn();
    
    return $result > 0;
}
?>
