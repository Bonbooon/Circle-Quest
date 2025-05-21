<?php
require_once '../../../config.php';
require_once DBCONNECT;
require_once ROOT_PATH . '/vendor/autoload.php';
require_once CONTROLLERS_PATH . '/NotifyMultipleUsers.php';
use Verot\Upload\Upload;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_title']) && $_SESSION['user']['is_admin']) {
    $title = $_POST['event_title'];
    $description = $_POST['event_description'];
    $prizes = $_POST['event_prizes'];
    $submission_deadline = $_POST['event_submission_deadline'];
    $presentation_date = $_POST['event_presentation_date'];
    $created_by = $_SESSION['user']['user_id'];
    $tag_ids = isset($_POST['tags']) ? $_POST['tags'] : [];

    $images = [];

    if (isset($_FILES['event_image']) && is_array($_FILES['event_image']['name'])) {
        foreach ($_FILES['event_image']['name'] as $index => $name) {
            if ($_FILES['event_image']['error'][$index] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['event_image']['name'][$index],
                    'type' => $_FILES['event_image']['type'][$index],
                    'tmp_name' => $_FILES['event_image']['tmp_name'][$index],
                    'error' => $_FILES['event_image']['error'][$index],
                    'size' => $_FILES['event_image']['size'][$index],
                ];

                $handle = new Upload($file);

                if ($handle->uploaded) {
                    $upload_dir = EVENT_IMAGE_PATH;
                    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

                    $handle->file_safe_name = true;
                    $handle->file_new_name_body = uniqid();
                    $handle->process($upload_dir);

                    if ($handle->processed) {
                        $image_type = $_POST['event_image_type'][$index] ?? 'main-visual';
                        $images[] = [
                            'path' => 'assets/events/' . $handle->file_dst_name,
                            'type' => $image_type
                        ];
                        $handle->clean();
                    } else {
                        $message = "画像アップロード失敗: " . $handle->error;
                    }
                }
            }
        }
    }

    try {
        $dbh->beginTransaction();
        
        $stmt = $dbh->prepare('INSERT INTO events (title, description, prizes, submission_deadline, presentation_date, created_by) VALUES (:title, :description, :prizes, :submission_deadline, :presentation_date, :created_by)');
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':prizes', $prizes);
        $stmt->bindValue(':submission_deadline', $submission_deadline);
        $stmt->bindValue(':presentation_date', $presentation_date);
        $stmt->bindValue(':created_by', $created_by);
        $stmt->execute();

        $event_id = $dbh->lastInsertId();

        foreach ($tag_ids as $tag_id) {
            $tag_stmt = $dbh->prepare('INSERT INTO event_tag_map (event_id, tag_id) VALUES (:event_id, :tag_id)');
            $tag_stmt->bindValue(':event_id', $event_id);
            $tag_stmt->bindValue(':tag_id', $tag_id);
            $tag_stmt->execute();
        }

        foreach ($images as $img) {
            $img_stmt = $dbh->prepare('INSERT INTO event_images (event_id, image_path, image_type) VALUES (:event_id, :image_path, :image_type)');
            $img_stmt->bindValue(':event_id', $event_id);
            $img_stmt->bindValue(':image_path', $img['path']);
            $img_stmt->bindValue(':image_type', $img['type']);
            $img_stmt->execute();
        }

        $dbh->commit();
        NotifyMultipleUsers(dbh: $dbh, userIds: [], notification: "イベント告知！:「{$title}」あなたも参加してみませんか？", eventId: $event_id, redirectTo: 'event');
        header(HEADER_HP_PATH);
        exit;
    } catch (Exception $e) {
        $dbh->rollback();
        $message = 'イベント作成に失敗しました: ' . $e->getMessage();
        error_log($e->getMessage());
    }
}
