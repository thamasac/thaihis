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
$visit_type = Yii::$app->request->get('visit_type');
$btnDisabled = isset($options['disabled_box']) ? $options['disabled_box'] : 0;
$pt_id = $target;
$ezf_id = $options['order_ezf_id'];
$modal = isset($modal) ? $modal : 'modal';

if ($visit_id) {
    $view = \appxq\sdii\utils\SDUtility::getMillisecTime();
    ?>
    <div class="tab-pane active" id="order-<?= $view ?>">       
      <?= backend\modules\thaihis\classes\ThaiHisHelper::uiGridOrder($ezf_id, $visit_id, 'view-order-' . $view, $btnDisabled, $options); ?>
    </div>
    <?php
    $this->registerJs(" 

 $('#$modal').on('hidden.bs.modal', function (e) {       
    var url = $('#view-order-'+'$view').attr('data-url');
    getUiAjax(url, 'view-order-'+'$view');
});

");
}
?>
