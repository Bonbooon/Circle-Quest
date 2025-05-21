<?php
function RenderSubmissionButton(int $submission_id): string {
    return <<<HTML
        <button type="button" onclick="toggleModal('modal-{$submission_id}')" 
                class="w-40 h-10 bg-themeYellow text-black rounded-md hover:opacity-80">
            提出物を見る
        </button>
    HTML;
}
