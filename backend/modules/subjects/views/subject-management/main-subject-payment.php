<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use kartik\tabs\TabsX;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'items' => [
        [
            'label' => 'Subject Payment',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'subject-payment-sub'],
            'active' => true,
        ],
        [
            'label' => 'All Subject Payment',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'all-subject-payment'],
        ],
        [
            'label' => 'PMS Payment',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'other-payment'],
        ],
        [
            'label' => 'Invoice',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'invoice'],
        ],
    ]
]);
?>
<div id="show-subject-payment">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var div_show = $('#show-subject-payment');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/subject-payment',
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'module_id' => $module_id,
    'widget_id' => $widget_id,
    'financial_id' => $financial_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
])
?>';

        $.get(url, function (result) {
            div_show.html(result);
        })
    });
    $('#subject-payment-sub').click(function () {
        var div_show = $('#show-subject-payment');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/subject-payment',
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'module_id' => $module_id,
    'widget_id' => $widget_id,
    'financial_id' => $financial_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
])
?>';

        $.get(url, function (result) {
            div_show.empty();
            div_show.html(result);
        })
    });
    $('#all-subject-payment').click(function () {
        var div_show = $('#show-subject-payment');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/group-subject-payment',
    'schedule_id' => $schedule_id,
    'module_id' => $module_id,
    'procedure_id' => $procedure_id,
    'widget_id' => $widget_id,
    'financial_id' => $financial_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
    'view' => 'group-subject-payment',
])
?>';

        $.get(url, function (result) {

            div_show.empty();
            div_show.html(result);
        })
    });

    $('#other-payment').click(function () {

        var div_show = $('#show-subject-payment');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/other-payment',
    'module_id' => $module_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
    'view' => 'otherpay-main',
])
?>';

        $.get(url, function (result) {
            div_show.empty();
            div_show.html(result);
        })
    });
    $('#invoice').click(function () {
        onLoadTab('invoice', this);
    });
    function onLoadTab(view, e) {
        $(e).parent().find('.active').removeClass('active');
        $(e).addClass('active');
        var div = $('#show-subject-payment');
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