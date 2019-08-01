<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Url;
use kartik\tabs\TabsX;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfHelper;
use appxq\sdii\helpers\SDNoty;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$widget_schedule = SubjectManagementQuery::getWidgetById($options['widget_id']);
$optionSchedule = appxq\sdii\utils\SDUtility::string2Array($widget_schedule['options']);

$items = [];
$groupList = [];
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
        <li id="tab-procedure-group<?= $key ?>" class="<?= $active ?> tab-procedure-group" data-ezf_id="<?= $optionSchedule['group_ezf_id'] ?>" 
            data-group_name="<?= $val['group_name'] ?>" data-group_id="<?= $val['id'] ?>"><a href="#"><?= $val['group_name'] ?></a></li>
        <?php }
        ?>

</ul>
<br/>

<div class="row">
    <div class="col-lg-6">
        <?php if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) : ?>
            <?= EzfHelper::btn($options['procedure_ezf_id'])->label(Yii::t('ezform', '<i class="fa fa-plus"></i>') . " New procedure")->modal('modal-ezform-procedure')->reloadDiv('display-procedure')->initdata(['procedure_type' => '1', 'group_name' => '0'])->options(['class' => 'btn btn-success'])->buildBtnAdd(); ?>
            <?= yii\helpers\Html::button("<i class='fa fa-plus'></i> " . Yii::t('subject', 'New Visit'), ['id' => 'btn-add-visit', 'class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <div class="input-group">
            <span class="input-group-btn">
                <button class="btn btn-secondary" type="button"><?= Yii::t('subjects', 'Search') ?></button>
            </span>
            <input type="text" class="form-control" placeholder="Search for Procedure...">
        </div>
    </div>
</div>
<br/>
<div id="display-procedure" data-url="
     <?=
     Url::to(['/subjects/subject-management/visit-procedure',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'module_id' => $module_id,
         'options' => $options,
         'user_create' => $user_create,
         'user_update' => $user_update,
         'reloadDiv' => $reloadDiv,
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
        var ezf_id = "";
        var group_id = "";
        $('.tab-procedure-group').each(function (i, e) {

            if ($(e).hasClass('active')) {
                ezf_id = $(e).attr('data-ezf_id');
                group_id = $(e).attr('data-group_id');
                group_name = $(e).attr('data-group_name');
            }

        });
        var display = $('#display-procedure');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');
        url += '&group_name=' + group_name + '&group_id=' + group_id;
        display.attr('data-url', url)
        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });

    $('.tab-procedure-group').click(function () {

        var ezf_id = $(this).attr('data-ezf_id');
        var group_id = $(this).attr('data-group_id');
        var group_name = $(this).attr('data-group_name');

        $(document).find('.tab-procedure-group').removeClass('active')

        $(this).addClass('active')
        var display = $('#display-procedure');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display.attr('data-url');
        url += '&group_name=' + group_name + '&group_id=' + group_id;
        display.attr('data-url', url)

        $.get(url, {group_name: group_name, group_id: group_id}, function (result) {
            display.html(result);
        });
    });
    $('#btn-add-visit').click(function () {
        var group_id = "";
        var group_name = "";
        $('.tab-procedure-group').each(function (i, e) {

            if ($(e).hasClass('active')) {
                group_id = $(e).attr('data-group_id');
                group_name = $(e).attr('data-group_name');
            }

        });
        var url = '<?=
     yii\helpers\Url::to([
         '/subjects/subject-management/config-visit-procedure',
         'reloadDiv' => 'display-procedure',
         'options' => $options,
         'widget_id' => $widget_id,
         'schedule_id'=>$schedule_id,
     ])
     ?>';
        url = url +"&group_id="+group_id;
        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });
    function getReloadDiv(url, div) {
        $('#' + div).html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>