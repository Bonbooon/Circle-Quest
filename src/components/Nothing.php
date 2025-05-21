<?php
function Nothing(string $title) {
    ob_start();
?>
    <div class="flex justify-center">
        <h2 class="p-20 bg-themeBeige"><?=$title?></h2>
    </div>
<? 
    return ob_get_clean();
}
