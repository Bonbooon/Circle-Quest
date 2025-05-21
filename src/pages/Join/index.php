<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/Nothing.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/Pagination.php';
require_once COMPONENTS_PATH . '/CircleDisplay.php';
require_once COMPONENTS_PATH . '/ShowMessage.php';
require_once HELPERS_PATH . '/PaginateItems.php';
require_once HELPERS_PATH . '/FilterCircles.php';
require_once HELPERS_PATH . '/JoinCircle.php';
require_once HELPERS_PATH . '/LeaveCircle.php';
require_once CONTROLLERS_PATH . '/CircleList.php';
require_once CONTROLLERS_PATH . '/UpdateUserData.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';
require_once HELPERS_PATH . '/GetTeamInfo.php';
require_once JS_PATH . '/RenderRememberScrollScript.php';

$teaming = isset($_GET['teaming']) && $_GET['teaming'] == 1;
$_SESSION['teaming'] = $teaming;   
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;
$_SESSION['event_id'] = $event_id;
$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;
$_SESSION['team_id'] = $team_id;

$teamingMode =  isset($_SESSION['teaming']) && isset($_SESSION['event_id']) ? "&teaming={$_SESSION['teaming']}&id={$_SESSION['event_id']}" : '' ;

if ($_SESSION['team_id']) {
    $teamInfo = GetTeamInfo($dbh, $_SESSION['team_id']);
    $_SESSION['teamInfo'] = $teamInfo;
}

$circle_id_arr = [];
foreach ($_SESSION['user']['circles'] as $circle) {
    $circle_id_arr[] = $circle['circle_id'];
}

$circles = CircleList($dbh, $circle_id_arr, $teaming);

$filteredCircles = FilterCircles($circles);

$circlesPerPage = 5;
$pagination = PaginateItems($filteredCircles, $circlesPerPage);

$scrollPosition = isset($_GET['scroll']) ? (int)$_GET['scroll'] : 0;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['join']) || isset($_POST['leave'])) {
    try {
        if (isset($_POST['join'])) {
            if (!JoinCircle($dbh, $_POST['circle_id'])) {
                throw new Exception("Couldn't join the circle");
                unset($_POST['join']);
            }
        } elseif (isset($_POST['leave'])) {
            if (!LeaveCircle($dbh, $_POST['circle_id'])) {
                throw new Exception("Couldn't leave the circle");
                unset($_POST['leave']);
            }
        }
        UpdateUserData($dbh);
        UpdateUserRequestAndEvents($dbh);
    } catch(Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<?= ShowMessage($message); ?>

<? if ($circles) { ?>
    <div class="flex flex-col gap-10">
        <div class="flex justify-center items-start gap-6">
            <div class="flex justify-center items-start w-[512px]">
                <form method="POST" class="flex flex-col gap-7 w-full max-w-[600px] h-fit p-6 rounded-lg shadow-xl">
                    <input type="hidden" name="search" value="1">
                    <h1>サークルを探す</h1>
                    <fieldset class="flex flex-col gap-7">
                        <span class="before-search">
                            <input type="text"
                                name="searchTerm"
                                class="w-full bg-themeInput px-14 rounded-[20px] h-16"
                                placeholder="検索..." />
                        </span>
                        <div class="w-full flex justify-center gap-4">
                            <?= Button("検索", extraCSS: "w-28", extraAttribute: "type='submit'"); ?>
                            <a href="?page=join<?= $teamingMode ?>&scroll=0"
                                onclick="rememberScroll(event)"
                                class="w-28 h-8 py-1 px-6 rounded-md hover:opacity-80 bg-gray-500 text-white text-center">
                                リセット
                            </a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

        <?= CirclesDisplay(circles: $pagination['paginatedItems'], text: "サークル一覧", redirects_to: "profile/circle") ?>
        <? $path = $teaming && $event_id ? "?page=join&teaming={$_SESSION['teaming']}&id={$_SESSION['event_id']}&p=" : "?page=join&p="; ?>
        <?= Pagination($pagination['currentPage'], $pagination['totalPages'], $path); ?>
    </div>
    <?= RenderRememberScrollScript($scrollPosition); ?>
<? } else { ?>
    <?= Nothing("サークルがまだないようです") ?>
<? } ?>
