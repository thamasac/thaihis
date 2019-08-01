
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
?>
<?php
$this->registerCss('

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
    ');

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

$unit = "week";
$step = '1';
if ($scale_unit == '0') {
    $unit = "day";
} elseif ($scale_unit == '1') {
    $unit = "week";
} elseif ($scale_unit == '2') {
    $unit = "month";
}

if (isset($scale_step))
    $step = $scale_step;
?>

<input type="hidden" value="<?= $widget_id ?>" name="widget_id" id="data-widget_id">
<input type="hidden" value="<?= $schedule_id ?>" name="schedule_id" id="data-schedule_id">
<div class="gantt-default-index">      
    <section class="content">     
        <?php
        if (EzfAuthFuncManage::auth()->accessBtn($module_id, 1)) {
            ?>

            <input class="btn btn-warning" value="Export to MS Project" type="button" onclick='gantt.exportToMSProject({skip_circular_links: false})'
                   style='margin:20px;'>
            <input class="btn btn-success" value="Export to Excel" type="button" onclick='gantt.exportToExcel()' style='margin:20px;'>
            <input class="btn btn-primary" value="Export to PDF " type="button" onclick='gantt.exportToPDF()' style='margin:20px;'>
            <input class="btn btn-info" value="Export to PNG " type="button" onclick='gantt.exportToPNG()'>
            <?php
        }
        ?>
        <div class="row">
            <div class="col-md-2">
                <?= Html::label("Scale Unit", 'scale_unit') ?>
                <?= Html::dropDownList('scale_unit', isset($scale_unit) ? $scale_unit : '1', ArrayHelper::map($itemUnit, 'id', 'unit_name'), ['class' => 'form-control scale_unit_selector']) ?>

            </div>
            <div class="col-md-2">
                <?= Html::label(Yii::t('subjects', "Scale Step"), 'scale_step') ?>
                <?= Html::input('number', 'scale_step', $step, ['class' => 'form-control scale_step_selector']) ?>

            </div>

            <div class="clearfix"></div>
        </div>
    </section>   
</div>
<br/>

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

<!--<script src="/js-gantt/htmlgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/js-gantt/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">-->
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
if ($skin_name == "default" || $skin_name =='' || $skin_name ==null || !isset($skin_name)) {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt.css");
} else {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt_" . $skin_name . ".css");
}
?>
<br/>
<div id="gantt-schedule" data-url="<?=
Url::to([
    'gantt-schedule',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'skin_name' => $skin_name,
    'reloadDiv' => $reloadDiv,
    'unit' => $unit,
    'step' => $step,
    'module_id'=>$module_id,
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
        var url = $('#gantt-schedule').attr('data-url');
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
            cache: false,
            data: {group_id: group_id},
            success: function (result) {
                $('#gantt-schedule').empty();
                $('#gantt-schedule').html(result);
            }
        });

    });

    $('.tab-gantt-group').click(function () {
        $(document).find('.tab-gantt-group').removeClass('active')
        $(this).addClass('active')

        var url = $('#gantt-schedule').attr('data-url');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id')
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: false,
            data: {group_id: group_id},
            success: function (result) {
                gantt.clearAll();
                $('#gantt-schedule').empty();
                $('#gantt-schedule').html(result);
            }
        });
    });

    function getReloadDiv(url, div) {
        var scale_unit = $('.scale_unit_selector').val();
        var scale_step = $('.scale_step_selector').val();
        $.get(url, {scale_unit: scale_unit, scale_step: scale_step}, function (result) {
            gantt.clearAll();
            $('#' + div).empty()
            $('#' + div).html(result);
        });
    }
    $('.scale_unit_selector').change(function () {
        getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
    });

    $('.scale_step_selector').change(function () {
        getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>