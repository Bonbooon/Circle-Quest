<?php
function EventForm(PDO $dbh)
{
    ob_start();
?>
    <form action="/pages/create/actions/event.php" id="eventForm" method="POST" enctype="multipart/form-data" class="hidden flex flex-col justify-center items-center gap-12 pb-5">
        <fieldset class="create-fieldset">
            <label for="event_title">イベントタイトル</label>
            <input type="text" name="event_title" class="bg-themeGray p-2" maxlength=255 required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="event_description">イベント内容</label>
            <textarea name="event_description" rows="4" class="textarea bg-themeGray p-2 rounded-xl" required></textarea>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="event_image">イベント画像（複数）</label>
            <input type="file" name="event_image[]" id="event_image" accept="image/*" multiple class="bg-themeGray p-2">
            <div id="imageTypesContainer" class="mt-2 flex flex-col gap-2"></div>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="event_prizes">賞品</label>
            <textarea name="event_prizes" rows="2" class="textarea bg-themeGray p-2 rounded-xl"></textarea>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="event_submission_deadline">提出締切</label>
            <input type="date" name="event_submission_deadline" class="bg-themeGray p-2" required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label for="event_presentation_date">発表日</label>
            <input type="date" name="event_presentation_date" class="bg-themeGray p-2" required>
        </fieldset>

        <fieldset class="create-fieldset">
            <label class="text-xl">タグ</label>
            <div class="flex flex-wrap gap-2">
                <?php
                $tag_stmt = $dbh->query('SELECT * FROM event_tags');
                $tags = $tag_stmt->fetchAll();
                foreach ($tags as $tag) : ?>
                    <label class="flex items-center gap-1">
                        <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"> <?= htmlspecialchars($tag['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <?= Button("イベントを作成") ?>
    </form>
<? return ob_get_clean();
} ?>
