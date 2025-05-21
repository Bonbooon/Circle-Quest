<?php
function UserDataResource(PDOStatement $user_stmt): ?array
{
    $rows = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        return null;
    }

    $user_data = $rows[0];
    $user_data['circles'] = [];

    foreach ($rows as $row) {
        if (!empty($row['circle_name'])) { 
            $user_data['circles'][] = [
                'circle_id' => $row['circle_id'],
                'circle_name' => $row['circle_name'],
                'circle_member_id' => $row['circle_member_id'],
                'circle_type' => $row['circle_type'],
                'circle_level' => $row['circle_level'],
                'circle_exp_point' => $row['circle_exp_point'],
                'circle_request_count' => $row['circle_request_count'],
                'circle_give_count' => $row['circle_give_count']
            ];
        }
    }
    return $user_data;
}
