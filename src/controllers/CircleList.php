<?php
function CircleList(PDO $dbh, array $circle_id_arr, $teaming=false): array
{
    if ($teaming) {
        $exclude_ids = [/* list of IDs to exclude */];
        $circle_id_arr = array_diff($circle_id_arr, $exclude_ids);

        if (empty($circle_id_arr)) {
            return [];
        }
    }

    if (empty($circle_id_arr)) {
        $stmt = $dbh->prepare("SELECT id, name, image FROM circles");
        $stmt->execute();
    } else {
        $placeholders = implode(',', array_fill(0, count($circle_id_arr), '?'));

        $stmt = $dbh->prepare("
            SELECT id, name, image
            FROM circles
            " . ($teaming ? "WHERE id NOT IN ($placeholders)" : "") . "
            ORDER BY FIELD(id, $placeholders) DESC, FIELD(id, " . implode(',', $circle_id_arr) . ") DESC, id ASC
        ");

        // If teaming is true, we need to pass the circle_id_arr twice: once for NOT IN and once for ORDER BY FIELD
        $params = $teaming ? array_merge($circle_id_arr, $circle_id_arr) : $circle_id_arr;

        $stmt->execute($params);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
