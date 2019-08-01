<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Url;
use kartik\tabs\TabsX;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt.js?48', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/api.js?3', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_marker.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_smart_rendering.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_tooltip.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$href = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/ezmodules/ezmodule/view?id=" . $module_id;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-export',
    'size' => 'modal-sm',
]);
?>
<?php //\backend\modules\ezforms2\classes\EzfHelper::btn($options['group_ezf_id'])->label('<i class="fa fa-trash"></i>')->options(['class'=>'btn btn-danger btn-xs'])->buildBtnDelete($val['id'])                  ?>

<div id="display-schedule"  data-url="
     <?=
     Url::to(['/subjects/subject-management/schedule',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'options' => $options,
         'user_create' => $user_create,
         'user_update' => $user_update,
         'reloadDiv' => 'display-schedule',
     ]);
     ?>
     "></div>

     <?php
     \richardfan\widget\JSRegister::begin([
         //'key' => 'bootstrap-modal',
         'position' => \yii\web\View::POS_READY
     ]);

//$project_id = "1520742111042203500";
     ?>
<script>
    $(function () {
        $('#<?= $reloadDiv ?>').attr('data-urlgroup', '<?= $href ?>');
        var display = $('#display-schedule');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
     Url::to(['/subjects/schedule-config/grid-group',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'module_id' => $module_id,
         'options' => $options,
         'user_create' => $user_create,
         'user_update' => $user_update,
         'reloadDiv' => $reloadDiv,
     ]);
     ?>';
        $.get(url, {}, function (result) {
            display.html(result);
        });
    });

    $('.btn_export_excel').click(function () {
        var group_name = "";
        var group_id = "";
        var e = $('.tab-schedule-first-group');
        var ezf_id = "";
        var data_id = "";

        if ($(e).hasClass('active')) {
            ezf_id = $(e).attr('data-ezf_id');
            data_id = $(e).attr('data-data_id');
            group_name = $(e).attr('data-group_name');
            group_id = $(e).attr('data-group_id');
        }

        var url = '<?=
     Url::to(['/subjects/subject-management/export-first-schedule',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'module_id' => $module_id,
         'options' => $options,
         'user_create' => $user_create,
         'user_update' => $user_update,
         'reloadDiv' => $reloadDiv,
         'export' => true,
     ]);
     ?>'
        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            var data = JSON.parse(result);
<?= SDNoty::show('data.message', 'data.status') ?>;
            $('#modal-export .modal-content').html(data.html);
            $('#modal-export').modal('hide');
        });
    });

    $('#search_number').change(function () {
        onResearch();
    });
    $('#search_group').change(function () {
        onResearch();
    });
    $('#search_visit').change(function () {
        onResearch();
    });
    $('#date_consent').change(function () {
        onResearch();
    });
    $('#next_date').change(function () {
        onResearch();
    });
    $('#subject_status').change(function () {
        onResearch();
    });

    function onResearch() {
        var group_name = "";
        var group_id = "";
        var e = $('.tab-schedule-first-group');
        var ezf_id = "";
        var data_id = "";
        var number = "";
        var visit_id = "";
        var date_consent = "";
        var next_date = "";
        var subject_status = "";
        var view = "";

        $('.tab-schedule-group').each(function (i, e) {
            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
                group_name = $(e).attr('data-group_name');
                group_id = $(e).attr('data-group_id');
                number = $('#search_number').val();
                visit_id = $('#search_visit').val();
                date_consent = $('#date_consent').val();
                next_date = $('#next_date').val();
                subject_status = $('#subject_status').val();
                view = "schedule";
            }
        });

        if ($(e).hasClass('active')) {
            ezf_id = $(e).attr('data-ezf_id');
            data_id = $(e).attr('data-data_id');
            group_name = $(e).attr('data-group_name');
            group_id = $('#search_group').val();
            number = $('#search_number').val();
            visit_id = $('#search_visit').val();
            date_consent = $('#date_consent').val();
            next_date = $('#next_date').val();
            subject_status = $('#subject_status').val();
            view = "first";
        }
        if (data_id == '1') {
            $('#tab-schedule-edit').addClass('disabled');
            $('#tab-schedule-delete').addClass('disabled');
        }

        var display = $('#display-schedule');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "";
        if (view == "first") {
            url = '<?=
Url::to(['/subjects/subject-management/first-schedule',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
]);
?>';
        } else {
            url = display.attr('data-url');
        }
        $.get(url, {group_name: group_name, group_id: group_id, number: number, visit_id: visit_id, date_consent: date_consent, next_date: next_date, subject_status: subject_status}, function (result) {
            display.html(result);
        });
    }

    $('.tab-schedule-group').click(function () {
        $('#search_group').prop('disabled', true);
        $('#search_visit').prop('disabled', true);
        $('#start_date').prop('disabled', true);
        $('#end_date').prop('disabled', true);

        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id');

        if (data_id == '1') {
            $('#tab-schedule-edit').addClass('disabled');
            $('#tab-schedule-delete').addClass('disabled');
        } else {
            $('#tab-schedule-edit').removeClass('disabled');
            $('#tab-schedule-delete').removeClass('disabled');
        }

        $(document).find('.tab-schedule-group').removeClass('active')
        $(document).find('.tab-schedule-first-group').removeClass('active')
        $(this).addClass('active');

        var display = $('#display-schedule');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');

        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });

    $('.tab-schedule-first-group').click(function () {
        $('#search_group').prop('disabled', false);
        $('#search_visit').prop('disabled', false);
        $('#start_date').prop('disabled', false);
        $('#end_date').prop('disabled', false);

        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id');

        var url = '<?=
Url::to(['/subjects/subject-management/first-schedule',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
]);
?>';
        $(document).find('.tab-schedule-group').removeClass('active')
        $(this).addClass('active');
        var display = $('#display-schedule');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });

    $('#tab-schedule-add').click(function () {
        var ezf_id = "";
        var data_id = "";
        $('#modal-ezform-group').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        ezf_id = $('.tab-schedule-first-group').attr('data-ezf_id');
        data_id = $('.tab-schedule-first-group').attr('data-data_id');

        var url = "/ezforms2/ezform-data/ezform?";
        $('#modal-ezform-group').modal('show');
        $('#modal-ezform-group').find('.modal-content').load(url + 'ezf_id=' + ezf_id + '&modal=modal-ezform-group&v=v1&reloadDiv=<?= $reloadDiv ?>');
    });

    $('#tab-schedule-edit').click(function () {
        var ezf_id = "";
        var data_id = "";
        $('#modal-ezform-group').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('.tab-schedule-group').each(function (i, e) {
            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
            }
        });
        var url = "/ezforms2/ezform-data/ezform?";
        $('#modal-ezform-group').modal('show');
        $('#modal-ezform-group').find('.modal-content').load(url + 'ezf_id=' + ezf_id + '&dataid=' + data_id + '&modal=modal-ezform-group&reloadDiv=<?= $reloadDiv ?>');
    });

    $('#tab-schedule-delete').click(function () {
        var ezf_id = "";
        var data_id = "";

        $('.tab-schedule-group').each(function (i, e) {
            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
            }
        });
        bootbox.confirm({
            title: '<?= Yii::t('subjects', 'Confirm') ?>',
            message: '<?= Yii::t('subjects', 'Are you sure to delete item?') ?>',
            callback: function (result) {
                console.log(result);
                if (result) {
                    handleAction();
                }
            }
        });

        var handleAction = function () {
            var url = "/ezforms2/ezform-data/delete?ezf_id=" + ezf_id + "&dataid=" + data_id + '&reloadDiv=<?= $reloadDiv ?>';
            $.post(url, {ezf_id: ezf_id, dataid: data_id, reloadDiv: '<?= $reloadDiv ?>'}, function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
                }

            });
        }


    });

    function getReloadDiv(url, div) {
        $('#' + div).html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>