<?php

function PaginateItems($items, $itemsPerPage) {
    $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $totalItems = count($items);
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Ensure current page is within valid range
    $currentPage = max(1, min($currentPage, $totalPages));

    // Get paginated Items
    $paginatedItems = array_slice($items, ($currentPage - 1) * $itemsPerPage, $itemsPerPage);

    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'paginatedItems' => $paginatedItems
    ];
}
