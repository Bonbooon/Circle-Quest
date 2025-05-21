<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/CustomH3.php';
require_once CONTROLLERS_PATH . '/RetrieveRankings.php';

$user_id = $_SESSION['user']['user_id'];
$rankings = RetrieveRankings($dbh, $user_id);
$circle_rankings = $rankings['top_circles'];
$user_rankings = $rankings['top_users'];
$user_rank = $rankings['user_rank'];
?>

<div class="flex justify-center mb-4">
    <h1 class="text-yellow-400 font-bold">ランキング</h1>
</div>

<!-- Toggle Buttons -->
<div class="flex justify-center gap-4 mb-4">
    <button id="btn-users" onclick="toggleRanking('users')" class="ranking-toggle bg-white border-2 border-themeOrange text-themeOrange px-4 py-2 rounded-xl font-bold focus:bg-themeOrange focus:text-white">
        ユーザーランキング
    </button>
    <button id="btn-circles" onclick="toggleRanking('circles')" class="ranking-toggle bg-white border-2 border-themeOrange text-themeOrange px-4 py-2 rounded-xl font-bold focus:bg-themeOrange focus:text-white">
        サークルランキング
    </button>
</div>

<!-- Rankings Container -->
<div id="ranking-container" class="flex flex-col">

    <!-- User Rankings (default view) -->
    <div id="user-ranking">
        <?php foreach ($user_rankings as $index => $user): ?>
            <div class="flex m-2 items-center justify-center w-full">
                <?php if ($index < 3): ?>
                    <img class="w-20 h-20" src="/assets/img/rank/crown<?= $index + 1 ?>.svg" alt="">
                <?php else: ?>
                    <h2 class="w-20 h-20 text-2xl flex items-center justify-center"><?= $index + 1 ?></h2>
                <?php endif; ?>
                <h2 class="w-144 h-20 text-2xl text-center flex items-center justify-center px-40"><?= htmlspecialchars($user['name']) ?></h2>
                <h2 class="w-20 h-20 text-2xl text-center flex items-center justify-center">Lv.<?= $user['level'] ?></h2>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Circle Rankings -->
    <div id="circle-ranking" style="display: none;">
        <?php foreach ($circle_rankings as $index => $circle): ?>
            <div class="flex m-2 items-center justify-center w-full">
                <?php if ($index < 3): ?>
                    <img class="w-20 h-20" src="/assets/img/rank/crown<?= $index + 1 ?>.svg" alt="">
                <?php else: ?>
                    <h2 class="w-20 h-20 text-2xl flex items-center justify-center"><?= $index + 1 ?></h2>
                <?php endif; ?>
                <h2 class="w-144 h-20 text-2xl text-center flex items-center justify-center"><?= htmlspecialchars($circle['name']) ?></h2>
                <h2 class="w-20 h-20 text-2xl text-center flex items-center justify-center">Lv.<?= $circle['level'] ?></h2>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="w-full border-b-2 my-4"></div>

    <!-- My Rank -->
    <div class="flex items-center w-full">
        <h2 class="h-1 w-20 text-2xl mt-[16px]">あなた</h2>
    </div>
    <div class="flex m-2 items-center justify-center w-full">
        <h2 class="w-20 h-20 text-2xl text-center flex items-center justify-center"><?= $user_rank ?></h2>
        <h2 class="w-144 h-20 text-2xl text-center flex items-center justify-center px-40"><?= htmlspecialchars($_SESSION['user']['user_name']) ?></h2>
        <h2 class="w-20 h-20 text-2xl text-center flex items-center justify-center">Lv.<?= $_SESSION['user']['user_level'] ?? '-' ?></h2>
    </div>

</div>

<!-- Toggle Script -->
<script>
    function toggleRanking(type) {
        const userRanking = document.getElementById('user-ranking');
        const circleRanking = document.getElementById('circle-ranking');
        const btnUsers = document.getElementById('btn-users');
        const btnCircles = document.getElementById('btn-circles');

        if (type === 'users') {
            userRanking.style.display = 'block';
            circleRanking.style.display = 'none';

            btnUsers.classList.add('bg-blue-600');
            btnUsers.classList.remove('bg-blue-400');

            btnCircles.classList.remove('bg-green-600');
            btnCircles.classList.add('bg-green-500');
        } else {
            userRanking.style.display = 'none';
            circleRanking.style.display = 'block';

            btnCircles.classList.add('bg-green-600');
            btnCircles.classList.remove('bg-green-500');

            btnUsers.classList.remove('bg-blue-600');
            btnUsers.classList.add('bg-blue-400');
        }
    }

    window.addEventListener('DOMContentLoaded', () => toggleRanking('users'));
</script>
