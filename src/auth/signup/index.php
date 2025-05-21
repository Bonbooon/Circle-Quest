<?php
require_once __DIR__ . '/action.php';
require_once __DIR__ . '/../../config.php';
require_once COMPONENTS_PATH . "/button.php";
require_once COMPONENTS_PATH . '/ShowMessage.php';

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新規登録</title>
  <link rel="stylesheet" href="../../dist/output.css">
</head>

<body>
  <div class="p-10 flex items-center justify-center">
    <div class="w-fit flex justify-center items-center flex-col gap-12">
      <div class="text-center px-4 border-b-2 border-themeYellow">
        <h1 class="">新規会員登録</h1>
        <a href="../login.php" class="text-sm text-themeYellow">アカウントをお持ちですか？</a>
        <?= ShowMessage($message); ?>
      </div>
      <form method="post" action="" class="w-fit mb-5 flex flex-col items-center gap-8" enctype="multipart/form-data">
        <div class="flex flex-col gap-2">
          <fieldset class="signup-fieldset">
            <label for="name[]" class="">名前</label>
            <div class="flex w-80">
              <input type="text" name="last_name" id="email" class="border p-2 w-full" placeholder="姓" required>
              <input type="text" name="first_name" id="email" class="border p-2 w-full" placeholder="名" required>
            </div>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="date_of_birth">生年月日</label>
            <input type="date" name="date_of_birth" id="date_of_birth" class="border p-2 w-80 " required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="college">所属大学名</label>
            <input type="text" name="college" id="college" class="border p-2 w-80 " placeholder="所属大学名" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="circle">所属サークル名</label>
            <input type="text" name="circle" id="circle" class="border p-2 w-80 " placeholder="所属サークル名" required>
          </fieldset>
          <fieldset class="mb-3 flex justify-between items-center gap-8 w-[512px]">
            <label for="type">所属サークルのタイプ</label>
            <select name="type" id="type" class="w-80">
              <option value="intramural">同学</option>
              <option value="intercollegiate">インカレ</option>
            </select>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="email" class="">メールアドレス</label>
            <input type="email" name="email" id="email" class="border p-2 w-80 " placeholder="メールアドレス" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="line_link" class="">Lineの招待リンク</label>
            <input type="text" name="line_link" id="line_link" class="border p-2 w-80 " placeholder="Lineの招待リンク" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="instagram_link" class="">インスタグラム</label>
            <input type="text" name="instagram_link" id="instagram_link" class="border p-2 w-80 " placeholder="インスタグラム" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="twitter_link" class="">X(Twitter)</label>
            <input type="text" name="twitter_link" id="twitter_link" class="border p-2 w-80 " placeholder="X(Twitter)" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="image" class="">プロフィール画像</label>
            <input type="file" name="image" id="image" class="border p-2 w-80 ">
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="phone_number" class="">電話番号</label>
            <input type="tel" name="phone_number" id="phone_number" class="border p-2 w-80 " placeholder="電話番号" required>
          </fieldset>
          <fieldset class="signup-fieldset">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="border p-2 w-80 " placeholder="パスワード" required>
          </fieldset>
        </div>
        <?=Button(text: "登録"); ?>
      </form>
    </div>
  </div>
</body>

</html>
