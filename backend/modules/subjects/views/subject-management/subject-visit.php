<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);

$schedule_data = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
$ezform_subject = EzfQuery::getEzformOne($schedule_data['subject_ezf_id']);
$fieldDisplay = $schedule_data['subject_field'];
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);
$group_form = EzfQuery::getEzformOne($schedule_data['group_ezf_id']);
$group_data = SubjectManagementQuery::GetTableData($group_form, ['id'=>$group_id],'one');
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
$group_name = $group_data['group_name'];
if(empty($group_name))$group_name = "Subject is not group.";
else $group_name = "Visit of $group_name group."
?>

<div class="list-group">
    <li class="list-group-item active" >  <?=$group_name?></li>
    <?php
    foreach ($visitSchedule as $key => $value):
        $ezform_main = EzfQuery::getEzformOne($value['ezf_id']);
        $actual_field = isset($value['actual_date'])?$value['actual_date']:'date_visit';
        $visit_field = isset($value['visit_name_mapping'])?$value['visit_name_mapping']:'visit_name';
        $visit_name = $value['visit_name'];
        $visit_id = $value['id'];
        
        if(!isset($ezform_main->ezf_table)){
            $ezform_main = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
        }
        if($actual_field==''){
            $actual_field = $visitSchedule['11111']['actual_date'];
        }
        if($visit_field==''){
            $visit_field = $visitSchedule['11111']['visit_name_mapping'];
        }
        if($visit_name==''){
            $visit_name = $visitSchedule['11111']['visit_name'];
        }

        $data_main = SubjectManagementQuery::GetActivityAllVisit($ezform_subject, $ezform_main->ezf_table, $fieldDisplay, [$ezform_main->ezf_table . '.target' => $data_id, $visit_field => $visit_id], null, 'one');
        
        $subjectList = [];
        $data_subject = SubjectManagementQuery::getVisitProcedure($ezform_budget, $visit_id, $group_id);
                
        $status = 0;
        $approvedList = [];
        $additionalApp = [];
        $approvedList = SubjectManagementQuery::getVisitProcedureApproved($visit_id, $group_id,$data_id);
        $additionalList = SubjectManagementQuery::getVisitProcedureAddition($visit_id, $group_id,$data_id);
        foreach ($additionalList as $key => $val){
            $addapp = SubjectManagementQuery::GetTableData('subject_visit_approved', ['visit_name'=>$visit_id,'procedure_name'=>$val['procedure_name'],'subject_target_id'=>$data_id],'one');
            if($addapp)
                $additionalApp[] = $val;
        }     
        if (isset($approvedList) && (count($approvedList)+count($additionalApp)) >= (count($data_subject) + count($additionalList))) {
            $status = 1;
        }
        $disabled = "";
        if ($data_main[$actual_field] == '')
            $disabled = "false disabled";
        
        
        ?>
        <a style="text-align: left;" href="javascript:void(0)" class="list-group-item subject-visit<?= $disabled ?>" data-visit_id="<?= $visit_id ?>" data-visit_field="<?= $visit_field ?>" data-name="<?= $visit_name ?>" style="text-align:right;" >
            <?php if ($data_main[$actual_field] != '' && $status == '1'): ?> 
                <i class="fa fa-check-circle-o " style="color: green;font-size: 16px;"></i>
            <?php elseif ($data_main[$actual_field] != ''): ?>
                <i class="fa fa-info-circle " style="color: orange;font-size: 16px;"></i>
            <?php else: ?>
                <i class="fa fa fa-times-circle-o " style="color:red;font-size: 16px;"></i>
            <?php endif; ?>  
            <?= $visit_name ?></a>
        <?php //endif; ?>
    <?php endforeach; ?>
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
yii\helpers\Url::to(['/subjects/subject-management/subject-view-data',
    'options' => $options,
    'subject_id' => $subject_id,
    'subject_payment_widget'=>$subject_payment_widget,
])
?>';
        var showData = $(document).find('#show-data-table');
        var visit_id = $(this).attr('data-visit_id');
        var name = $(this).attr('data-name');
        var field = $(this).attr('data-visit_field');
        var group_name = '<?= $group_name ?>';
        var group_id = '<?= $group_id ?>';
        var data_id = '<?= $data_id ?>';
        showData.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {visit_id: visit_id, name: name, data_id: data_id, field: field, group_name: group_name, group_id: group_id},
            success: function (result) {
                showData.empty();
                showData.html(result);
            }
        })
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
