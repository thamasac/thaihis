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
$visit_id = Yii::$app->request->get('visitid');
$target = Yii::$app->request->get('target');
$visit_type = Yii::$app->request->get('visit_type');

echo \yii\bootstrap\Modal::widget([
    'header' => '<h3 class="modal-title" id="itemModalLabel">Medical History </h3>',
    'footer' => \yii\helpers\Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
    'id' => 'modal-medical-history',
    'size' => 'modal-xxl',
]);

if ($visit_id) {
echo backend\modules\patient\classes\MedicalHisBuilder::contentBuilding()
        ->visitid($visit_id)
        ->target($target)
        ->modal('modal-medical-history')
        ->options($options)
        ->view("cpoe")
        ->buildMedical("/patient/medical-history/view");
}

?>
