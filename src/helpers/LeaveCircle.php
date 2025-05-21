<?php
require_once HELPERS_PATH . '/CheckIfUserIsMember.php';

function LeaveCircle(PDO $dbh, $circle_id) {
    $user_id = $_SESSION['user']['user_id'];
    $college = $_SESSION['user']['user_college'];

    if (CheckIfUserIsMember($user_id, $circle_id)) {    
        $stmt = $dbh->prepare("DELETE FROM circle_members WHERE user_id = :user_id AND circle_id = :circle_id");
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':circle_id', $circle_id);
        $stmt->execute();

        $stmt = $dbh->prepare("SELECT COUNT(*) FROM circle_members cm
                                JOIN users u ON cm.user_id = u.id
                                WHERE cm.circle_id = :circle_id AND u.college = :college");
            $stmt->bindValue(':circle_id', $circle_id);
            $stmt->bindValue(':college', $college);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $stmt = $dbh->prepare("DELETE FROM circle_colleges WHERE circle_id = :circle_id AND college = :college");
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
