<?php
function RenderCountDownScript($targetDate, $elementId)
{
    ob_start();
?>
    <script>
        function countdown(targetDate, elementId) {
            const target = new Date(targetDate);
            const timer = setInterval(() => {
                const now = new Date();
                const diff = target - now;

                if (diff <= 0) {
                    document.getElementById(elementId).innerText = "締切に達しました";
                    clearInterval(timer);
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hrs = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const mins = Math.floor((diff / (1000 * 60)) % 60);
                const secs = Math.floor((diff / 1000) % 60);

                document.getElementById(elementId).innerText =
                    `${days}日 ${hrs}時間 ${mins}分 ${secs}秒`;
            }, 1000);
        }

        countdown("<?= $targetDate ?>", "<?= $elementId ?>");
    </script>
<?php
    return ob_get_clean();
}
