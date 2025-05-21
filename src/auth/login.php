<?php
require_once __DIR__ . '/../config.php';
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Button.php";
require_once CONTROLLERS_PATH . '/UserData.php'; 
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';

session_start();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if ($email === '') {
      $message = 'メールアドレスは必須項目です。';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $message = '正しいEメールアドレスを指定してください。';
  } 
  elseif ($password === '') {
      $message = 'パスワードは必須項目です。';
  } 
  elseif ($user_data = UserData($dbh, $email)) {
      if (password_verify($password, $user_data["user_password"])) {
          $_SESSION['user'] = array_filter($user_data, fn($key) => $key !== "user_password", ARRAY_FILTER_USE_KEY);
          UpdateUserRequestAndEvents($dbh);
          header(HEADER_HP_PATH);
          exit();
      }
  } 
  $message = 'メールアドレスまたはパスワードが間違っています。';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link rel="stylesheet" href="../dist/output.css">
</head>
<body>
  <div class="p-10 h-screen flex items-center justify-center">
    <div class="w-fit flex justify-center items-center flex-col gap-20">
      <div class="text-center">
        <h1 class="mb-4">ログイン</h1>
        <a href="/auth/signup/index.php" class="px-6 mb-2 border-b-2 border-themeYellow text-sm text-themeYellow">
          新規会員登録はこちら
        </a>
        <?php if ($message !== '') : ?>
          <p class="text-red-500"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
      </div>
      <form method="post" action="" class="w-fit mb-5 flex flex-col items-center gap-28">
        <div class="flex flex-col gap-2">
          <fieldset class="w-80 mb-3 flex justify-between items-center gap-5">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" class="border p-2 max-w-lg" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </fieldset>
          <fieldset class="w-80 mb-3 flex justify-between items-center gap-5">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="border p-2 max-w-lg" autocomplete="off" required>
          </fieldset>
        </div>
        <?= Button(text: "ログイン"); ?>
      </form>
    </div>
  </div>
</body>
</html>
