<?php
function RenderEventPopUpSplideScript() {
    ob_start();
    ?>
        <script>
            new Splide('#image-slider', {
                type        : 'fade',
                heightRatio : 1, 
                autoplay    : true, 
                interval    : 3000,
                rewind      : true, 
                arrows      : false,
                pagination: true, 
            }).mount();
        </script>
    <? return ob_get_clean();
}
?>
