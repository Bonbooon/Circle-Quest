<?php
require_once ROOT_PATH . '/vendor/autoload.php';
require_once VALIDATIONS_PATH . '/Submission/setFileLimits.php';
require_once COMPONENTS_PATH . '/isValidFile.php';
use Verot\Upload\Upload;
/**
 * Handle the file upload and return the file path.
 *
 * @param array $file
 * @return string
 * @throws Exception
 */
function handleFileUpload(array $file): string {
    $lang = 'ja_JP';
    $handle = new Upload($file, $lang);

    if (!$handle->uploaded) {
        throw new Exception($handle->error);
    }

    $handle->mime_check = true;
    $file_mime = $handle->file_src_mime;

    // Set allowed file types and size limits
    setFileLimits($handle, $file_mime);

    $handle->file_safe_name = true;
    
    // Generate unique filename using timestamp and random string
    $timestamp = time();
    $random = bin2hex(random_bytes(8));
    $extension = pathinfo($handle->file_src_name, PATHINFO_EXTENSION);
    $new_filename = $timestamp . '_' . $random . '.' . $extension;
    
    $handle->file_new_name_body = pathinfo($new_filename, PATHINFO_FILENAME);
    $file_path = SUBMISSION_PATH . '/' . $new_filename;
    
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    $handle->process(SUBMISSION_PATH . "/");

    if (!$handle->processed) {
        throw new Exception($handle->error);
    }

    return $handle->file_dst_name;
}
