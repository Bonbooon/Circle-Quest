<?php
function RenderPaymentModalScript()
{
    ob_start();
?>
    <script>
        document.getElementById('paymentBtn').addEventListener('click', function() {
            Swal.fire({
                title: '支払い状況',
                text: "支払いは完了しましたか？",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'はい、支払い済みです',
                cancelButtonText: 'いいえ、未払いです'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.innerHTML = '支払い状況を確認 ✓';
                    document.getElementById('submitReview').disabled = false;
                    Swal.fire(
                        '確認完了',
                        '支払い済みとして記録されました',
                        'success'
                    );
                } else {
                    this.innerHTML = '支払い状況を確認 ✗';
                    document.getElementById('submitReview').disabled = true;
                    Swal.fire(
                        '確認完了',
                        '支払いが完了していないため、レビューを送信できません',
                        'warning'
                    );
                }
            });
        });
    </script>
<?php
    return ob_get_clean();
}
?>
