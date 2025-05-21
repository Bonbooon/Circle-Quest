<?php
function RenderModalScriptOnce(): string {
    static $rendered = false;
    if ($rendered) return '';
    $rendered = true;

    return <<<HTML
        <script>
            function toggleModal(id) {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.classList.toggle('hidden');
                }
            }
        </script>
    HTML;
}
