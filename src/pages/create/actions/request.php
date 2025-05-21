<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../config.php';
require_once DBCONNECT;
require_once CONTROLLERS_PATH . '/UserRequest.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once VALIDATIONS_PATH . '/Date/isStrictValidDate.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (!is_integer((int)$_POST['circle_member_id'])) {
        $message = 'サークルを選んでください';
    } elseif (!is_integer((int)$_POST['category_id'])) {
        $message = 'カテゴリーを選んでください';
    } elseif (!is_string($_POST['request'])) {
        $message = '依頼内容を書いてください';
    } elseif (!is_string($_POST['title'])) {
        $message = '依頼のタイトルを書いてください';
    } elseif (((int)$_POST['pay']) < 0) {
        $message = '提示する金額を記入してください';
    } elseif (!isStrictValidDate($_POST['due_date'])) {
        $message = '締切日を決めてください';
    } elseif (!is_string($_POST['comment'])) {
        $message = 'コメントを書いてください';
    } else {
        try {
            $dbh->beginTransaction();

            $stmt = $dbh->prepare('INSERT INTO requests (circle_member_id, category_id, title, request, pay, due_date, comment) VALUES (:circle_member_id, :category_id, :title, :request, :pay, :due_date, :comment)');
            $stmt->bindValue(':circle_member_id', $_POST['circle_member_id']);
            $stmt->bindValue(':category_id', $_POST['category_id']);
            $stmt->bindValue(':title', $_POST['title']);
            $stmt->bindValue(':request', $_POST['request']);
            $stmt->bindValue(':pay', $_POST['pay']);
            $stmt->bindValue(':due_date', $_POST['due_date']);
            $stmt->bindValue(':comment', $_POST['comment']);

            if ($stmt->execute()) {
                $dbh->commit();
                UpdateUserRequestAndEvents($dbh);
                header(HEADER_HP_PATH);
                exit;
            } else {
                throw new Exception("依頼を作成できませんでした");
            }
        } catch (Exception $e) {
            $dbh->rollback();
            $message = $e->getMessage();
            error_log($e->getMessage());
        }
    }
}
