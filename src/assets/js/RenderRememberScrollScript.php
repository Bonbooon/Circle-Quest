<?php
function RenderRememberScrollScript($scrollPosition) {
    ob_start();
    ?>
    <script>
        function rememberScroll(event) {
            const scrollY = window.scrollY;
            const targetHref = event.currentTarget.getAttribute('href');
            const url = new URL(targetHref, window.location.href);
            
            const params = new URLSearchParams(url.search);
            params.set('scroll', scrollY);
            url.search = params.toString();
            // console.log(url);
            event.currentTarget.href = url.toString();
        }
        
        window.onload = function() {
            const scrollPosition = <?= $scrollPosition ?>;
            if (scrollPosition > 0) {
                window.scrollTo(0, scrollPosition);
            }
        }
    </script>
    <?php
    return ob_get_clean();
}
