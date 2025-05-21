<?php
function RetrieveRankings(PDO $dbh, int $userId) {
    $rankings = [
        'top_users' => [],
        'top_circles' => [],
        'user_rank' => null
    ];

    // Top 5 users
    $userQuery = "
        SELECT name, exp_point, `level`, `rank`
        FROM users
        ORDER BY exp_point DESC, name ASC
        LIMIT 5
    ";
    $stmt = $dbh->prepare($userQuery);
    $stmt->execute();
    $rankings['top_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 5 circles
    $circleQuery = "
        SELECT name, exp_point, `level`, `rank`
        FROM circles
        ORDER BY exp_point DESC, name ASC
        LIMIT 5
    ";
    $stmt = $dbh->prepare($circleQuery);
    $stmt->execute();
    $rankings['top_circles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get user's EXP and name
    $userInfoQuery = "SELECT exp_point, name FROM users WHERE id = :user_id";
    $stmt = $dbh->prepare($userInfoQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userInfo !== false) {
        $userExp = $userInfo['exp_point'];
        $userName = $userInfo['name'];

        // Get user's rank based on exp and name
        $rankQuery = "
            SELECT COUNT(*) + 1 AS user_position
            FROM users
            WHERE exp_point > :user_exp
            OR (exp_point = :user_exp AND name < :user_name)
        ";
        $stmt = $dbh->prepare($rankQuery);
        $stmt->bindParam(':user_exp', $userExp, PDO::PARAM_INT);
        $stmt->bindParam(':user_name', $userName, PDO::PARAM_STR);
        $stmt->execute();
        $rankings['user_rank'] = $stmt->fetchColumn();
    }

    return $rankings;
}
