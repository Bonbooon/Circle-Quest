<?php
require_once __DIR__ . '/../../config.php';
require_once DBCONNECT;
require_once VALIDATIONS_PATH . "/Date/isStrictValidDate.php";
require ROOT_PATH . '/vendor/autoload.php';

use Verot\Upload\Upload;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!is_string($_POST['email'])) {
    throw new Exception('メールアドレスは必須項目です。');
  } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    throw new Exception('正しいEメールアドレスを指定してください。');
  } elseif (!is_string($_POST['password'])) {
    throw new Exception('パスワードは必須項目です。');
  } elseif (!is_string($_POST['first_name'])) {
    throw new Exception('名前は必須項目です。');
  } elseif (!is_string($_POST['last_name'])) {
    throw new Exception('名前は必須項目です。');
  } elseif (!is_string($_POST['college'])) {
    throw new Exception('所属大学は必須項目です。');
  } elseif (!is_string($_POST['circle'])) {
    throw new Exception('所属サークルは必須項目です。');
  } elseif (!isStrictValidDate($_POST['date_of_birth'])) {
    throw new Exception('生年月日は必須項目です。');
  } elseif (!is_string($_POST['phone_number'])) {
    throw new Exception('電話番号は必須項目です。');
  } elseif (empty($_POST['line_link']) || !isValidURL($_POST['line_link'])) {
    throw new Exception('有効なLINE招待リンクを入力してください。');
  } elseif (!empty($_POST['instagram_link']) && !isValidURL($_POST['instagram_link'])) {
    throw new Exception('有効なInstagramリンクを入力してください。');
  } elseif (!empty($_POST['twitter_link']) && !isValidURL($_POST['twitter_link'])) {
    throw new Exception('有効なTwitterリンクを入力してください。');
  } else {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $name = $last_name . ' ' . $first_name;
    $college = $_POST['college'];
    $circle = $_POST['circle'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];
    $type = $_POST['type'];
    $line_link = $_POST['line_link'] ?? null;
    $instagram_link = $_POST['instagram_link'] ?? null;
    $twitter_link = $_POST['twitter_link'] ?? null;

    $image_path = 'assets/img/profile/';
    $image = "default-profile.jpeg";

    try {
      $dbh->beginTransaction();

      $file = $_FILES['image'];
      if ($file['name'] !== '') {
        $lang = 'ja_JP';

        $handle = new Upload($file, $lang);

        if (!$handle->uploaded) {
          throw new Exception($handle->error);
        }

        // ファイルサイズのバリデーション： 5MB
        $handle->file_max_size = '5120000';
        // ファイルの拡張子と MIMEタイプをチェック
        $handle->allowed = array('image/jpeg', 'image/png', 'image/gif');
        // PNGに変換して拡張子を統一
        $handle->image_convert = 'png';
        $handle->file_new_name_ext = 'png';
        // サイズ統一
        $handle->image_resize = true;
        $handle->image_x = 718;
        // アップロードディレクトリを指定して保存
        $handle->process(PROFILE_PATH . "/");
        if (!$handle->processed) {
          throw new Exception($handle->error);
        }
        $image = $handle->file_dst_name;
      }

      $user_stmt = $dbh->prepare('INSERT INTO users (name, email, password, date_of_birth, phone_number, college, `image`) 
                                  VALUES (:name, :email, :password, :date_of_birth, :phone_number, :college, :image)');
      $user_stmt->bindValue(':name', $name);
      $user_stmt->bindValue(':email', $email);
      $user_stmt->bindValue(':password', $password);
      $user_stmt->bindValue(':date_of_birth', $date_of_birth);
      $user_stmt->bindValue(':phone_number', $phone_number);
      $user_stmt->bindValue(':college', $college);
      $user_stmt->bindValue(':image', $image);

      if (!$user_stmt->execute()) {
        throw new Exception("Failed to insert into users table.");
      }

      $user_id = $dbh->lastInsertId();

      $user_social_stmt = $dbh->prepare("INSERT INTO user_socials (user_id,line_link,instagram_link,twitter_link) VALUES (:user_id, :line_link, :instagram_link, :twitter_link)");
      $user_social_stmt->bindValue(":user_id", $user_id);
      $user_social_stmt->bindValue(":line_link", $line_link);
      $user_social_stmt->bindValue(":instagram_link", $instagram_link);
      $user_social_stmt->bindValue(":twitter_link", $twitter_link);

      if (!$user_social_stmt->execute()) {
        throw new Exception("Failed to insert into user_socials table.");
      }

      $circle_stmt = $dbh->prepare('SELECT id from circles where name = :name');
      $circle_stmt->bindValue(':name', $circle);
      if ($circle_stmt->execute()) {
        $circle_id = $circle_stmt->fetchColumn();
      }

      if (!$circle_id) { // If no existing circle_id, insert a new circle
        $circle_stmt = $dbh->prepare('INSERT INTO circles (name, type) VALUES (:name, :type)');
        $circle_stmt->bindValue(':name', $circle);
        $circle_stmt->bindValue(':type', $type);
        if (!$circle_stmt->execute()) {
          throw new Exception("Failed to insert into circles table.");
        }
        $circle_id = (int) $dbh->lastInsertId(); // Ensure it's an integer
      }

      $circle_member_stmt = $dbh->prepare('INSERT INTO circle_members (user_id, circle_id) 
                                          VALUES (:user_id, :circle_id)');
      $circle_member_stmt->bindValue(':user_id', $user_id);
      $circle_member_stmt->bindValue(':circle_id', $circle_id);

      if (!$circle_member_stmt->execute()) {
        throw new Exception("Failed to insert into circle_members table.");
      }

      $circle_college_stmt = $dbh->prepare('SELECT college from circle_colleges where college = :college AND circle_id = :circle_id');
      $circle_college_stmt->bindValue(':college', $college);
      $circle_college_stmt->bindValue(':circle_id', $circle_id);
      $circle_college_stmt->execute();
      if ($circle_college_stmt->fetchColumn() === false) {
        $circle_college_stmt = $dbh->prepare('INSERT INTO circle_colleges (circle_id, college) 
                                            VALUES (:circle_id, :college)');
        $circle_college_stmt->bindValue(":circle_id", $circle_id);
        $circle_college_stmt->bindValue(":college", $college);

        if (!$circle_college_stmt->execute()) {
          throw new Exception("Failed to insert into circle_colleges table.");
        }
      }

      $dbh->commit();
      header('Location: ../login.php');
      exit;
    } catch (Exception $e) {
      $dbh->rollBack();
      $message = $e->getMessage();
      if ($image !== $image_path . "/default-profile.jpeg" && is_file($image)) {
        unlink(ROOT_PATH . '/' . $image);
      }
      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }
}

// URL validation function to allow only specific domains
function isValidURL($url)
{
  // Check if URL is well-formed
  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    return false;
  }

  // Extract the domain from the URL
  $parsed_url = parse_url($url);
  $hostname = $parsed_url['host'] ?? '';

  // Check if the hostname is in the allowed domains list
  $allowed_domains = ['x.com', 'instagram.com', 'line.me'];
  foreach ($allowed_domains as $domain) {
    if (strpos($hostname, $domain) !== false) {
      return true;
    }
  }
  return false;
}
