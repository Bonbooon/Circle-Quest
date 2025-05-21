<?php
function RetrieveReviewCriteria(PDO $dbh, string $from): array {
    $stmt = $dbh->prepare("SELECT * FROM review_criteria WHERE `for` = 'both' OR `for` = :from");
    $stmt->bindValue(':from', $from);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
