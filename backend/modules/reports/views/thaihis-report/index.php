<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\tabs\TabsX;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$maintab = isset($maintab) ? $maintab : 1;
$urlOnload = '';
$itemCheck = [];
if (isset($options['report_send']) && $options['report_send'] == '1') {
    $itemCheck[] = 'report-send';
}
if (isset($options['checkup_config']) && $options['checkup_config'] == '1') {
    $itemCheck[] = 'checkup-config';
}
if (isset($options['patient_config']) && $options['patient_config'] == '1') {
    $itemCheck[] = 'patient-config';
}
if (isset($options['report2doc']) && $options['report2doc'] == '1') {
    $itemCheck[] = 'report-to-doc';
}
if (isset($options['report_opd']) && $options['report_opd'] == '1') {
    $itemCheck[] = 'report-opd';
}
if (isset($options['report_app_checkup']) && $options['report_app_checkup'] == '1') {
    $itemCheck[] = 'report-appoint-checkup';
}
$items = [];

if (is_array($item_report)) {
    foreach ($item_report as $key => $val) {
        if (in_array($val['id'], $itemCheck)) {
            if ($urlOnload == '')
                $urlOnload = $val['url'];
            $items[] = [
                'label' => $val['name'],
                'headerOptions' => ['style' => 'font-weight:bold', 'id' => $val['id']],
                'active' => $maintab == $key ? true : false,
            ];
        }
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
<div id="display-thaihis-report"> </div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function () {
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to([$urlOnload, 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#report-send').click(function () {
        $('.daterangepicker').remove();
        var screenWidth = $('.sdbox');
        screenWidth.css('margin-left', '15px');
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/report-checkup-send', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#checkup-config').click(function () {
        $('.daterangepicker').remove();
        var screenWidth = $('.sdbox');
        screenWidth.css('margin-left', '15px');
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/checkup-config', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#patient-config').click(function () {
        $('.daterangepicker').remove();
        var screenWidth = $('.sdbox');
        screenWidth.css('margin-left', '15px');
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/patent-config', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#report-to-doc').click(function () {
        $('.daterangepicker').remove();
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/report-checkup/report-to-doc', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#report-opd').click(function () {
        $('.daterangepicker').remove();
        var screenWidth = $('.sdbox');
        screenWidth.css('margin-left', '15px');
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/report-admin/report-opd-index', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

    $('#report-appoint-checkup').click(function () {
        $('.daterangepicker').remove();
        var screenWidth = $('.sdbox');
        screenWidth.css('margin-left', '15px');
        var div_content = $('#display-thaihis-report');
        var url = '<?= Url::to(['/reports/report-admin/report-appoint-checkup', 'taregt' => $target, 'module_id' => $module_id]) ?>';
        div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div_content.html(result);
        });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>