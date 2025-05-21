<?php
function updateExp(PDO $dbh, string $table, string $id_column, int $id_value, float $exp_to_add): void
{
    $stmt = $dbh->prepare("SELECT exp_point FROM $table WHERE $id_column = :id");
    $stmt->bindValue(':id', $id_value, PDO::PARAM_INT);
    $stmt->execute();
    $currentExp = $stmt->fetchColumn() ?? 0;

    $newExp = $currentExp + $exp_to_add;

    $stmt = $dbh->prepare("UPDATE $table SET exp_point = :exp WHERE $id_column = :id");
    $stmt->bindValue(':exp', $newExp);
    $stmt->bindValue(':id', $id_value, PDO::PARAM_INT);
    $stmt->execute();
}

function LevelUP(PDO $dbh, array $totalPoints, array $partaker_info): void
{
    $maxPoints = count($totalPoints) * 5;
    $totalEarned = array_sum($totalPoints);
    $exp = ($maxPoints > 0) ? ($totalEarned * 100 / $maxPoints) : (int)0;
    $exp = (int)$exp == 0 ? 1 : $exp;
    $shouldCommit = false;

    try {
        if (!$dbh->inTransaction()) {
            $dbh->beginTransaction();
            $shouldCommit = true;
        }

        // Update user exp_point
        updateExp($dbh, "users", "id", $partaker_info['user_id'], $exp);

        // Update circle member exp_point
        updateExp($dbh, "circle_members", "id", $partaker_info['circle_member_id'], $exp);

        // Recalculate total exp_point for the circle
        $stmt = $dbh->prepare("
            SELECT SUM(exp_point) AS total_exp_points
            FROM circle_members
            WHERE circle_id = :circle_id
        ");
        $stmt->bindValue(":circle_id", $partaker_info['circle_id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $circleExp = $result['total_exp_points'] ?? 0;

        // Update circle exp_point
        $stmt = $dbh->prepare("UPDATE circles SET exp_point = :exp WHERE id = :circle_id");
        $stmt->bindValue(":exp", $circleExp);
        $stmt->bindValue(":circle_id", $partaker_info['circle_id'], PDO::PARAM_INT);
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
?>
