<?php
// start widget builder
use yii\helpers\Url;

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model, 
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */
echo \yii\bootstrap\Modal::widget([
    'header' => '<h3 class="modal-title" id="itemModalLabel">Medical History </h3>',
    'footer' => \yii\helpers\Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
    'id' => 'modal-multiple-grid',
    'size' => 'modal-xxl',
]);

?>
<div class="main-thaihis-report"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function(){
       var div_content = $('.main-thaihis-report');
       var url = '<?= Url::to(['/customer/main-report/index'])?>';
       div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
       $.get(url,{},function(result){
           div_content.html(result);
       });
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>