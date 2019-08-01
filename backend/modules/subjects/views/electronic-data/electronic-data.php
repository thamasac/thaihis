<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use kartik\tabs\TabsX;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);

$scheduleOptions = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
if (isset($scheduleOptions['subject_ezf_id']))
    $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);

$ezform_main = EzfQuery::getEzformOne($scheduleOptions['11111']['main_ezf_id']);
$subDisplay = $scheduleOptions['subject_field'];
$field_visit = isset($scheduleOptions['11111']['main_visit_name'])?$scheduleOptions['11111']['main_visit_name']:'visit_name';
$visit_name = $scheduleOptions['11111']['form_name'];
$visit_id = '11111';
$field_visit2 = $scheduleOptions['22222']['random_visit_name'];
$visit_name2 = $scheduleOptions['22222']['form_name'];
$visit_id2 = '22222';

$data = SubjectManagementQuery::GetScheduleActivity($subjectForm, $ezform_main->ezf_table, $subDisplay, '('.$ezform_main->ezf_table . '.' . $field_visit . '="' . $visit_name . '" OR '. $field_visit . '="' . $visit_id . '")');
?>
<style>
    .active-select{
        background-color: #fbf069;
    } 
</style>

<div class="col-md-2" id="show-subject-list">
    <div class="list-group">
        <li class="list-group-item active" style="text-align: center;">Screening Number(Subject Number)</li>
        <li class="list-group-item" style="text-align: center;">
            <input type="text" name="subject-number-search" id="subject-number-search" class="form-control subject-number-search" placeholder="Subject Search..."> 
        </li>
        <?php foreach ($data as $key => $value): 
            $group_id = SubjectManagementQuery::getGroupByTarget($ezform_main, $value['target'])
            ?>
            <a href="javascript:void(0)" class="list-group-item subject-item" data-id="<?= $value['target'] ?>" data-subject_number="<?=$value['subject_no']?>" data-subject="<?= $value[$subDisplay] ?>" 
               data-group_name="<?= $value['group_name'] ?>" data-group_id="<?= $group_id['group_name']?>" style="text-align:right;font-size:16px;">
                <i class="fa fa-address-card "></i> <?= $value[$subDisplay].(isset($value['subject_no']) && $value['subject_no'] !=''?" <label style='color:blue;'>({$value['subject_no']})</label>":'') ?></a>
        <?php endforeach; ?>
    </div>
</div>

<div class="col-md-2 sdbox-col" id="show-visit" data-url="">

</div>

<div class="col-md-8 sdbox-col" id="show-data-table">

</div>
<div class="clearfix"></div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('#show-subject-list').on('click', '.subject-item', function () {
        $('#show-subject-list').find('.list-group-item-info').removeClass('list-group-item-info');
        $(this).addClass('list-group-item-info');

        var url = '<?=
yii\helpers\Url::to(['/subjects/electronic-data/electronic-data-visit',
    'options' => $options
])
?>';
        var data_id = $(this).attr('data-id');
        var subject_id = $(this).attr('data-subject');
        var subject_number = $(this).attr('data-subject_number');
        var group_name = $(this).attr('data-group_name');
        var group_id = $(this).attr('data-group_id');
        var showVisit = $('#show-visit');
        var showData = $('#show-data-table');
        $('#show-visit').removeClass('col-md-2');
        $('#show-visit').addClass('col-md-2');
        $('#show-data-table').removeClass('col-md-10');
        $('#show-data-table').removeClass('col-md-8');
        $('#show-data-table').addClass('col-md-8');
        
        showData.empty();
        showVisit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {data_id: data_id, subject_id: subject_id,group_name:group_name,group_id:group_id,subject_number:subject_number},
            success: function (result) {
                showVisit.empty();
                showVisit.html(result);
            }
        })
    });

    $('#show-subject-list').on('change', '.subject-number-search', function () {
        var show_subject = $('#show-subject-list');
        var subjectSearch = $(this).val();
        var url = '<?=
yii\helpers\Url::to(['/subjects/electronic-data/electronic-data-search',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'financial_id' => $financial_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
])
?>';
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {subject_search: subjectSearch},
            success: function (result) {
                show_subject.empty();
                show_subject.html(result);
            }
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>


