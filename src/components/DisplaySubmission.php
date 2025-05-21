<?php
require_once JS_PATH . '/RenderModalScriptOnce.php';
require_once COMPONENTS_PATH . '/RenderSubmissionModal.php';
require_once COMPONENTS_PATH . '/RenderSubmissionButton.php';

function DisplaySubmission(?int $submission_id, ?string $submission): string {
    if (is_null($submission)) return '';

    $button = RenderSubmissionButton($submission_id);
    $modal = RenderSubmissionModal($submission_id, $submission);
    $script = RenderModalScriptOnce();

    return <<<HTML
        {$button}
        {$modal}
        {$script}
    HTML;
}
?>
