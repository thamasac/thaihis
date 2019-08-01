<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Url;
use kartik\tabs\TabsX;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$widget_schedule = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$optionSchedule = appxq\sdii\utils\SDUtility::string2Array($widget_schedule['options']);

$items = [];
$ezform_group = EzfQuery::getEzformOne($optionSchedule['group_ezf_id']);
$groupList = SubjectManagementQuery::getGroupScheduleByWidget($options['schedule_widget_id'], $ezform_group, $optionSchedule['group_field']);
unset($groupList[0]);
?>
<ul class="nav nav-tabs">
    <li id="tab-allsubject-screen" class=" tab-allsubject-screen" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="Screening only" data-group_id="0"><a href="#">All subject</a></li>
    <?php
    foreach ($groupList as $key => $val) {
        $active = "";
        if ($key == 1) {
            $active = 'active';
        }
        ?>
        <li id="tab-allsubject-group<?= $key ?>" class="<?= $active ?> tab-allsubject-group" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="<?= $val['group_name'] ?>" data-group_id="<?= $val['id'] ?>"><a href="#"><?= $val['group_name'] ?></a></li>
        <?php }
        ?>

</ul>
<br/>
<div id="display-allsubject" data-url="
     <?=
     Url::to(['/subjects/subject-management/all-subject-payment',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'options' => $options,
         'start_date' => $start_date,
         'end_date' => $end_date,
         'thisPage'=>$thisPage,
         'view' => 'all-subject-payment',
         'reloadDiv' => 'show-subject-payment',
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
        var group_name = "";
        var group_id = "";
        $('.tab-allsubject-group').each(function (i, e) {
            var ezf_id = "";
            var data_id = "";

            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                data_id = $(e).attr('data-data_id');
                group_name = $(e).attr('data-group_name');
                group_id = $(e).attr('data-group_id')
            }

        });
        var display = $('#display-allsubject');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');
        url += '&group_name=' + group_name + '&group_id=' + group_id;
        display.attr('data-url', url)
        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });

    $('.tab-allsubject-group').click(function () {

        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id');

        $(document).find('.tab-allsubject-group').removeClass('active')
        $(document).find('.tab-allsubject-screen').removeClass('active')

        $(this).addClass('active')
        var display = $('#display-allsubject');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');
        url += '&group_name=' + group_name + '&group_id=' + group_id;
        display.attr('data-url', url)
        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });
    
    $('.tab-allsubject-screen').click(function () {

        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id');

        $(document).find('.tab-allsubject-group').removeClass('active')

        $(this).addClass('active')
        var display = $('#display-allsubject');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');
        url += '&group_name=' + group_name + '&group_id=' + group_id;
        display.attr('data-url', url)
        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>