<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$widget_schedule = SubjectManagementQuery::getWidgetById($schedule_id);
$optionSchedule = appxq\sdii\utils\SDUtility::string2Array($widget_schedule['options']);

$items = [];
$ezform_group = EzfQuery::getEzformOne($optionSchedule['group_ezf_id']);
$groupList = SubjectManagementQuery::getGroupScheduleByWidget($schedule_id, $ezform_group, $optionSchedule['group_field']);
unset($groupList[0]);
?>
<ul class="nav nav-tabs">
    <li id="tab-allsubject-main" class="tab-electronic-main active" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="" data-group_id=""><a href="#">All Group</a></li>
    <?php
    foreach ($groupList as $key => $val) {
        $active = "";

        ?>
        <li id="tab-allsubject-group<?= $key ?>" class="<?= $active ?> tab-electronic-group" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="<?= $val['group_name'] ?>" data-group_id="<?= $val['id'] ?>"><a href="#"><?= $val['group_name'] ?></a></li>
        <?php }
        ?>

</ul>
<br/>
<div id="display_main_edc" data-url="<?=
     Url::to([
         '/subjects/electronic-data/electronic-dashboard',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'financial_id' => $financial_id,
         'options' => $options,
         'reloadDiv' => $reloadDiv,
     ]);
     ?>">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
        var group = "";
        $('.tab-electronic-group').each(function (i, e) {
            if ($(e).hasClass("active")) {
                group = $(e).attr("data-group_id");
            }
        });
        var div_show = $('#display_main_edc');
        var url = div_show.attr('data-url');
        
        div_show.attr('data-url-old',url);
        
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view: 'electronic-dashboard', group_id: group}, function (result) {
            div_show.html(result);
        });
    });

    $('.tab-electronic-group').click(function () {
        $(document).find('.tab-electronic-group').removeClass('active')
        $(document).find('.tab-electronic-main').removeClass('active')

        $(this).addClass('active')
        var div_show = $('#display_main_edc');
        var url = div_show.attr('data-url-old');
        var group = $(this).attr("data-group_id");
        div_show.attr('data-url',url+"&group_id="+group+"&view="+'electronic-dashboard');
        
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view: 'electronic-dashboard', group_id: group}, function (result) {
            div_show.html(result);
        });
    });
    
    $('.tab-electronic-main').click(function () {
        $(document).find('.tab-electronic-group').removeClass('active')
        $(this).addClass('active')
        var div_show = $('#display_main_edc');
        var url = div_show.attr('data-url-old');
        var group = $(this).attr("data-group_id");
        div_show.attr('data-url',url+"&group_id="+group+"&view="+'electronic-dashboard');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view: 'electronic-dashboard', group_id: group}, function (result) {
            div_show.html(result);
        });
    });



</script>
<?php \richardfan\widget\JSRegister::end(); ?>