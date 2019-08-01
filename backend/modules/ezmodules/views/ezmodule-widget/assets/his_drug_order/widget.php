
<?php
if (!isset($modal)) {
    $modal = 'modal-content-widget' . \appxq\sdii\utils\SDUtility::getMillisecTime();
    echo appxq\sdii\widgets\ModalForm::widget([
        'id' => $modal,
        'size' => 'modal-xxl',
    ]);
}

$visit_id = Yii::$app->request->get('visitid');
$target = Yii::$app->request->get('target');
$options['order_tran_status'] = Yii::$app->request->get('order_tran_status', '1');
$options['order_status'] = Yii::$app->request->get('order_status', '1');
$options['order_id'] = Yii::$app->request->get('order_id');
$options['ezm_id'] = Yii::$app->request->get('id');
$reloadDiv = 'drug-content' . \appxq\sdii\utils\SDUtility::getMillisecTime();

echo \backend\modules\pis\classes\DrugOrderBuilder::contentBuilding()
        ->target($target)
        ->visitid($visit_id)
        ->modal($modal)
        ->reloadDiv($reloadDiv)
        ->options($options)
        ->buildBox('/pis/pis-item-order/grid-order');
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $(function () {
      $('#<?= $modal ?>').on('hidden.bs.modal', function () {
        var url = $('#<?= $reloadDiv ?>').attr('data-url');
        getUiAjax(url, '<?= $reloadDiv ?>');
      });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>