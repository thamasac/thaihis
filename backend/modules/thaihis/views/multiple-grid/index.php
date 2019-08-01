<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="subcontent-multiple-grid">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function () {
        var div_content = $('#subcontent-multiple-grid');
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?= Url::to(['/thaihis/multiple-grid/grid', 'options' => isset($options) ? $options : null, 'reloadDiv' => isset($reloadDiv) ? $reloadDiv : null, 'modal' => isset($modal) ? $modal : null]) ?>';
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    function getUiAjax(url, reloadDiv) {
        var div = $('#' + reloadDiv);
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, function (result) {
            div.html(result);
        });
    }


</script>
<?php \richardfan\widget\JSRegister::end(); ?>