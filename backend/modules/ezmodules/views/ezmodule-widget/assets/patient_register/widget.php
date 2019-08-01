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
$moduleId = \Yii::$app->request->get('id', '');
$widgetId = isset($widget_config['widget_id']) ? $widget_config['widget_id'] : appxq\sdii\utils\SDUtility::getMillisecTime();
//$cid = \Yii::$app->request->get('cid', '');
//$ezf_id = \Yii::$app->request->get('ezf_id', '1503378440057007100');
//$dataId = \Yii::$app->request->get('data_id', '1544066453089288200');
//$mockUrl = yii\helpers\Url::to(["/ezforms2/ezform-data/ezform?ezf_id={$ezf_id}&modal=modal-ezform-main&dataid={$dataId}&reloadDiv==&v=v_1536037289063854600&initdata=eyJwdF9jaWQiOiIzMDUxNTk4NjgzNzY5In0%3D"]);
//$mockUrl = "http://backend.ncrc.local/ezforms2/ezform-data/ezform?ezf_id=1503378440057007100&modal=modal-ezform-main&dataid=1544066453089288200";
//appxq\sdii\utils\VarDumper::dump($widget_config);

?>
<div id="<?= $widgetId?>"></div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
</script>
<?php \richardfan\widget\JSRegister::end(); ?>