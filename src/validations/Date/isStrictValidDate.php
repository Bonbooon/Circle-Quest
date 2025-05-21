<?php
require_once ROOT_PATH . '/validations/Date/isValidDateRegex.php';
require_once ROOT_PATH . '/validations/Date/isValidDate.php';
function isStrictValidDate($date, $format = 'Y-m-d') {
    if (!is_string($date) || trim($date) === '') return false;
    return isValidDateRegex($date) && isValidDate($date, $format);
}
