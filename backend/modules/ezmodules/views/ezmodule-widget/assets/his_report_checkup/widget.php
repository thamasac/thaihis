<?php
use backend\modules\reports\classes\ReportCheckupBuilder;
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
$target = Yii::$app->request->get('target');
$visitid = Yii::$app->request->get('visitid');
$que_type = Yii::$app->request->get('que_type');
$action = Yii::$app->request->get('action');
$report_status = Yii::$app->request->get('report_status');
$page = Yii::$app->request->get('page');
$module_id = Yii::$app->request->get('id');
$params = [
    'que_type'=>$que_type,
    'action'=>$action,
    'report_status'=>$report_status,
    'page'=>$page
];
echo ReportCheckupBuilder::reportUI()->target($target)
        ->params($params)->visitid($visitid)->moduleId($module_id)->buildReport();
?>
