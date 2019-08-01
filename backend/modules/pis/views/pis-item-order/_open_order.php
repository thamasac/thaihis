<?php
$options = backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($options);
$options['order_id'] = $order_id;
$urlOpenOrder = \yii\helpers\Url::to(['/pis/pis-item-order', 'visitid' => $visit_id
            , 'options' => \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options)]);
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    modalEzformMain('<?= $urlOpenOrder ?>', '#modal-<?= $ezfOrder_id ?>');

    $('#modal-<?= $ezfOrder_id ?>').on('hidden.bs.modal', function (e) {
      location.reload();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>