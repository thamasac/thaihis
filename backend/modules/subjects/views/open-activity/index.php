<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

$groupItem = SubjectManagementQuery::getGroupScheduleByFunc($schedule_id);
$widgetOptions = SubjectManagementQuery::getWidgetById($schedule_id);
$scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($widgetOptions['options']);

$statusField = EzfQuery::getFieldByName($scheduleOptions['11111']['main_ezf_id'], 'type_visit');
$dataItems = \appxq\sdii\utils\SDUtility::string2Array($statusField['ezf_field_data']);

$statusItems[0] = 'Selected...';
foreach ($dataItems['items'] as $key => $value) {
    $statusItems[$key] = $value;
}
?>
<div class="row">
    <label>Search by: </label>
</div>
<div class="row" style="border:1px solid #aaaaaa;padding: 10px 5px;border-radius: 10px;">
    <div class="col-lg-2">
        <?= Html::label(Yii::t('subjects', 'Subject Number'), 'search') ?>
        <input type="text" name="search" class="form-control" id="search_number" placeholder="Search Subject Number...">

    </div>
    <div class="col-md-2 ">
        <?= Html::label(Yii::t('subjects', 'Group Name'), 'search_group') ?>
        <?= Html::dropDownList('group_name', '', $groupItem, ['class' => 'form-control', 'id' => 'search_group']) ?>

    </div>
    <div class="col-md-2 ">
        <?= Html::label(Yii::t('subjects', 'Visit Name'), 'group_name') ?>
        <?=
        \kartik\widgets\DepDrop::widget([
            'name' => 'search_visit',
            'options' => ['id' => 'search_visit'],
            'pluginOptions' => [
                'depends' => ['search_group'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/subjects/subject-management/get-visit', 'schedule_id' => $schedule_id])
            ]
        ])
        ?>

    </div>
    <div class="col-md-2 ">
        <?= Html::label(Yii::t('subjects', 'Date Consented '), 'date_consent') ?>
        <?=
        \kartik\date\DatePicker::widget([
            'name' => 'date_consent',
            'id' => 'date_consent',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd/mm/yyyy'
            ]
        ])
        ?>

    </div>
    <div class="col-md-2 ">
        <?= Html::label(Yii::t('subjects', 'Next Date Visit'), 'next_date') ?>
        <?=
        \kartik\date\DatePicker::widget([
            'name' => 'next_date',
            'id' => 'next_date',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd/mm/yyyy'
            ]
        ])
        ?>

    </div>
    <div class="col-md-2 ">
<?= Html::label(Yii::t('subjects', 'Subject Status'), 'subject_status') ?>
<?= Html::dropDownList('group_name', '', $statusItems, ['class' => 'form-control', 'id' => 'subject_status']) ?>

    </div>
</div>
<div class="clearfix"></div>
<br/>
<div class="display-activity" id="display-activity" data-url="<?=
     Url::to([
         '/subjects/open-activity/open-activity',
         'reloadDiv' => $reloadDiv,
         'module_id' => $module_id,
         'subject_profile_ezf' => $subject_profile_ezf,
         'subject_detail_ezf' => $subject_detail_ezf,
         'profile_column' => $profile_column,
         'detail_column' => $detail_column,
         'detail_column2' => $detail_column2,
         'field_subject' => $field_subject,
         'schedule_id' => $schedule_id,
         'profile_ezf' => $subject_profile_ezf,
         'detail_ezf' => $subject_detail_ezf,
         'modal' => $modal,
     ])
     ?>">

</div>
<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-activity-detail',
    'size' => 'modal-xl',
]);
?>

<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-grid-detail',
    'size' => 'modal-xl',
]);
?>

<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-show-formlist',
    'size' => 'modal-lg'
])
?>

<?php
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-export',
    'size' => 'modal-sm',
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);


//$project_id = "1520742111042203500";
?>

<script>
    $(function () {
        var url = $('.display-activity').attr('data-url');
        var div_result = $('.display-activity');
        div_result.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, function (result) {
            div_result.html(result);
        });
    });

    $('#modal-activity-detail').on('hidden.bs.modal', function (e) {
        getReloadDiv($('#display-activity').attr('data-url'), 'display-activity');
    });

    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }

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
        var subject_status = "";
        var next_date = "";

        group_id = $('#search_group').val();
        number = $('#search_number').val();
        visit_id = $('#search_visit').val();
        date_consent = $('#date_consent').val();
        next_date = $('#next_date').val();
        subject_status = $('#subject_status').val();

        var display = $('.display-activity');
        display.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = "";
        url = $('.display-activity').attr('data-url');

        $.get(url, {group_id: group_id, number: number, visit_id: visit_id, date_consent: date_consent, next_date: next_date, subject_status: subject_status}, function (result) {
            display.html(result);
        });
    }

    $('#modal-show-formlist').on('hidden.bs.modal', function (e) {
        if ($('#modal-activity-detail').hasClass('in')) {
            $('body').addClass('modal-open');
        }
        ;
    });

    $(document).on('click', '.view-activity', function () {
        console.log("OK");
        var showDetail = $(document).find('#modal-activity-detail');
        var data_id = $(this).attr('data-id');
        var inform_date = $(this).attr('data-inform_date');
        var url = '/subjects/open-activity/activity-detail?module_id=<?= $module_id ?>&data_id=' + data_id + '&inform_date=' + inform_date + '&reloadDiv=display-activity-detail&modal=modal-grid-detail&subject_profile_ezf=<?= $subject_profile_ezf ?>&field_subject=<?= $field_subject ?>&schedule_id=<?= $schedule_id ?>&subject_detail_ezf=<?= $subject_detail_ezf ?>&profile_column=<?= base64_encode(json_encode($profile_column)) ?>&detail_column=<?= base64_encode(json_encode($detail_column)) ?>&detail_column2=<?= base64_encode(json_encode($detail_column2)) ?>';
        showDetail.modal('show');
        showDetail.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        showDetail.find('.modal-content').load(url);
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
