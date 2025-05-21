<?php
function recalculateRanks(PDO $dbh): void
{
    $shouldCommit = false;

    try {
        if (!$dbh->inTransaction()) {
            $dbh->beginTransaction();
            $shouldCommit = true;
        }

        // Initialize user rank variable
        $stmt = $dbh->prepare("SET @rank := 0;");
        $stmt->execute();

        // Update user ranks by level and exp_point
        $stmt = $dbh->prepare("
            UPDATE users
            JOIN (
                SELECT id, @rank := @rank + 1 AS new_rank
                FROM users
                ORDER BY `level` DESC, exp_point DESC
            ) ranked_users ON users.id = ranked_users.id
            SET users.rank = ranked_users.new_rank;
        ");
        $stmt->execute();

        // Initialize circle rank variable
        $stmt = $dbh->prepare("SET @circle_rank := 0;");
        $stmt->execute();

        // Update circle ranks by level and exp_point
        $stmt = $dbh->prepare("
            UPDATE circles
            JOIN (
                SELECT id, @circle_rank := @circle_rank + 1 AS new_rank
                FROM circles
                ORDER BY `level` DESC, exp_point DESC
            ) ranked_circles ON circles.id = ranked_circles.id
            SET circles.rank = ranked_circles.new_rank;
        ");
        $stmt->execute();

        if ($shouldCommit) {
            $dbh->commit();
        }
    } catch (Exception $e) {
        if ($dbh->inTransaction() && $shouldCommit) {
            $dbh->rollBack();
        }
        throw $e;
    }
}
