<?php
function ToWhichSide(PDO $dbh, int $request_id, int $user_id ) {
    $stmt = $dbh->prepare('SELECT circle_member_id FROM requests WHERE id = :id');
    $stmt->bindValue(":id", $request_id);
    $stmt->execute();
    $requester_id = $stmt->fetch(PDO::FETCH_ASSOC)['circle_member_id'];
    if ($user_id === $requester_id) {
        $res = ['toWhom' => 'giver', 'from' => 'requester'];
    } else {
        $res = ['toWhom' => 'requester', 'from' => 'giver'];
    }
    return $res;
}
