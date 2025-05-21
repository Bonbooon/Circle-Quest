<?php
function FilterCircles(array $circles): array
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && $_POST['search'] == 1) {
        $searchTerm = $_POST['searchTerm'] ?? null;

        if (!empty($searchTerm)) {
            return array_filter($circles, function ($circle) use ($searchTerm) {
                return stripos($circle["name"], $searchTerm) !== false;
            });
        }
    }

    return $circles;
}
