<?php
require_once "../config.php";
require_once DBCONNECT;
require_once COMPONENTS_PATH . "/Button.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Circle Quest</title>
  <link rel="stylesheet" href="../dist/output.css">
</head>

<body>
  <? include "../layout/header.php" ?>
  <? $img_path = '../assets/img/before/'; ?>

  <main class="mt-20">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
      <div class="px-8 py-6 flex flex-col items-center justify-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 bg-themeYellow pt-6 pb-6 pr-36 pl-36 rounded-2xl">Circle Questとは</h2>
        <h2 class="text-xl text-gray-700 bg-themeGray font-bold rounded-2xl pt-12 pb-12 pr-20 pl-20 mb-16">大学生サークル間で協力、交流をする機会を与えるアプリ</h2>
        <h2 class="text-2xl font-bold text-gray-800 mb-4 bg-themeYellow pt-6 pb-6 pr-36 pl-36 rounded-2xl">できること</h2>

        <div class="mb-6 bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-20 pl-20">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">①サークルで課題解決</h3>
          </div>
          <div class="flex items-center gap-6">
            <img src="<?= $img_path ?>before1.svg" alt="サークルで課題解決のイメージ" class="w-48 h-48 rounded mr-4">
            <p class="text-gray-700">・アイコンや自分のサークルのホームページなどの作成を他のサークルに依頼できる</p>
          </div>
        </div>

        <div class="mb-6 bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-20 pl-20">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">②サークル間で競う</h3>
          </div>
          <div class="flex items-center gap-6">
            <img src="<?= $img_path ?>before2.svg" alt="サークル間で競うのイメージ" class="w-48 h-48 rounded mr-4">
            <div class="flex flex-col justify-center gap-4">
              <p class="text-gray-700">・他サークルの依頼を受け、サークル間でコンペ形式で競う</p>
              <p class="text-gray-700">・依頼者に選ばれたサークルは作品を提出し、評価を受ける</p>
            </div>
          </div>
        </div>

        <div class="bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-20 pl-20 mb-16">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">③表彰</h3>
          </div>
          <div class="flex items-center gap-6">
            <img src="<?= $img_path ?>before2.5.svg" alt="サークルで課題解決のイメージ" class="w-48 h-48 rounded mr-4">
            <div class="flex flex-col justify-center gap-4">
              <p class="text-gray-700">・提出する度にポイントが入り、ポイント貯まるとレベルが上がる</p>
              <p class="text-gray-700">・ポイントが高い上位のサークルは「Lancer of the Year」で表彰され、Udemyなどのオンライン学習講座が無料で提供される。</p>
            </div>
          </div>
        </div>


        <h2 class="text-2xl font-bold text-gray-800 mb-4 bg-themeYellow pt-6 pb-6 pr-36 pl-36 rounded-2xl">使い方</h2>
        <div class="mb-6 bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-36 pl-36">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">①依頼する</h3>
          </div>
          <div class="flex items-center gap-9">
            <div class="text-center">
              <img src="<?= $img_path ?>before3.svg" alt="依頼するのイメージ" class="w-48 h-48 rounded mb-2">
              <p class="text-gray-700 text-sm">依頼をする</p>
            </div>
            <div class="mx-4 text-gray-500 text-2xl">→</div>
            <div class="text-center">
              <img src="<?= $img_path ?>before4.svg" alt="評価するのイメージ" class="w-48 h-48 rounded mb-2">
              <p class="text-gray-700 text-sm">評価する</p>
            </div>
          </div>
        </div>

        <div class="bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-36 pl-36 mb-16">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">②依頼を受注する</h3>
          </div>
          <div class="flex items-center gap-9">
            <div class="text-center">
              <img src="<?= $img_path ?>before5.svg" alt="依頼を見つけるのイメージ" class="w-48 h-48 rounded mb-2">
              <p class="text-gray-700 text-sm">依頼を見つける</p>
            </div>
            <div class="mx-4 text-gray-500 text-2xl">→</div>
            <div class="text-center">
              <img src="<?= $img_path ?>before6.svg" alt="納品のイメージ" class="w-48 h-48 rounded mb-2">
              <p class="text-gray-700 text-sm">納品</p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-4 bg-themeYellow pt-6 pb-6 pr-36 pl-36 rounded-2xl">強み</h2>
        <div class="mb-6 bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-20 pl-20">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">評価の可視化</h3>
          </div>
          <div class="flex items-center gap-6">
            <img src="<?= $img_path ?>before8.svg" alt="評価がもらえるのイメージ" class="w-48 h-48 rounded mr-4">
            <ul class="flex flex-col text-gray-700 gap-4">
              <li>・評価がもらえることで、スキルを磨くモチベーションに繋がる</li>
              <li>・他者からの評価の可視化→信頼の獲得→スキルの証明→ガクチカになったり、活動の幅や将来の選択肢が広がる</li>
            </ul>
          </div>
        </div>

        <div class="bg-themeGray font-bold rounded-2xl pt-4 pb-4 pr-20 pl-20 mb-16">
          <div class="flex flex-col justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">サークル内外での交流</h3>
          </div>
          <div class="flex items-center gap-6">
            <img src="<?= $img_path ?>before9.svg" alt="サークルで課題解決のイメージ" class="w-48 h-48 rounded mr-4">
            <div class="flex flex-col justify-center gap-4">
              <p class="text-gray-700">・コンペ式のため、サークル内での交流が活発になる</p>
              <p class="text-gray-700">・プロに評価してもらう対面のイベントで、他サークルとの交流を図ることができる</p>
            </div>
          </div>
        </div>
        <?= Button(text: "新規会員登録", url: "/auth/signup/index.php", extraCSS: "w-80"); ?>
      </div>
    </div>
  </main>
</body>

</html>
