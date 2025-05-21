<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.1/dist/css/splide.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kosugi&family=Noto+Sans+JP:wght@100..900&family=Zen+Kaku+Gothic+New&family=Zen+Maru+Gothic&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kosugi&family=Noto+Sans+JP:wght@100..900&family=Zen+Kaku+Gothic+New&family=Zen+Maru+Gothic&display=swap" rel="stylesheet">
    <script src="<?= JS_PATH . "/level-control.js"; ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.1/dist/js/splide.min.js"></script>
    <title>CQ</title>
</head>

<body>
    <? include 'header.php'; ?>
    <div class="flex">
        <? include 'sidebar.php'; ?>
        <main class="w-[100vw] h-screen ml-72 mt-20 p-10">
            <?
            if (isset($content)) {
                echo $content;
            } else {
                echo "<p>Welcome to my website!</p>";
            }
            ?>
        </main>
    </div>
</body>

</html>
