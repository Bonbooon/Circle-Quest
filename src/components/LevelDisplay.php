<?php
function LevelDisplay(int $exp, int $level)
{
    $to_next_level = 5 * (($level) ** 2 + ($level));
    $current_level_exp = 5 * (($level - 1) ** 2 + ($level - 1));

    $exp_delta = $to_next_level - $current_level_exp;
    if ($exp_delta == 0) {
        $exp_ratio = 0;
    } else {
        $exp_pointer = $exp - $current_level_exp;
        $exp_ratio = $exp_pointer / $exp_delta;
    }

    $exp_to_level_up = $to_next_level - $exp;

    ob_start();
?>
    <div>
        <div class="flex justify-between">
            <h1>Level. <?= $level ?></h1>
            <p>次のレベルまであと<?= $exp_to_level_up ?>ポイント！</p>
        </div>
        <div class="w-full bg-themeYellow bg-opacity-50 relative" data-exp-ratio="<?= $exp_ratio; ?>" id="level-bar">
            <div class="text-right py-4 bg-themeYellow text-white w-4/12 " id="progress-bar">
                <p class="absolute top-1 right-1"><?= floor($exp_ratio * 100) ?>%</p>
            </div>
        </div>
        <script>
            const level_bar = document.getElementById('level-bar');
            const level_bar_width = level_bar.clientWidth;
            const progress_bar = document.getElementById('progress-bar');
            const progress = Math.round('<?= $exp_ratio; ?>' * level_bar_width);
            progress_bar.style.width = `${progress}px`;
            progress_bar.style.maxWidth = `${level_bar_width}px`;
        </script>
    </div>
<? return ob_get_clean();
}
