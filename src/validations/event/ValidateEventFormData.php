<?php
/**
 * Validate event form data before submission.
 *
 * @param array $data
 * @throws Exception
 */
function ValidateEventFormData(array $data): void {
    // Check if the event checkbox is checked
    if (empty($data['event'])) {
        throw new Exception('イベント内容を確認してください');
    }

    // Check if the submission deadline checkbox is checked
    if (empty($data['submission_deadline'])) {
        throw new Exception('応募デッドラインを確認してください');
    }

    // Check if the presentation date checkbox is checked
    if (empty($data['presentation_date'])) {
        throw new Exception('発表日を確認してください');
    }

    // Check if the prizes checkbox is checked
    if (empty($data['prizes'])) {
        throw new Exception('優勝商品を確認してください');
    }

    // Check if the circle member ID is selected (for event participation)
    if (empty($data['circle_member_id'])) {
        throw new Exception('代表するサークルを決めてください');
    }

    if (!isset($data['team'])) {
        throw new Exception('チームIDがPOSTされてません');
    }

    // If file upload is required, ensure the submission file exists
    if (empty($data['submission']) && empty($data['submission_type']) && $data['submission_type'] !== 'draft') {
        throw new Exception('提出ファイルを添付してください');
    }
}
