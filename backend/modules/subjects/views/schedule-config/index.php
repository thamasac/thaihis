<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

$scheduleOptions = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($options['schedule_id']);
$procedureOptions = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($options['procedure_id']);
$items = [
    [
        'label' => 'Visit Schedule',
        'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'visit_schedule'],
        'active' => true,
    ],
    [
        'label' => 'Gantt Schedule',
        'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'gantt_schedule'],
    ],
    [
        'label' => 'Visit Procedure',
        'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'visit_procedure'],
    ]
];
?>
<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels' => false,
    //'enableStickyTabs' => true,
    'items' => $items,
]);
?>
<div id="display-schedule-config" data-url="">

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
        var display = $('#display-schedule-config');
        
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "<?=
Url::to([
    '/subjects/schedule-config/visit-config',
    'widget_id' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'module_id' => $module_id,
    'reloadDiv' => 'display-schedule-config',
]);
?>";
        display.attr('data-url',url);
        $.get(url, function (data) {
            display.html(data);
        });
    });

    $('#visit_schedule').click(function () {
        var display = $('#display-schedule-config');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "<?=
Url::to([
    '/subjects/schedule-config/visit-config',
    'widget_id' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'module_id' => $module_id,
    'reloadDiv' => 'display-schedule-config',
]);
?>";
        display.attr('data-url',url);
        $.get(url, function (data) {
            display.html(data);
        });
    });

    $('#gantt_schedule').click(function () {
        var display = $('#display-schedule-config');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "<?=
Url::to([
    '/gantt/gantt/index',
    'widget_id' => $widget_id,
    'schedule_id' => $options['schedule_id'],
    'user_create' => $user_create,
    'user_update' => $user_update,
    'module_id' => $module_id,
    'reloadDiv' => 'display-schedule-config',
    'skinName'=>'default',
    'step' => '1',
    'unit' => '1',
]);
?>";
        display.attr('data-url',url);
        $.get(url, function (data) {
            display.html(data);
        });
    });

    $('#visit_procedure').click(function () {
        var display = $('#display-schedule-config');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "<?=
Url::to([
    '/subjects/subject-management/main-procedure',
    'widget_id' => $widget_id,
    'schedule_id' => $options['schedule_id'],
    'widget_id' => $options['procedure_id'],
    'options'=> SDUtility::string2Array($procedureOptions['options']),
    'user_create' => $user_create,
    'user_update' => $user_update,
    'module_id' => $module_id,
    'reloadDiv' => 'display-schedule-config',
]);
?>";
        display.attr('data-url',url);
        $.get(url, function (data) {
            display.html(data);
        });
    });

    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>