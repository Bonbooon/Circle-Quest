<?php 
  require_once DBCONNECT;
  require_once COMPONENTS_PATH . "/Button.php";
  require_once CONTROLLERS_PATH . "/GetUnreadNotificationCount.php";
  if (isset($_SESSION['user']['user_email'])) {
    $img_path= "assets/img";
    $user_id = $_SESSION['user']['user_id'];
    $email = $_SESSION['user']['user_email'];
    $level = $_SESSION['user']['user_level'];
    $unreadNotifications = getUnreadNotificationCount($dbh, $user_id);
  }
  $img_path = "../assets/img";
  ?>

<header class="h-20 w-full bg-themeBrown flex items-center justify-between p-4 fixed top-0">
  <div class="flex justify-between w-full">
    <img src="<?=$img_path . "/logo.svg"?>" alt="logo">
    <div class="flex items-center gap-12 font-bold">
      <?php if (isset($email)) { ?>
        <p><?= isset($level) ? "LV: " . $level : "LV: idk man" ?></p>
        <a href="?page=ranking"><img src="<?=$img_path . "/ranking.svg" ?>" alt="ranking"></a>
        <div class="relative">
          <a href="?page=notification"><img src="<?=$img_path . "/notification.svg" ?>" alt="notification"></a>
          <? if ($unreadNotifications !== '') { ?>
            <p class=" bg-red-600 rounded-[50%] leading-3 text-white absolute <?= $unreadNotifications == '9+' ? "p-[6px] text-xs bottom-[-24%] right-[-40%]" : "p-2 text-base bottom-[-28%] right-[-40%]" ?>"><?= $unreadNotifications ?></p>
            <?} ?>
        </div>
        <form method="POST" action="/auth/logout.php">
        <?= Button(text:"ログアウト" ); ?>
        </form>
      <?php } else { ?>
        <?= Button(text:"新規会員登録", url: "/auth/signup/index.php" ); ?>
        <?= Button(text:"ログイン", url: "/auth/login.php" ); ?>
      <?php } ?>
    </div>
  </div>
</header>
