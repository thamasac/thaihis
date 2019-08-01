<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($subtab) || $subtab == '')
    $subtab = 1;
$viewload = "";
if ($subtab == '1') {
    $viewload = "information-cta";
} else if ($subtab == '2') {
    $viewload = "protocol-summary";
} else if ($subtab == '3') {
    $viewload = "budget-payment";
}

$href = "/ezmodules/ezmodule/view?id=" . $module_id . "&maintab=" . $maintab;
?>
<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'items' => [
        [
            'label' => 'Information of CTA',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'information_cta'],
            'active' => $subtab == 1 ? true : false,
        ],
        [
            'label' => 'Protocol Summary',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'protocol_summary'],
            'active' => $subtab == 2 ? true : false,
        ],
        [
            'label' => 'Budget And Payment Schedule',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'budget_payment'],
            'active' => $subtab == 3 ? true : false,
        ],
    ]
]);
?>

<div id="show-sub-data">

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

        var div = $('#show-sub-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/clinical-trial/clinical-trial-tab',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'module_id' => $module_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
    'view' => 'information-cta',
])
?>';
        $.get(url, function (result) {
            div.empty();
            div.html(result);
        })
    });
    $('#information_cta').click(function () {
        window.history.replaceState({}, "Financial Report", '<?= $href ?>&subtab=1');
        onLoadTab('information-cta', this);
    });
    $('#protocol_summary').click(function () {
        window.history.replaceState({}, "Financial Report", '<?= $href ?>&subtab=2');
        onLoadTab('protocol-summary', this);

    });
    $('#budget_payment').click(function () {
        window.history.replaceState({}, "Financial Report", '<?= $href ?>&subtab=3');
        onLoadTab('budget-payment', this);
    });

    function onLoadTab(view, e) {
        $(document).find('.daterangepicker').remove();
        $(e).parent().find('.active').removeClass('active');
        $(e).addClass('active');
        var div = $('#show-sub-data');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/clinical-trial/clinical-trial-tab',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'module_id' => $module_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
])
?>';
        $.get(url, {view: view}, function (result) {
            div.empty();
            div.html(result);
        });
    }

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