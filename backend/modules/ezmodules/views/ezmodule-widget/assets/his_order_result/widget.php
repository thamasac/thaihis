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
  'target' => $visit_id,
 */
$visit_id = Yii::$app->request->get('visitid');
if(!isset($target) || $target=='')
    $target = Yii::$app->request->get('target');

$visit_type = Yii::$app->request->get('visit_type');
$btnDisabled = isset($options['disabled_box']) ? $options['disabled_box'] : 0;
$pt_id = $target;

if ($visit_id) {
    $view = \appxq\sdii\utils\SDUtility::getMillisecTime();
    $pt_data = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
    echo \backend\modules\cpoe\classes\CpoeHelper::uiResultOrderCpoe($pt_id, $pt_data['pt_hn'], $visit_id, 'view-result-order');
}
?>
