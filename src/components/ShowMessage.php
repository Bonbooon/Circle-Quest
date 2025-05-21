<?php
function ShowMessage(string $message): string
{
    if ($message == '') {
        return '';
    }
    ob_start();
?>
    <p class="text-red-500 text-center mt-4"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<? return ob_get_clean();
}
