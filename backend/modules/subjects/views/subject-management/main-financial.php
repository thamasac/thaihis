<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($maintab) || $maintab == '')
    $maintab = 1;
if (!isset($subtab) || $subtab == '')
    $subtab = 1;

$href = $_SERVER['HTTP_ORIGIN'];
$href .= "/ezmodules/ezmodule/view?id=" . $module_id;


$urlonload = "";
$view = 'group-financial';
if ($maintab == '1') {
    $urlonload = '/subjects/clinical-trial/index';
} elseif ($maintab == '2') {
    $view = 'study-payment';
    $urlonload = '/subjects/subject-management/sub-financial';
} elseif ($maintab == '3') {
    $view = 'group-financial';
    $urlonload = '/subjects/subject-management/sub-financial';
} elseif ($maintab == '4') {
    $view = 'main-subject-payment';
    $urlonload = '/subjects/subject-management/sub-financial';
} elseif ($maintab == '5') {
    $urlonload = '/subjects/reports/report';
}

$items = [];
if ($status == 'wizard') {
    $items = [
        [
            'label' => 'Clinical Trial Agreement',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'clinical-trial-agreement'],
            'active' => $maintab == 1 ? true : false,
        ],
        [
            'label' => '<span style="color:red;font-size:20px;">*</span> Payment and cost',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'payment-and-cost'],
            'active' => $maintab == 2 ? true : false,
        ],
        [
            'label' => 'Report',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'financial-report'],
            'active' => $maintab == 3 ? true : false,
        ]
    ];
} else {


    $items = [
        [
            'label' => 'Clinical Trial Agreement',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'clinical-trial-agreement'],
            'active' => $maintab == 1 ? true : false,
        ],
        [
            'label' => 'Revenue',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'revenue'],
            'active' => $maintab == 2 ? true : false,
        ],
        [
            'label' => 'Cost Allocations',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'payment-and-cost'],
            'active' => $maintab == 3 ? true : false,
        ],
        [
            'label' => 'Payment',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'subject-payment'],
            'active' => $maintab == 4 ? true : false,
        ],
        [
            'label' => 'Report',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'financial-report'],
            'active' => $maintab == 5 ? true : false,
        ],
    ];
    $view = "";
    $url = "";
    if ($maintab == 1) {
        $url = Url::to(['/subjects/clinical-trial/index',
                    'reloadDiv' => $reloadDiv,
                    'schedule_id' => $schedule_id,
                    'widget_id' => $widget_id,
                    'options' => $options,
                    'module_id' => $module_id,
                    'subtab' => $subtab,
                    'maintab'=>$maintab,
                    'user_create' => $user_create,
                    'user_update' => $user_update]);
    } elseif ($maintab == 5) {
        $url = Url::to(['/subjects/reports/report',
                    'reloadDiv' => $reloadDiv,
                    'schedule_id' => $schedule_id,
                    'widget_id' => $widget_id,
                    'options' => $options,
                    'module_id' => $module_id,
                    'view' => '_report',
                    'maintab'=>$maintab,
                    'user_create' => $user_create,
                    'user_update' => $user_update]);
    } else {
        if ($maintab == 2)
            $view = 'study-payment';
        elseif ($maintab == 3)
            $view = 'group-financial';
        elseif ($maintab == 4)
            $view = 'main-subject-payment';

        $url = Url::to(['/subjects/subject-management/sub-financial',
                    'reloadDiv' => $reloadDiv,
                    'schedule_id' => $schedule_id,
                    'widget_id' => $widget_id,
                    'subtab' => $subtab,
                    'options' => $options,
                    'status' => $status,
                    'module_id' => $module_id,
                    'view' => $view,
                    'maintab'=>$maintab,
                    'user_create' => $user_create,
                    'user_update' => $user_update]);
    }
}
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
<div id="show-data">

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
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=$url?>';
        $.get(url, function (result) {
            div.empty();
            div.html(result);
        });
        $.get(url, function (result) {
            div.empty();
            div.html(result);
        });
    });
    $('#payment-and-cost').click(function () {
        $(document).find('.daterangepicker').remove();
        window.history.replaceState({}, "Payment And Cost", '<?= $href ?>&maintab=3');
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/subject-management/sub-financial',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'subtab' => $subtab,
    'options' => $options,
    'status' => $status,
    'module_id' => $module_id,
    'view' => 'group-financial',
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url + '&maintab=3', function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#revenue').click(function () {
        $(document).find('.daterangepicker').remove();
        window.history.replaceState({}, "Revenue", '<?= $href ?>&maintab=2');
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/subject-management/sub-financial',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'subtab' => $subtab,
    'options' => $options,
    'status' => $status,
    'module_id' => $module_id,
    'view' => 'study-payment',
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url + '&maintab=2', function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#subject-payment').click(function () {
        $(document).find('.daterangepicker').remove();
        window.history.replaceState({}, "Subject Payment", '<?= $href ?>&maintab=4');
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/subject-management/sub-financial',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'subtab' => $subtab,
    'options' => $options,
    'status' => $status,
    'module_id' => $module_id,
    'view' => 'main-subject-payment',
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url + '&maintab=4', function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#financial-report').click(function () {
        $(document).find('.daterangepicker').remove();
        window.history.replaceState({}, "Financial Report", '<?= $href ?>&maintab=5');
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/reports/report',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'options' => $options,
    'module_id' => $module_id,
    'view' => '_report',
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url + '&maintab=5', function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#clinical-trial-agreement').click(function () {
        $(document).find('.daterangepicker').remove();
        window.history.replaceState({}, "Clinical Trial Agreement", '<?= $href ?>&maintab=1');
        var div = $('#show-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/clinical-trial/index',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'options' => $options,
    'module_id' => $module_id,
    'subtab' => $subtab,
    'user_create' => $user_create,
    'user_update' => $user_update])
?>';
        $.get(url + '&maintab=1', function (result) {
            div.empty();
            div.html(result);
        });
    });

    function getUiAjax(url, divid) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        }).fail(function (err) {
            err = JSON.parse(JSON.stringify(err))['responseText'];
            $('#' + divid).html(`<div class='alert alert-danger'>` + err + `</div>`);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
