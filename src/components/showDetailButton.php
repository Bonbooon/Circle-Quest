<?php
require_once COMPONENTS_PATH . '/Button.php';
function ShowDetailButton (string $redirects_to='', ?string $request_id='') {
    $url = $redirects_to == '' ? '?page=home' : "?page={$redirects_to}" . ($request_id ? '&id=' . $request_id : '');
    return Button(
        text: "詳しく見る",
        color: "bg-white",
        textColor: "text-black",
        url: $url,
        extraCSS: "border"
    );
}
