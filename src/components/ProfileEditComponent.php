<?php
function ProfileEditComponent(string $table, int $table_id)
{
    ob_start();
?>
    <div class="flex justify-center mt-4">
        <button id="edit-profile-image-btn" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">プロフィール画像を編集</button>
    </div>
    <form id="profile-image-form" method="POST" enctype="multipart/form-data" class="mt-4 hidden">
        <input type="hidden" name="table" value="<?= $table ?>">
        <input type="hidden" name="table_id" value="<?= $table_id ?>">
        <fieldset class="flex flex-col items-center gap-2">
            <label for="profile_image">新しいプロフィール画像をアップロード:</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*" class="border rounded-none">
        </fieldset>
        <div class="flex justify-center mt-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">更新</button>
        </div>
    </form>
<?php
    return ob_get_clean();
}
