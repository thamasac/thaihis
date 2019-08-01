<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$url = $_SERVER['HTTP_REFERER'];
if (!isset($subtab) || $subtab == '')
    $subtab = '2';
$href = "/ezmodules/ezmodule/view?id=" . $module_id . "&maintab=" . $maintab;
$element = 'budget-breakdown';
$viewload = "";
if ($subtab == '2') {
    $element = 'budget-breakdown';
    $viewload = "group-financial";
} elseif ($subtab == '3') {
    $element = 'payment-breakdown';
    $viewload = "payment-breakdown";
} elseif ($subtab == '4') {
    $element = 'subject-payment';
    $viewload = "main-subject-payment";
} elseif ($subtab == '1') {
    $element = 'study-payment-tracking';
    $viewload = "study-payment-tracking";
} elseif ($subtab == '5') {
    $element = 'invoice';
    $viewload = "invoice";
}

$item = [];
if ($status == 'wizard') {
    $item = [
        [
            'label' => 'Budget Breakdown',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'budget-breakdown'],
            'active' => true,
        ],
        [
            'label' => 'Payment Breakdown',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'payment-breakdown'],
        ],
    ];
} else {
    $item = [
        [
            'label' => 'Study Payment Tracking',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'study-payment-tracking'],
            'active' => $subtab == 1 ? true : false,
        ],
        [
            'label' => 'Budget Breakdown',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'budget-breakdown'],
            'active' => $subtab == 2 ? true : false,
        ],
        [
            'label' => 'Payment Breakdown',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'payment-breakdown'],
            'active' => $subtab == 3 ? true : false,
        ],
        [
            'label' => 'Subject Payment',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'subject-payment'],
            'active' => $subtab == 4 ? true : false,
        ],
        [
            'label' => 'Invoice',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'invoice'],
            'active' => $subtab == 5 ? true : false,
        ],
    ];
}
?>
<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'items' => $item
]);
?>
<div id="show-sub-data" data-url="<?=
Url::to(['/subjects/subject-management/sub-financial',
    'reloadDiv' => 'show-sub-data',
    'widget_id' => $widget_id,
    'module_id' => $module_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update])
?>">

</div>

<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-budget-form',
    'size' => 'modal-xl',
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        onLoadTab('<?= $viewload ?>', $('#<?= $element ?>'));
    });
    $('#subject-payment').click(function () {
        window.history.replaceState({}, "Subject Payment", '<?= $href ?>&subtab=4');
        onLoadTab('main-subject-payment', this);
    });
    $('#budget-breakdown').click(function () {
        window.history.replaceState({}, "Budget Breakdown", '<?= $href ?>&subtab=2');
        onLoadTab('group-financial', this);

    });
    $('#payment-breakdown').click(function () {
        window.history.replaceState({}, "Payment Breakdown", '<?= $href ?>&subtab=3');
        onLoadTab('payment-breakdown', this);
    });
    $('#study-payment-tracking').click(function () {
        window.history.replaceState({}, "Study Payment Tracking", '<?= $href ?>&subtab=1');
        onLoadTab('study-payment', this);
    });
    $('#invoice').click(function () {
        window.history.replaceState({}, "Invoice", '<?= $href ?>&subtab=5');
        onLoadTab('invoice', this);
    });
    function onLoadTab(view, e) {
        $(e).parent().find('.active').removeClass('active');
        $(e).addClass('active');
        var div = $('#show-sub-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/subject-management/sub-financial',
    'reloadDiv' => 'show-sub-data',
    'widget_id' => $widget_id,
    'module_id' => $module_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url, {view: view}, function (result) {
            div.empty();
            div.html(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>