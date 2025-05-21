<?php
require_once ROOT_PATH . '/vendor/autoload.php';
use Verot\Upload\Upload;
/**
 * Set file upload limits based on MIME type.
 *
 * @param Upload $handle
 * @param string $file_mime
 */
function setFileLimits(Upload $handle, string $file_mime): void {
    // Set limits based on MIME type
    if (strpos($file_mime, 'video') !== false) {
        $handle->file_max_size = 200 * 1024 * 1024; // 200MB for videos
    } elseif (strpos($file_mime, 'image') !== false) {
        $handle->file_max_size = 10 * 1024 * 1024; // 10MB for images
        $handle->image_resize = true;
        $handle->image_x = 718;
    } elseif (strpos($file_mime, 'audio') !== false) {
        $handle->file_max_size = 30 * 1024 * 1024; // 30MB for audio
    } elseif (strpos($file_mime, 'application/pdf') !== false || strpos($file_mime, 'powerpoint') !== false) {
        $handle->file_max_size = 20 * 1024 * 1024; // 20MB for PDFs/Slides
    } elseif (strpos($file_mime, 'text') !== false) {
        $handle->file_max_size = 5 * 1024 * 1024; // 5MB for source code
    }

    // Disallowed file types
    $handle->forbidden = array('application/x-php', 'text/x-php', 'application/x-executable', 'application/javascript');
}
