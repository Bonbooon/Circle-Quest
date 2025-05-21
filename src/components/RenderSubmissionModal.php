<?php
require_once COMPONENTS_PATH . '/RenderSubmissionFile.php';
function RenderSubmissionModal(int $submission_id, string $submission): string {
    if (is_null($submission)) return '';

    $submissionContent = RenderSubmissionFile($submission);

    $modalHTML = <<<HTML
        <div id="modal-{$submission_id}" 
            class="bg-black bg-opacity-50 p-4 hidden flex items-center justify-center top-0 left-0 h-full">
            <div class="max-w-[700px] bg-white p-6 rounded-lg shadow-lg relative">
                <button type="button" onclick="toggleModal('modal-{$submission_id}')" 
                        class="absolute top-0 left-2 text-gray-600 hover:text-black text-xl">&times;</button>
                {$submissionContent}
            </div>
        </div>
    HTML;

    return $modalHTML;
}
