
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;

$unit = "day";
$step = '1';
if (isset($scale_unit)) {
    if ($scale_unit == '0') {
        $unit = "day";
    } elseif ($scale_unit == '1') {
        $unit = "week";
    } elseif ($scale_unit == '2') {
        $unit = "month";
    }
}

if (isset($scale_step) && $scale_step > 0)
    $step = $scale_step;

$itemUnit[0]['id'] = '0';
$itemUnit[0]['unit_name'] = Yii::t('subjects', "Day");

$itemUnit[1]['id'] = '1';
$itemUnit[1]['unit_name'] = Yii::t('subjects', "Week");

$itemUnit[2]['id'] = '2';
$itemUnit[2]['unit_name'] = Yii::t('subjects', "Month");

$itemFilter[0]['id'] = '0';
$itemFilter[0]['filter_name'] = Yii::t('subjects', "All Task");

$itemFilter[1]['id'] = '1';
$itemFilter[1]['filter_name'] = Yii::t('subjects', "Completed Task");

$itemFilter[2]['id'] = '2';
$itemFilter[2]['filter_name'] = Yii::t('subjects', "Not Complete Task");

$itemFilter[3]['id'] = '3';
$itemFilter[3]['filter_name'] = Yii::t('subjects', "My Task");


$customCss = ".red .gantt_cell, .odd.red .gantt_cell, .red .gantt_task_cell, .odd.red .gantt_task_cell { background-color: #FDE0E0; }";
$customCss .= ".green .gantt_cell, .odd.green .gantt_cell, .green .gantt_task_cell, .odd.green .gantt_task_cell { background-color: #BEE4BE; }";
$customCss .= ".yellow .gantt_cell, .odd.yellow .gantt_cell, .yellow .gantt_task_cell, .odd.yellow .gantt_task_cell { background-color: #FBF5A8; } .gray .gantt_cell, .odd.gray .gantt_cell, .gray .gantt_task_cell, .odd.gray .gantt_task_cell { background-color: #E6E6E6; }";
$customCss .= ".high { border: 2px solid #d96c49; color: #d96c49; background: #d96c49; }";
$customCss .= ".high .gantt_task_progress { background: #db2536; }";
$customCss .= ".medium { border: 2px solid #34c461; color: #34c461; background: #34c461; }";
$customCss .= ".medium .gantt_task_progress { background: #23964d; }";
$customCss .= ".low { border: 2px solid #6ba8e3; color: #6ba8e3; background: #6ba8e3; }";
$customCss .= ".low .gantt_task_progress { background: #547dab; } .status_line { background-color: #0ca30a; }";
$customCss .= ".deadline { position: absolute; -moz-box-sizing: border-box; color: white; background: rgb(77, 77, 255); margin-left: -11px; border-radius: 15px; width: 24px; font-size: 20px; text-align: center; z-index: 1; }";
$customCss .= ".overdue-indicator { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: rgb(0, 230, 0); line-height: 25px; text-align: center; font-size: 24px; }";
$customCss .= ".overdue-indicator-danger { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: red; line-height: 25px; text-align: center; font-size: 24px; }";
$customCss .= ".overdue-indicator-warning { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: #FFCC33; line-height: 25px; text-align: center; font-size: 24px; } .custom_progress { display: inline-block; vertical-align: top; text-align: center; height: 100%; }";
$customCss .= ".custom_progress.warning { background-color: #ffd11a; }";
$customCss .= ".custom_progress.success { background-color: #00e600; }";
$customCss .= ".custom_progress.info { background-color: #00bfff; } .custom_progress.danger { background-color: #d9534f; } .custom_progress.primary { background-color: #2e6da4; }";
$customCss .= ".custom_progress {display: inline-block;vertical-align: top;text-align: center;height: 100%;}.custom_progress.nearly_done {background-color: #ffd11a;}.custom_progress.in_progress {background-color: #00e600;}.custom_progress.idle {background-color: #00bfff;}";
?>


<style type="text/css">

    .red .gantt_cell, .odd.red .gantt_cell,
    .red .gantt_task_cell, .odd.red .gantt_task_cell {
        background-color: #FDE0E0;
    }

    .green .gantt_cell, .odd.green .gantt_cell,
    .green .gantt_task_cell, .odd.green .gantt_task_cell {
        background-color: #BEE4BE;
    }

    .yellow .gantt_cell, .odd.yellow .gantt_cell,
    .yellow .gantt_task_cell, .odd.yellow .gantt_task_cell {
        background-color: #FBF5A8;
    }
    .gray .gantt_cell, .odd.gray .gantt_cell,
    .gray .gantt_task_cell, .odd.gray .gantt_task_cell {
        background-color: #E6E6E6;
    }

    .high {
        border: 2px solid #d96c49;
        color: #d96c49;
        background: #d96c49;
    }

    .high .gantt_task_progress {
        background: #db2536;
    }

    .medium {
        border: 2px solid #34c461;
        color: #34c461;
        background: #34c461;
    }

    .medium .gantt_task_progress {
        background: #23964d;
    }

    .low {
        border: 2px solid #6ba8e3;
        color: #6ba8e3;
        background: #6ba8e3;
    }

    .low .gantt_task_progress {
        background: #547dab;
    }
    .status_line {
        background-color: #0ca30a;
    }

    .deadline {
        position: absolute;
        -moz-box-sizing: border-box;
        color: white;
        background: rgb(77, 77, 255);
        margin-left: -11px;
        margin-top: 3px;
        border-radius: 15px;
        width: 24px;
        font-size: 20px;
        text-align: center;
        z-index: 1;
    }

    .overdue-indicator-success {
        width: 24px;
        margin-top: 5px;
        height: 24px;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        border-radius: 17px;
        color: rgb(0, 230, 0);
        line-height: 25px;
        text-align: center;
        font-size: 24px;
    }

    .overdue-indicator-danger {
        width: 24px;
        margin-top: 5px;
        height: 24px;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        border-radius: 17px;
        color: red;
        line-height: 25px;
        text-align: center;
        font-size: 24px;
    }

    .overdue-indicator-warning {
        width: 24px;
        margin-top: 5px;
        height: 24px;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        border-radius: 17px;
        color: #FFCC33;
        line-height: 25px;
        text-align: center;
        font-size: 24px;
    }

</style>

<input type="hidden" value="<?= $widget_id ?>" name="widget_id" id="data-widget_id">
<input type="hidden" value="<?= $schedule_id ?>" name="schedule_id" id="schedule_widget_id">
<input type="hidden" value="<?= $data_id ?>" name="data_id" id="data-data_id">
<input type="hidden" value="<?= $data_ptid ?>" name="data_id" id="data-data_ptid">
<input type="hidden" value="<?= $group_id ?>" name="data-group_id" id="data-group_id">
<input type="hidden" value="<?= $widget_id ?>" name="widget_id" id="data-widget_id">
<input type="hidden" value="<?= $schedule_id ?>" name="schedule_id" id="data-schedule_id">


<?php
$widget_schedule = SubjectManagementQuery::getWidgetById($schedule_id);
$optionSchedule = appxq\sdii\utils\SDUtility::string2Array($widget_schedule['options']);

$items = [];
$ezform_group = EzfQuery::getEzformOne($optionSchedule['group_ezf_id']);
$groupList = SubjectManagementQuery::getGroupScheduleByWidget($schedule_id, $ezform_group, $optionSchedule['group_field']);
unset($groupList[0]);
?>
<ul class="nav nav-tabs">
    <?php
    foreach ($groupList as $key => $val) {
        $active = "";
        if ($key == 1) {
            $active = 'active';
        }
        ?>
        <li id="tab-gantt-group<?= $key ?>" class="<?= $active ?> tab-gantt-group" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="<?= $val['group_name'] ?>" data-group_id="<?= $val['id'] ?>"><a href="#"><?= $val['group_name'] ?></a></li>
        <?php }
        ?>

</ul>
<div class="gantt-default-index">      
    <section class="content">  
        <div class="dropdown pull-right" style="margin-right: 5px;padding-top: 20px;">
            <button class="btn btn-warning  dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('subject', 'Export PMS') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="#" onclick='gantt.exportToMSProject({
                            name: "mypms-export-msproject.xml",
                            header: "<style><?= $customCss ?></style><h1>Subject Number : <?= $subject_number ?></h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            skip_circular_links: false,
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })'><i class='fa fa-file'></i> Export to MS Project </a></li>
                <li><a href="#" onclick='gantt.exportToExcel({
                            name: "mypms-export-excel.xlsx",
                            header: "<style><?= $customCss ?></style><h1>Subject Number : <?= $subject_number ?></h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })' ><i class='fa fa-file-excel-o '></i> Export to Excel </a></li>
                <li><a href="#" onclick='gantt.exportToPDF({
                            name: "mypms-export-pdf.pdf",
                            header: "<style><?= $customCss ?></style><h1>Subject Number : <?= $subject_number ?></h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        });'><i class="fa fa-file-pdf-o"></i> Export to PDF</a></li>
                <li><a href="#" onclick='gantt.exportToPNG({
                            name: "mypms-export-pdf.png",
                            header: "<style><?= $customCss ?></style><h1>Subject Number : <?= $subject_number ?></h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })'><i class="fa fa-file-image-o"></i> Export to PNG </a></li>
            </ul>
        </div>
        <!--<div class="foreground" style="width: 100%;height: 550px; background-color: yellow; position: absolute;">-->

        <!--</div>-->

        <div class="row">
            <div class="col-md-2">
                <?= Html::label("Scale Unit", 'scale_unit') ?>
                <?= Html::dropDownList('scale_unit', isset($scale_unit) ? $scale_unit : '0', ArrayHelper::map($itemUnit, 'id', 'unit_name'), ['class' => 'form-control scale_unit_selector']) ?>

            </div>
            <div class="col-md-2">
                <?= Html::label(Yii::t('subjects', "Scale Step"), 'scale_step') ?>
                <?= Html::input('number', 'scale_step', $step, ['class' => 'form-control scale_step_selector']) ?>

            </div>

            <div class="clearfix"></div>
            <br/>

        </div>
    </section>   
</div>
<style type="text/css">
    .custom_progress {
        display: inline-block;
        vertical-align: top;
        text-align: center;
        height: 100%;
    }

    .custom_progress.nearly_done {
        background-color: #ffd11a;
    }

    .custom_progress.in_progress {
        background-color: #00e600;
    }

    .custom_progress.idle {
        background-color: #00bfff;
    }

</style>

<?php
//
//
if ($skin_name == "default") {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt.css");
} else {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt_" . $skin_name . ".css");
}
?>
<div id="gantt-individual" data-url="<?=
     Url::to([
         'gantt-subject-patient',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'skin_name' => $skin_name,
         'data_id' => $data_id,
         'data_ptid' => $data_ptid,
         'group_id' => $group_id,
     ]);
     ?>">

</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    var checkEvent = 0;
    $(function () {
        var url = $('#gantt-individual').attr('data-url');
        var group_name = "";
        var group_id = "";
        $('.tab-gantt-group').each(function (i, e) {
            var ezf_id = "";
            var data_id = "";

            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
                group_name = $(e).attr('data-group_name');
                group_id = $(e).attr('data-group_id')
            }

        });
        var scale_unit = $('.scale_unit_selector').val();
        var scale_step = $('.scale_step_selector').val();
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: false,
            data: {scale_unit: scale_unit, scale_step: scale_step, group_name: group_name, group_id: group_id},
            success: function (result) {
                $('#gantt-individual').empty();
                $('#gantt-individual').html(result);
            }
        });

    });

    $('.scale_filter_selector').change(function () {
        getReloadDiv($('#gantt-individual').attr('data-url'), 'gantt-individual');
    });
    $('.btn_reload').click(function () {
        var url = $('#gantt-individual').attr('data-url');
        getReloadDiv(url, 'gantt-individual');
    });

    $('#modal-ezform-gantt').on('hidden.bs.modal', function () {
        getReloadDiv($('#gantt-individual').attr('data-url'), 'gantt-individual');
    });

    $('.tab-gantt-group').click(function () {
        $(document).find('.tab-gantt-group').removeClass('active')
        $(this).addClass('active')

        var url = $('#gantt-individual').attr('data-url');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id')
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: false,
            data: {group_id: group_id, group_name: group_name},
            success: function (result) {
                gantt.clearAll();
                $('#gantt-individual').empty();
                $('#gantt-individual').html(result);
            }
        });
    });

    function getReloadDiv(url, div) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var proID = $('.project-selector').val();
        var scale_unit = $('.scale_unit_selector').val();
        var scale_step = $('.scale_step_selector').val();
        var group_name = "";
        var group_id = "";
        $('.tab-gantt-group').each(function (i, e) {
            var ezf_id = "";
            var data_id = "";

            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
                group_name = $(e).attr('data-group_name');
                group_id = $(e).attr('data-group_id')
            }

        });

        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: true,
            data: {scale_unit: scale_unit, scale_step: scale_step, group_id: group_id, group_name: group_name},
            success: function (result) {
                gantt.clearAll();
                checkEvent = 0;
                $('#' + div).empty();
                $('#' + div).html(result);
            }
        });
    }
    $('#modal-ezform-gantt').on('hidden.bs.modal', function (e) {
        gantt.clearAll();
        onInitGantt();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>