<?php
function CompleteRequest(PDO $dbh, string $request_id): void {
    $stmt = $dbh->prepare("SELECT r.circle_member_id, cm.circle_id
                           FROM requests r
                           JOIN circle_members cm ON cm.id = r.circle_member_id
                           WHERE r.id = :request_id");
    $stmt->bindValue("request_id", $request_id);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        throw new Exception("Requester information could not be found for request ID: $request_id");
    }

    $requester_user_id = $request['circle_member_id'];
    $requester_circle_id = $request['circle_id'];
    
    $stmt = $dbh->prepare("UPDATE users SET request_count = request_count + 1 WHERE id = :requester_user_id");
    $stmt->bindValue("requester_user_id", $requester_user_id);
    $stmt->execute();
    
    $stmt = $dbh->prepare("UPDATE circles SET request_count = request_count + 1 WHERE id = :requester_circle_id");
    $stmt->bindValue("requester_circle_id", $requester_circle_id);
    $stmt->execute();
    
    $stmt = $dbh->prepare("SELECT s.circle_member_id, cm.circle_id
                           FROM submissions s
                           JOIN circle_members cm ON cm.id = s.circle_member_id
                           WHERE s.request_id = :request_id AND s.status = 'selected' LIMIT 1"); 
    $stmt->bindValue("request_id", $request_id);
    $stmt->execute();
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        throw new Exception("Giver information could not be found for request ID: $request_id");
    }

    $giver_user_id = $submission['circle_member_id'];
    $giver_circle_id = $submission['circle_id'];
    
    $stmt = $dbh->prepare("UPDATE users SET give_count = give_count + 1 WHERE id = :giver_user_id");
    $stmt->bindValue("giver_user_id", $giver_user_id);
    $stmt->execute();
    
    $stmt = $dbh->prepare("UPDATE circles SET give_count = give_count + 1 WHERE id = :giver_circle_id");
    $stmt->bindValue("giver_circle_id", $giver_circle_id);
    $stmt->execute();
    
    $stmt = $dbh->prepare("UPDATE requests SET is_completed = 1 WHERE id = :request_id");
    $stmt->bindValue("request_id", $request_id);
    $stmt->execute();
}
