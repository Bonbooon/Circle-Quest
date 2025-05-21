<?php 
$allowedMimeTypes = [
    'video' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
    'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
    'image' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'],
    'pdf' => ['application/pdf'],
    'text' => ['text/plain', 'text/markdown']
];

function isValidFile($file_type) {
    global $allowedMimeTypes;

    foreach ($allowedMimeTypes as $category => $mimes) {
        if (in_array($file_type, $mimes)) {
            return true;
        }
    }

    return false;
}
