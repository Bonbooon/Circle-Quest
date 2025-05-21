<?php 
function ProfileImg($img = "", $path = "#", $extraCSS = '', $isUser = true) {
    $imgPath = "assets/img/profile/";
    $id = $isUser ? 'js-user-profile-img' : 'js-circle-profile-img';
    $userImage = $img !== '' ? $imgPath . $img : $imgPath . $_SESSION['user']['user_image'];
    $image = "<img src=\"$userImage\" class=\"$id profile-img $extraCSS\" alt=\"profile-img\">";

    if ($path !== '#') {
        return "<a href=\"$path\">$image</a>";
    }

    return $image;
}
?>
