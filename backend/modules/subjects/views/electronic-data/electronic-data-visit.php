<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\gantt\classes\GanttQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);

$schedule_data = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
$ezform_subject = EzfQuery::getEzformOne($schedule_data['subject_ezf_id']);

$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$procedureForm = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);

$fieldDisplay = $schedule_data['subject_field'];
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);

$additional_forms = $options['additional_forms'];
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
?>
<style>
    .btn-active{
        background-color: #337ab7;
        color:#ffffff;
    }
    .modal-content{
        box-shadow:none;
    }
</style>
<button class="btn btn-primary" id="btn-zoomin-visit" title="<?= Yii::t('subject', 'Zoom in') ?>" style="display:none;"><i class="fa fa-plus"></i></button>
<div class="list-group " id="panel-visit-list" >
    <li class="list-group-item active" ><label class="">Visit.</label> <button id="btn-zoomout-visit" class="btn btn-primary pull-right" title="<?= Yii::t('subject', 'Zoom out') ?>"><i class="fa fa-minus "></i></button> </li>
    <li class="list-group-item additional_form_box" data-open="0" style="background-color: orange;color:white;"><i class="fa fa-chevron-circle-right "></i> <strong>Additional Forms</strong></li>
    <div class="box_display_additional" style="display:none;padding-left: 10px;">
        <?php
        foreach ($additional_forms as $key => $val):
            $ezform = EzfQuery::getEzformOne($val);
            $data_main = SubjectManagementQuery::GetTableData($ezform, ['target' => $data_id], 'one', null, ['order' => 'desc', 'column' => 'create_date'])
            ?>
            <a class="list-group-item subject-additional-form" href="javascript:void(0)" 
               data-data_id="<?= isset($target) ? $target : '' ?>" data-target="<?= isset($data_id) ? $data_id : '' ?>" data-data_id="<?= isset($data_main['id']) ? $data_main['id'] : '' ?>" data-viersion="<?= $ezform->ezf_version ?>"
               data-ezf_id="<?= $ezform->ezf_id ?>" ><?= $ezform->ezf_name ?></a>
           <?php endforeach; ?>
    </div>
    <?php
    $dataDetail = null;
    $ezform_detail = EzfQuery::getEzformOne($schedule_data['11111']['main_ezf_id']);
    if ($ezform_detail) {
        $dataDetail = SubjectManagementQuery::GetTableData($ezform_detail, ['target' => $data_id]);
    }

    $dataProcedure = SubjectManagementQuery::GetTableData($procedureForm);
    $visitProcedure = SubjectManagementQuery::GetTableDataNotEzform('zdata_visit_procedure', ['group_name' => $group_id]);

    if (isset($visitSchedule) && is_array($visitSchedule)) {
        foreach ($visitSchedule as $key => $value):
            $ezform_main = EzfQuery::getEzformOne($value['ezf_id']);
            $actual_field = $value['actual_date'];
            $visit_field = $value['visit_name_mapping'];
            $visit_name = $value['visit_name'];
            $visit_id = $value['id'];

            $proData = GanttQuery::findArraybyFieldName($visitProcedure, $visit_id, 'visit_name', 'all');

            $proDataVisit = null;
            $proForms = [];
            if ($proData) {
                foreach ($proData as $keyPro => $valPro) {
                    $proDataVisit = GanttQuery::findArraybyFieldName($dataProcedure, $valPro['procedure_name'], 'id');
                    if (isset($proDataVisit['ezform_crf']) && $proDataVisit['ezform_crf'] != null) {
                        $formArr = appxq\sdii\utils\SDUtility::string2Array($proDataVisit['ezform_crf']);
                        $proForms = array_merge($proForms,$formArr);
                    }
                }
            }


            if (!isset($ezform_main->ezf_table)) {
                $ezform_main = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
            }
            if ($actual_field == '') {
                $actual_field = $visitSchedule['11111']['actual_date'];
            }
            if ($visit_field == '') {
                $visit_field = $visitSchedule['11111']['visit_name_mapping'];
            }
            if ($visit_name == '') {
                $visit_name = $visitSchedule['11111']['visit_name'];
            }
            $disabled = 'false disabled';

            $targetVisit = GanttQuery::findArraybyFieldName($dataDetail, $visit_id, 'visit_name');
            if (isset($targetVisit['date_visit']) && $targetVisit['date_visit'] != '') {
                $disabled = '';
            }

            $success = 0;
            $waiting = 0;
            $no_process = 0;
            $formList = isset($value['form_list'])?\appxq\sdii\utils\SDUtility::string2Array($value['form_list']):[];

            if (count($formList) > 0) {
                $formList = array_merge($formList, $proForms);
            } else {
                $formList = $proForms;
            }

            if ($formList) {
                foreach ($formList as $keyForm => $valForm) {
                    $ezformThis = EzfQuery::getEzformOne($valForm);
                    if ($ezformThis) {
                        $dataForm = SubjectManagementQuery::GetTableData($ezformThis, ['target' => $data_id], 'one');
                        if ($dataForm['rstat'] == '2') {
                            $success += 1;
                        } else if ($dataForm['rstat'] == '1') {
                            $waiting += 1;
                        } else {
                            $no_process += 1;
                        }
                    }
                }
            }
            ?>
            <a style="text-align: left;" href="javascript:void(0)" class="list-group-item subject-visit<?= isset($disabled) ? $disabled : '' ?>" data-visit_id="<?= isset($visit_id) ? $visit_id : '' ?>" data-visit_field="<?= $visit_field ?>" 
               data-form_list="<?= base64_encode(appxq\sdii\utils\SDUtility::array2String($formList)) ?>" data-name="<?= $visit_name ?>" style="text-align:right;">
                   <?php if ($success >= count($formList) && count($formList) != 0): ?> 
                    <i class="fa fa-check-circle-o " style="color: green;font-size: 16px;"></i>
                <?php elseif ($success < count($formList) && count($formList) != 0 && $disabled == ''): ?>
                    <i class="fa fa-info-circle " style="color: orange;font-size: 16px;"></i>
                <?php else: ?>
                    <i class="fa fa fa-times-circle-o " style="color:red;font-size: 16px;"></i>
                <?php endif; ?>  
                <?= $visit_name." (".(count($formList)).")"?></a>
            <?php //endif; ?>
            <?php
        endforeach;
    }
    ?>

</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('.subject-visit').click(function () {
        $('#show-visit').find('.list-group-item-info').removeClass('list-group-item-info');
        $(this).addClass('list-group-item-info');
        var url = '<?=
yii\helpers\Url::to(['/subjects/electronic-data/electronic-data-form',
    'options' => $options,
    'subject_id' => $subject_id,
    'subject_number' => $subject_number,
])
?>';
        var showData = $(document).find('#show-data-table');
        var visit_id = $(this).attr('data-visit_id');
        var name = $(this).attr('data-name');
        var field = $(this).attr('data-visit_field');
        var form_list = $(this).attr('data-form_list');
        var group_name = '<?= $group_name ?>';
        var group_id = '<?= $group_id ?>';
        var data_id = '<?= $data_id ?>';
        url = url + '&data_id=' + data_id + '&field=' + field + '&group_name=' + group_name + '&group_id=' + group_id+"&visit_id="+visit_id+"&form_list="+form_list;
        $('#show-visit').attr('data-url', url);
        $('#show-data-table').attr('data-url', url);
        
        showData.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {visit_id: visit_id, name: name, data_id: data_id, field: field, group_name: group_name, group_id: group_id, form_list: form_list},
            success: function (result) {
                showData.empty();
                showData.html(result);
            }
        })
    });

    $('.additional_form_box').click(function () {
        var open = $(this).attr('data-open');

        if (open == '0') {

            $(this).find("i").removeClass("fa-chevron-circle-right");
            $(this).find("i").addClass("fa-chevron-circle-down");
            $(this).attr('data-open', '1');
            $(".box_display_additional").animate({
                opacity: 1,
                top: "-=50",
                height: "toggle"
            }, 250, function () {
                $('.box_display_additional').css('display', '');
            });
        } else {

            $(this).find("i").addClass("fa-chevron-circle-right");
            $(this).find("i").removeClass("fa-chevron-circle-down");
            $(this).attr('data-open', '0');
            $(".box_display_additional").animate({
                opacity: 1,
                top: "+=50",
                height: "toggle"
            }, 250, function () {
                $('.box_display_additional').css('display', 'none');
            });

        }

    });

    $('.subject-additional-form').click(function () {
        $('#show-visit').find('.list-group-item-info').removeClass('list-group-item-info');
        $(this).addClass('list-group-item-info');

        var showData = $(document).find('#show-data-table');
        showData.empty();
        showData.append('<div id="display-ezform"><div class="modal-content"></div></div>');
        showData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '/ezforms2/ezform-data/ezform';
        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var target = $(this).attr('data-target');
        var ezf_version = $(this).attr('data-ezf_version');
        var divData = $('#display-ezform');

        var data = {
            ezf_id: ezf_id,
            version: ezf_version,
            reloadDiv: 'show-data-table',
            dataid: data_id,
            target: target,
            modal: 'display-ezform',
        }

        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                showData.find('.modal-content').html(result);
                $(document).find(".glyphicon-remove").parent().remove();
                $(document).find(".close").remove();
            }
        });
    });

    $('#btn-zoomout-visit').click(function () {

        $("#panel-visit-list").animate({
            opacity: 1,
            top: "-=50",
            height: "toggle"
        }, 250, function () {
            $('#panel-visit-list').css('display', 'none');
            $('#btn-zoomin-visit').css('display', 'block');
            $('#show-visit').removeClass('col-md-2');
            $('#show-data-table').removeClass('col-md-8');
            $('#show-data-table').addClass('col-md-10');
        });
    });

    $('#btn-zoomin-visit').click(function () {
        $('#show-visit').addClass('col-md-2');
        $('#show-data-table').addClass('col-md-8');
        $('#show-data-table').removeClass('col-md-10');
        $('#btn-zoomin-visit').css('display', 'none');
        $("#panel-visit-list").animate({
            opacity: 1,
            top: "-=50",
            height: "toggle"
        }, 250, function () {

            $('#panel-visit-list').css('display', 'block');

        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
