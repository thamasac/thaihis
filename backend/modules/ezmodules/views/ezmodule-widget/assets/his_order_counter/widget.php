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
$visit_type = Yii::$app->request->get('que_type');
$pt_id = $target;
$order_status = Yii::$app->request->get('order_status');
$options['ezm_id'] = $module;
if ($visit_id) {
    $view = \appxq\sdii\utils\SDUtility::getMillisecTime();

    echo \backend\modules\thaihis\classes\ThaiHisHelper::uiOrderLists($visit_id, $visit_type, $order_status, $options, 'order-data-lists');
    $this->registerJs(" 

 $('#ttt').on('hidden.bs.modal', function (e) {       
    var url = $('#view-order-'+'$view').attr('data-url');
    getUiAjax(url, 'view-order-'+'$view');
});

");
}
?>

<br><br><br>