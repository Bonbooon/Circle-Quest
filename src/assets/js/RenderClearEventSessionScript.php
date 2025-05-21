<?php
function RenderClearEventSessionScript() {
    ob_start();
    ?>
<script>
window.addEventListener('beforeunload', function(event) {
    // Use fetch to make a POST request to clear the session
    fetch('/helpers/ClearEventSession.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'clear_session=true'
    })
    .then(response => response.json())
    .then(data => {
        // You can handle the response if needed
        console.log('Session cleared:', data);
    })
    .catch(error => {
        console.error('Error clearing session:', error);
    });
});
</script>
<?php
    return ob_get_clean();
}
