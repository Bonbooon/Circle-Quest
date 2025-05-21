<?php
function Categories(PDO $dbh) {
    $stmt = $dbh->prepare('SELECT id,category from categories');
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
