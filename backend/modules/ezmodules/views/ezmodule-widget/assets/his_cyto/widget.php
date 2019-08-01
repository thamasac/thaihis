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
$params = Yii::$app->request->get();

echo backend\modules\thaihis\classes\ThaiHisHelper::uiOrderListCyto('grid-counter-cyto', $options);
?>
