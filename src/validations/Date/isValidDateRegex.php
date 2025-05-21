<?php
function isValidDateRegex($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}
