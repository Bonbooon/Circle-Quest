<?php
function Button($text, $color = '', $url = '', $textColor='', $extraAttribute='',$id='', $extraCSS='', $forceExtraCSS=false) {
    $bgClass = $color ? $color : "bg-themeYellow";
    $textClass = $textColor ? $textColor : "text-white";

    // Add the ! prefix to each class in $extraCSS
    if ($forceExtraCSS) {
        $extraCSS = implode(' ', array_map(function($class) {
            return "!" . $class; // Add ! before each class
        }, explode(' ', $extraCSS)));
    }

    // Combine the classes and add extraCSS at the end
    $class = "h-8 py-1 px-6 rounded-md hover:opacity-80 {$bgClass} {$textClass} {$extraCSS} ";
    
    $button = "
        <button class='{$class}' {$extraAttribute} id='{$id}'>
            {$text}
        </button>
    ";

    if ($url) {
        return "<a href='{$url}' class='inline-block'>{$button}</a>";
    }

    return $button;
}
