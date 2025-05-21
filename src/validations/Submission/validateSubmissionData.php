<?php
/**
 * Validate request data before submission.
 *
 * @param array $data
 * @throws Exception
 */
function validateSubmissionData(array $data): void {
    if (empty($data['request'])) {
        throw new Exception('依頼内容を確認してください');
    }
    if (empty($data['due_date'])) {
        throw new Exception('納期を確認してください');
    }
    if (empty($data['pay'])) {
        throw new Exception('金額を確認してください');
    }
    if (empty($data['circle_member_id'])) {
        throw new Exception('代表するサークルを決めてください');
    }
}
