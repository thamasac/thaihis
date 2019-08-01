<?php
use yii\helpers\Url;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="container-widget-display"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function () {
        var div_content = $('#container-widget-display');
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?= Url::to(['/thaihis/cotnainer-widget/container-content'
            , 'options' => isset($options) ? $options : null, 'reloadDiv' => isset($reloadDiv) ? $reloadDiv : null, 'modal' => isset($modal) ? $modal : null
            ,'visitid'=> isset($visitid)?$visitid:null,'visit_type'=>$visit_type,'target'=>$target]) ?>';
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });



</script>
<?php \richardfan\widget\JSRegister::end(); ?>
