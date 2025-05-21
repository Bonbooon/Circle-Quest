<?php
function DisplaySocialLinks(PDO $dbh, string $circle_id) {
    // Assuming you have the necessary social media links for the circle
    $socialLinks = [
        'line' => 'line_link',
        'instagram' => 'instagram_link',
        'twitter' => 'twitter_link',
    ];
    foreach ($socialLinks as $platform => $link_field) {
        $query = "SELECT $link_field FROM circle_socials WHERE circle_id = :circle_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindValue(':circle_id', $circle_id, PDO::PARAM_INT);
        $stmt->execute();
        $social_link = $stmt->fetchColumn();
        $img_path = 'assets/img';
        ?>
        <div id='social-links' class="flex items-center justify-center gap-2">
        <? if ($social_link) { ?>
            <a data-platform="<?= $platform ?>" href="<?= $social_link ?>" target="_blank">
                <img src="<?= $img_path . '/' . $platform  . '.svg' ?>" alt="<?= $platform ?>">
            </a>
        <? } ?>
        </div>
        <?
    }
}
