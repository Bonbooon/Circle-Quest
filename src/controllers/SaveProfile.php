<?php

require_once '../config.php';
require_once DBCONNECT;
require_once ROOT_PATH . '/vendor/autoload.php';

use Verot\Upload\Upload;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $table_id = $_POST['table_id'];

    if (isset($_POST['line_link'], $_POST['instagram_link'], $_POST['twitter_link'])) {

        $twitter = $_POST['twitter_link'];
        $instagram = $_POST['instagram_link'];
        $line = $_POST['line_link'];

        $social = $table === 'users' ? 'user_socials' : 'circle_socials';
        $stmt = $dbh->prepare("SELECT id FROM {$social} WHERE id = ?");
        $stmt->execute([$table_id]);
        $social_id = $stmt->fetchColumn();
        $stmt = $dbh->prepare("UPDATE {$social} SET twitter_link = :twitter, instagram_link = :instagram, line_link = :line WHERE id = :id");
        $stmt->bindValue(':twitter', $twitter, PDO::PARAM_STR);
        $stmt->bindValue(':instagram', $instagram, PDO::PARAM_STR);
        $stmt->bindValue(':line', $line, PDO::PARAM_STR);
        $stmt->bindValue(':id', $social_id, PDO::PARAM_INT);
        $success = $stmt->execute();

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Social media links updated successfully', 'stmt' => $stmt]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update social media links']);
        }
    } elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {

        $file = $_FILES['profile_image'];

        $upload = new Upload($file);

        if ($upload->uploaded) {
            $upload_dir = ASSETS_PATH . '/img/profile/';

            $current_info = getInfo($dbh, $table_id, $table);
            $current_image = $current_info['image'];

            if ($current_image !== 'default-profile.jpeg' && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }

            $new_image_name = $table_id . '_' . time();
            $upload->file_new_name_body = $new_image_name;
            $upload->process($upload_dir);

            if ($upload->processed) {
                $new_image_name = $new_image_name . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

                $stmt = $dbh->prepare("UPDATE {$table} SET image = :image WHERE id = :id");
                $stmt->bindValue(':image', $new_image_name, PDO::PARAM_STR);
                $stmt->bindValue(':id', $table_id, PDO::PARAM_INT);
                $stmt->execute();

                if ($table === 'users') {
                    $_SESSION['user']["user_image"] = $new_image_name;
                }
                echo json_encode(['success' => true, 'message' => 'Profile image updated successfully', 'image' => $new_image_name]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not authenticated or invalid request']);
}

function getInfo($dbh, $id, $table)
{
    $stmt = $dbh->prepare("SELECT * FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
