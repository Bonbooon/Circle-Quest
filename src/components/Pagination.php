<?php
function Pagination($currentPage, $totalPages, $baseUrl = '?page=search&p=') {
    if ($totalPages <= 1) return '';

    ob_start(); ?>
    <div class="flex justify-center gap-4 my-4">
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl . ($currentPage - 1) ?>"
                onclick="rememberScroll(event)"
                class="px-4 py-2 bg-themeYellow text-white rounded hover:opacity-80">
                前へ
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= $baseUrl . $i ?>"
                onclick="rememberScroll(event)"
                class="px-4 py-2 <?= $i === $currentPage ? 'bg-themeYellow text-white' : 'bg-gray-200' ?> rounded hover:opacity-80">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl . ($currentPage + 1) ?>"
                onclick="rememberScroll(event)"
                class="px-4 py-2 bg-themeYellow text-white rounded hover:opacity-80">
                次へ
            </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
?>
