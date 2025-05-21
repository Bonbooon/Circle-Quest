<?php
function UserData(PDO $dbh, ?string $email, ?string $user_id=null) {
    require_once ROOT_PATH . '/Resources/UserDataResource.php';
    $user_stmt = $dbh->prepare('SELECT u.id as user_id, 
                    u.name as user_name, 
                    u.email as user_email, 
                    u.password as user_password, 
                    u.image as user_image,
                    u.date_of_birth as user_date_of_birth, 
                    u.college as user_college, 
                    u.level as user_level, 
                    u.exp_point as user_exp_point, 
                    u.request_count as user_request_count, 
                    u.give_count as user_give_count, 
                    u.is_admin, 
                    us.line_link,
                    us.instagram_link,
                    us.twitter_link,
                    cm.id as circle_member_id,
                    c.id as circle_id,
                    c.name as circle_name, 
                    c.type as circle_type, 
                    c.level as circle_level, 
                    c.exp_point as circle_exp_point, 
                    c.request_count as circle_request_count, 
                    c.give_count as circle_give_count
                FROM users u
                LEFT JOIN circle_members cm ON u.id = cm.user_id
                LEFT JOIN circles c ON cm.circle_id = c.id
                LEFT JOIN user_socials us ON us.user_id = u.id
                WHERE u.email = :email OR u.id = :user_id');
    $user_stmt->bindValue(':email', $email);
    $user_stmt->bindValue(':user_id', $user_id);
    if ($user_stmt->execute()) {
        return UserDataResource($user_stmt);
    } else {
        return null;
    };
};
