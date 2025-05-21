<?php
function RequestForm(array $circles, array $categories)
{
    ob_start();
?>
    <form action="/pages/create/actions/request.php" id='requestForm' method="POST" class="flex flex-col justify-center items-center gap-12 pb-5">
        <fieldset class="create-fieldset">
            <label for="request">1.依頼のタイトル</label>
            <input type="text" name="title" id="title" class="bg-themeGray p-2" maxlength=255 required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="request">2.依頼内容</label>
            <textarea name="request" id="request" rows="4" cols="50" class="textarea max-w-[512px] p-2 rounded-xl" required></textarea>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="circle_member_id">3.サークルを選択</label>
            <select name="circle_member_id" id="circle_member_id" class="bg-themeGray p-2" required>
                <? foreach ($circles as $circle) { ?>
                    <option value="<?= $circle['circle_member_id'] ?>"><?= $circle['circle_name'] ?></option>
                <? } ?>
            </select>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="category_id">4.カテゴリー</label>
            <select name="category_id" id="category_id" class="bg-themeGray p-2" required>
                <? foreach ($categories as $category) { ?>
                    <option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
                <? } ?>
            </select>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="pay">5.金額</label>
            <input type="number" name="pay" id="pay" class="bg-themeGray p-2" min="0" required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="due_date">6.期日</label>
            <input type="date" name="due_date" id="due_date" class="bg-themeGray p-2" required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="comment">7.コメント</label>
            <textarea role="textbox" name="comment" id="comment" rows="4" cols="50" class="textarea max-w-[512px] p-2 rounded-xl" contenteditable></textarea>
        </fieldset>
        <?= Button("作成") ?>
    </form>
<? return ob_get_clean();
} ?>
