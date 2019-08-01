<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="asset_content_response">
<?php
$asset = appxq\sdii\assets\FroalaEditorAsset::register($this);
$asset2 = \kartik\select2\Select2Asset::register($this);
$asset3 = trntv\yii\datetimepicker\DatetimepickerAsset::register($this);
?>
    <link href="<?= $asset->baseUrl ?>/css/plugins/char_counter.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/code_view.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/colors.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/draggable.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/emoticons.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/file.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/fullscreen.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/image.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/image_manager.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/line_breaker.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/quick_insert.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/table.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/video.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/help.min.css" rel="stylesheet">
    <link href="<?= $asset->baseUrl ?>/css/plugins/special_characters.min.css" rel="stylesheet">


    <link href="<?= $asset2->baseUrl ?>/css/select2.css" rel="stylesheet">
    <link href="<?= $asset2->baseUrl ?>/css/select2-addl.css" rel="stylesheet">
    <link href="<?= $asset2->baseUrl ?>/css/select2-krajee.css" rel="stylesheet">

    <link href="<?= $asset3->baseUrl ?>/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

</div>
