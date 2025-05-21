<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Button.php";
require_once CONTROLLERS_PATH . '/Categories.php';
require_once 'script/index.php';
require_once 'forms/EventForm.php';
require_once 'forms/RequestForm.php';

$message = '';
$circles = $_SESSION['user']['circles'];
$categories = Categories($dbh);

?>
<div class="flex flex-col items-center gap-2">
    <h1 class="text-themeYellow font-bold" id="createTitle">依頼する<h1>
    <h2 class="text-xl">内容の入力<xh2>
</div>

<?php if ($message !== '') : ?>
    <p class="text-red-500"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($_SESSION['user']['is_admin']) { ?>
    <?= Button("イベントを作成", "bg-blue-500", "", "text-white", "toggle-btn", "toggleEventButton", "px-4 py-2 rounded", "text-center") ?>
<?php } ?>

<?= EventForm($dbh) ?>
<?= RequestForm($circles, $categories) ?>
<?= RenderToggleScript(); ?>
