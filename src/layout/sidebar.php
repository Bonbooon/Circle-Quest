<?php require_once ROOT_PATH . "/components/ProfileImg.php";?>
<aside class="bg-themeBeige w-72 h-screen mt-20 px-6 pt-8 fixed z-10">
    <div class="flex flex-col gap-20">
        <div class="flex gap-12 w-fit items-center">
            <?= ProfileImg();?>
            <p class="text-center h-fit"><?= $_SESSION['user']["user_name"];?></p>
        </div>
        <div class="flex flex-col gap-8 w-full text-2xl pl-2">
            <a href="index.php" class="text-2xl">トップページ</a>
            
                <div class="flex flex-col">
                    <a href="?page=create" class="sb-redirect text-2xl">依頼する</a>
                </div>
                <div class="flex flex-col">
                    <a href="?page=create" class="sb-redirect text-2xl">指名する</a>
                </div>



                <div class="flex flex-col gap-4 text-center">
                    <a href="?page=search" class="sb-redirect text-2xl ">依頼を探す</a>
                </div>
            
        </div>
    </div>
</aside>
