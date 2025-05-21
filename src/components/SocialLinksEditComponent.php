<?php
function SocialLinksEditComponent()
{
    ob_start();
?>
    <div class="flex justify-center">
        <button id="edit-btn" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">SNSを編集</button>
    </div>
<?php
    return ob_get_clean();
}
