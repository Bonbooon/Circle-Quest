<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/Button.php';
require_once COMPONENTS_PATH . '/Nothing.php';
require_once COMPONENTS_PATH . '/EventPopup.php';
require_once COMPONENTS_PATH . '/ProfileImg.php';
require_once COMPONENTS_PATH . '/Pagination.php';
require_once COMPONENTS_PATH . '/RequestsDisplay.php';
require_once COMPONENTS_PATH . '/EventPopup.php';
require_once CONTROLLERS_PATH . '/Categories.php';
require_once CONTROLLERS_PATH . '/RequestList.php';
require_once HELPERS_PATH . '/PaginateItems.php';
require_once HELPERS_PATH . '/FilterRequests.php';
require_once JS_PATH . '/RenderRememberScrollScript.php';

// Fetch data
$requests = RequestList($dbh);
$categories = Categories($dbh);

// Filter requests
$filteredRequests = FilterRequests($requests);

// Pagination
$requestsPerPage = 5;
$pagination = PaginateItems($filteredRequests, $requestsPerPage);

// Get scroll position
$scrollPosition = isset($_GET['scroll']) ? (int)$_GET['scroll'] : 0;
?>

<?= !$_SESSION['user']['is_admin'] ? EventPopup() : null; ?>

<? if ($requests) { ?>
    <div class="flex flex-col gap-10">
        <div class="flex justify-center items-start gap-6">
            <div class="flex justify-center items-start w-[512px]">
                <form method="POST" class="flex flex-col gap-7 w-full max-w-[600px] h-fit p-6 rounded-lg shadow-xl">
                    <h1>依頼を探す</h1>
                    <div class="flex flex-col gap-7">
                        <span class="before-search">
                            <input type="text"
                                name="searchTerm"
                                class="w-full bg-themeInput px-14 rounded-[20px] h-16"
                                placeholder="検索..." />
                        </span>
                        <div class="flex flex-col gap-2 h-fit">
                            <label for="categories">カテゴリー別検索</label>
                            <div class="flex flex-col gap-2">
                                <?php foreach ($categories as $category) : ?>
                                    <fieldset>
                                        <input type="checkbox"
                                            name="categories[]"
                                            value="<?= htmlspecialchars($category['category']) ?>">
                                        <label><?= htmlspecialchars($category['category']) ?></label>
                                    </fieldset>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="w-full flex justify-center gap-4">
                            <?= Button("検索", extraCSS: "w-28"); ?>
                            <a href="?page=search&scroll=0"
                                onclick="rememberScroll(event)"
                                class="w-28 h-8 py-1 px-6 rounded-md hover:opacity-80 bg-gray-500 text-white text-center">
                                リセット
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?= RequestsDisplay(
            requests: $pagination['paginatedItems'],
            text: "依頼を出しているサークル一覧",
            redirects_to: 'submit'
        ) ?>

        <?= Pagination($pagination['currentPage'], $pagination['totalPages']); ?>
    </div>
    <?= RenderRememberScrollScript($scrollPosition); ?>
<? } else { ?>
    <?= Nothing("依頼がまだないようです") ?>
<? } ?>
