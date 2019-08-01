<?php
// start widget builder

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

echo \backend\modules\thaihis\classes\MultipleGridBuilder::gridDisplay()
        ->options($options)->reloadDiv('content-multiple-grid')->modal('modal-multiple-grid')->buildGrid();
        
?>
