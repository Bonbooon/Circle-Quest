<?php
function RenderCircleDisplayScript()
{
    ob_start();
?>
    <script>
        document.querySelectorAll('form[id^="join-form-"], form[id^="leave-form-"]').forEach(function(form) {
            console.log(form);
            form.addEventListener('submit', function(event) {
                event.preventDefault(); 
                var formData = new FormData(form);
                
                fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text()) 
                    .then(() => {
                        window.location.reload(); 
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    </script>
<?php
    return ob_get_clean();
}
