<?php

$ezfOrderTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
$urlSelect = \yii\helpers\Url::to(['/pis/pis-item-order/package-show-items',
            'item_dataid' => $item_dataid//package_id
            , 'visit_id' => $visit_id
            , 'right_code' => $right_code
            , 'options' => $options
            , 'order_id' => $order_id, 'mode' => 'PACKAGE', 'action' => $action]);

$this->registerJs("
    //select show package
    modalEzformMain('$urlSelect', 'modal-<?= $ezfOrderTran_id ?>');
    ");
?>