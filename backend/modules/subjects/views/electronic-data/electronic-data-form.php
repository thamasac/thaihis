<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);
$procedure_widget_id = $options['procedure_widget_id'];
$budget_ezf_id = $options['budget_ezf_id'];

$schedule_data = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
$ezform_section = EzfQuery::getEzformOne($options['section_ezf_id']);

//$visitSchedule = SubjectManagementQuery::getVisitScheduleById($options['schedule_widget_id'], $visit_id);
//$form_list = \appxq\sdii\utils\SDUtility::string2Array($visitSchedule['form_list']);

$table_width = "100%";

$ezform_procedure = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "procedure_type=1 AND (group_name='$group_id' OR group_name='0' OR group_name IS NULL)");
$budgetData = SubjectManagementQuery::GetTableData($ezform_budget);
$sectionData = SubjectManagementQuery::GetTableData($ezform_section);

$procedureAll = [];
$procedureCheck = [];
$count = 0;
$subjectList = [];

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
<div class="clearfix"></div>
<div id="show-form-list">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div id="display-form-list">
                <label style="font-size: 16px;" class="label label-primary">Form for Screening Number(Subject Number): <?= $subject_id." ({$subject_number})" ?></label>

                <br/><br/>
                <?php
                
                foreach ($form_list as $key => $val):
                    $ezform_data = EzfQuery::getEzformOne($val);
                    $data_main = SubjectManagementQuery::GetTableData($ezform_data, "(target='{$data_id}' OR subject_link='{$data_id}') AND visit_link='{$visit_id}' ", 'one', null, ['order'=>'desc','column'=>'create_date'])
                    ?>
                    <a class="btn btn-default btn-open-form" style="font-weight: bold;" href="javascript:void(0)" data-ezf_id="<?= $val ?>" data-ezf_version="<?= $ezform_data['ezf_version'] ?>" data-data_id="<?= $data_main['id'] ?>" data-target="<?= $data_id ?>">
                        <?php if ($data_main['rstat'] == '2'): ?> 
                            <i class="fa fa-check-circle-o " style="color: green;font-size: 16px;"></i>
                        <?php elseif ($data_main): ?>
                            <i class="fa fa-info-circle " style="color: orange;font-size: 16px;"></i>
                        <?php else: ?>
                            <i class="fa fa fa-times-circle-o " style="color:red;font-size: 16px;"></i>
                        <?php endif; ?>  
                        <?= $ezform_data['ezf_name'] ?></a>
                    <?php endforeach; ?>

            </div>

        </div>
        <div class="panel-body">
            <div id="display-ezform">
                <div class="modal-content">
                    <label style="font-size:20px;color: gray;"><?= Yii::t('subjects', 'Select Form...') ?></label>
                </div>
            </div>
        </div>

    </div>

</div>
<div class="clearfix"></div>
<br/>
<?php
echo ModalForm::widget([
    'id' => 'modal-content-history',
    'size' => 'modal-xl',
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
    function onRemoveBackdrop(){
         $(document).find(".modal-backdrop").remove();
         $(document).find(".modal-open").removeClass('modal-open');
         setTimeout(function(){
             
         },300);
    }

    $(document).on('click','.ezform-main-open',function(){
        onRemoveBackdrop();
    });
    
    $('.btn-open-form').click(function () {
        $(document).find('.btn-open-form').removeClass('active');
        $(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform';
        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var target = $(this).attr('data-target');
        var ezf_version = $(this).attr('data-ezf_version');
        var divData = $('#display-ezform');
        
        var initdata= {visit_name:'<?=$visit_id?>'};
        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        
        $('#show-data-table').attr('data-url',$('#show-visit').attr('data-url')+"&ezf_id="+ezf_id);
        
        var data = {
            ezf_id: ezf_id,
            version: ezf_version,
            reloadDiv: 'show-data-table',
            dataid: data_id,
            target: target,
            modal:'display-ezform',
            initdata:btoa(JSON.stringify(initdata)),
        }

        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                divData.find('.modal-content').html(result);
                $(document).find(".glyphicon-remove").parent().remove();
                $(document).find(".close").remove();
            }
        });
    });
 
</script>
<?php \richardfan\widget\JSRegister::end(); ?>