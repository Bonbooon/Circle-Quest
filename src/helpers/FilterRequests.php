<?php

function FilterRequests($requests) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $requests;
    }

    $searchTerm = $_POST['searchTerm'] ?? null;
    $selectedCategories = $_POST['categories'] ?? [];

    return array_filter($requests, function ($request) use ($searchTerm, $selectedCategories) {
        $matchesSearch = empty($searchTerm) ||
            stripos($request["request"], $searchTerm) !== false ||
            stripos($request["title"], $searchTerm) !== false;
        $matchesCategory = empty($selectedCategories) ||
            in_array($request["category"], $selectedCategories);

        return $matchesSearch && $matchesCategory;
    });
}
