<?php
require_once COMPONENTS_PATH . '/GetFileElement.php';
function RenderSubmissionFile($submissionFile) {
    // Get file extension
    $fileExtension = pathinfo($submissionFile, PATHINFO_EXTENSION);
    $filePath = "assets/submissions/" . $submissionFile;

    // File type handling based on file extension
    $fileHandlers = [
        'video' => ['mp4', 'mov', 'avi'],
        'audio' => ['mp3', 'wav', 'ogg'],
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'pdf' => ['pdf'],
        'text' => ['txt', 'docx', 'md','html','css','json','py','rb','java','c','cpp','go','rs','sql']
    ];

    // Check the file extension and return the corresponding HTML
    foreach ($fileHandlers as $type => $extensions) {
        if (in_array($fileExtension, $extensions)) {
            return GetFileElement($type, $filePath);
        }
    }

    return '<p>Unsupported file type.</p>';  // If the file type is unsupported
}
