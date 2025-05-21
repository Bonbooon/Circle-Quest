<?php
function CustomH3($text, $extraCSS='') {
    return "
        <h3 class='w-fit h-fit py-3 px-6 rounded-md text-white font-bold bg-themeYellow {$extraCSS}'>
            {$text}
        </h3>
    ";    
}
