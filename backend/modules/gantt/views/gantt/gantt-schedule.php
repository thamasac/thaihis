<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$btnAdd = "";
$permission = EzfAuthFuncManage::auth()->accessBtn($module_id, 1);
if($permission)$btnAdd = "<a href='javascript:void(0)' onclick='addNewVisit()' class='btn btn-success btn-xs add-new-task'><i class='fa fa-plus'></i></a>";

?>

<div class="row">
    <div class="wrapper1"></div>
    <div id="gantt_here" style='width:100%; height:700px;'></div>
</div>
<?=

ModalForm::widget([
    'id' => 'modal-ezform-gantt2',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var alert = 0;
    $(function () {
        var mx = 0;
        var permission = '<?=$permission?>';
        console.log(permission);
        setTimeout(function () {
//
//            $('.gantt_task').on({
//                mousemove: function (e) {
//                    switch (e.which)
//                    {
//                        case 3:
//                            var mx2 = e.pageX - $('.gantt_hor_scroll').offsetLeft;
//                            if (mx)
//                                $('.gantt_hor_scroll').scrollLeft = this.sx + mx - mx2;
//                            break;
//
//                    }
//
//                },
//                mousedown: function (e) {
//                    switch (e.which)
//                    {
//                        case 3:
//                            this.sx = $('.gantt_hor_scroll').scrollLeft;
//                            mx = e.pageX - $('.gantt_hor_scroll').offsetLeft;
//                            break;
//                    }
//
//                }
//            });
//            $(document).on("mouseup", function () {
//                mx = 0;
//            });
        }, 3000);

        gantt.config.grid_width = 380;
        gantt.config.add_column = false;
        gantt.templates.grid_row_class = function (start_date, end_date, item) {
            if (item.status == 3)
                return "red";
            if (item.status == 2)
                return "yellow";
            if (item.status == 0)
                return "gray";
            if (item.status >= 3)
                return "green";
        };
        gantt.templates.task_row_class = function (start_date, end_date, item) {
            if (item.status == 3)
                return "red";
            if (item.status == 2)
                return "yellow";
            if (item.status == 0)
                return "gray";
            if (item.status >= 3)
                return "green";
        };
        gantt.config.columns = [
            {name: "text", label: "Task name", tree: true, width: 120},
            {name: "duration", label: "Day", align: "center", width: 80},
            {name: "start_date", label: "Start Date", align: "center", width: 80},
            {
                name: "progress", label: "Progress", width: 100, align: "center",
                template: function (item) {
                    if (item.progress >= 1)
                        return "Complete";
                    if (item.progress == 0)
                        return "Not started";
                    return Math.round(item.progress * 100) + "%";
                }
            },
            {
                name: "", label:"<?=$btnAdd?>" , width: 80, align: "center", template: function (obj) {
                    var store = gantt.getDatastore("resource");
                    if (obj.id != '') {
                        if(permission == 1)
                            return "<a href='javascript:void(0)' onclick='onDeleteTask(\"" + obj.id + "\")' class='btn btn-danger btn-xs btn-delete-form'><i class='fa fa-trash'></i></a>";
                        else
                            return"";
                    } else {
                        return "";
                    }
                }
            },
        ];


        gantt.templates.task_text = function (start, end, task) {
            var summ = task.progress1 + task.progress2 + task.progress3;
            return renderLabel(task.progress1, summ, "Earliest Date", "nearly_done") + renderLabel(task.progress2, summ, "Plan", "in_progress") + renderLabel(task.progress3, summ, "Latest Date", "idle");

        };
//    gantt.config.scale_unit = "month";
//    gantt.config.date_scale = "%F, %Y";
//
        gantt.config.scale_height = 50;
        gantt.config.scale_unit = "month";
        gantt.config.date_scale = "%F";

        gantt.config.subscales = [
            {unit: "<?= $unit ?>", step: <?= $step ?>, date: "%j, %D"}
        ];

        gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';
        gantt.init('gantt_here');
        gantt.load('/gantt/gantt/connector?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&group_id=<?= $group_id ?>');

        var dp = new gantt.dataProcessor('/gantt/gantt/connector?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&group_id=<?= $group_id ?>');
        dp.init(gantt);

    });

    function renderLabel(progress, sum, txt, css) {

        var relWidth = progress / sum * 100;

        var cssClass = "custom_progress ";
        cssClass += css;
        return "<div class='" + cssClass + "' style='width:" + relWidth + "%'>" + txt + "</div>";

    }

    function onUpdateLink(data) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '/gantt/gantt/update-link-schedule';
        $.post(url, {dataList: JSON.stringify(data), widget_id: widget_id, schedule_id: schedule_id}, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
        });
    }

    function onUpdateGantt(t, dat) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '/gantt/gantt/update-gantt';
        $.post(url, {options: JSON.stringify(dat)}, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
        });
    }

    function onModalAddGanttDialog(e, dat) {
        var require = dat.text.replace(' ', '_');
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => 'gantt-schedule',
    'group_id' => $group_id,
    'widget_id' => $schedule_id,
])
?>';
        $('#modal-ezform-gantt .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-gantt').modal('show')
                .find('.modal-content')
                .load(url + '&data_id=' + dat.id + '&parent=' + dat.parent + '&require=' + require);
    }

    function addNewVisit() {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => 'gantt-schedule',
    'group_id' => $group_id,
    'widget_id' => $schedule_id,
])
?>';

        $('#modal-ezform-gantt .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-gantt').modal('show')
                .find('.modal-content')
                .load(url);
    }


    function onDeleteTask(id) {
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/delete-visit',
    'reloadDiv' => 'display-schedule',
    'group_id' => $group_id,
    'widget_id' => $schedule_id,
])
?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.get(url, {data_id: id}
            ).done(function (result) {
                if (result.status == 'success') {
                    getReloadDiv($('#gantt-schedule').attr('data-url') + '&group_id=<?= $group_id ?>', 'gantt-schedule');
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
            });
        });
    }

    function onBeforeDeleteLink(e, txt) {
        if (alert == 1)
            return;
        if (e.source != '22222') {
            alert = 1;
                            
            yii.confirm(txt, function (confirm) {
                alert = 0;
                if (confirm) {
                    onDeleteLink(e);
                }
            },function(){
               alert = 0;
            });
        }
    }

    function onDeleteLink(dat) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '/gantt/gantt/delete-link';
        var proID = $('.project-selector').val();

        $.post(url, {dataList: JSON.stringify(dat), widget_id: widget_id, schedule_id: schedule_id}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>