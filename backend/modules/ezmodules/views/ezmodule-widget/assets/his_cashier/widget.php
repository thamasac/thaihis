<?php

use \backend\modules\patient\classes\CashierBuilder;

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
?>
<?php

$modal = 'modal-content-widget' . $widget_config['widget_id'];
echo appxq\sdii\widgets\ModalForm::widget([
    'id' => $modal,
    'size' => 'modal-xxl',
]);
$visit_id = Yii::$app->request->get('visitid');
$target = Yii::$app->request->get('target');
//$options['params']['ezm_id'] = Yii::$app->request->get('id');
//$options['params']['cashier_status'] = Yii::$app->request->get('order_tran_cashier_status');
//$options['params']['target'] = $target;
//$options['params']['visit_id'] = $visit_id;
$options['params'] = Yii::$app->request->get();
if ($target) {
    echo CashierBuilder::contentBuilding()
            ->target($target)
            ->visitid($visit_id)
            ->modal($modal)
            ->configs(isset($options['configs']) ? $options['configs'] : null)
            ->items(isset($options['items']) ? $options['items'] : null)
            ->params($options['params'])
            ->buildBox('/patient/cashier2');
}
?>