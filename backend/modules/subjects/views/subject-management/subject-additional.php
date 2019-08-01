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
$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id']);

$ezform_procedure = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);

$procedureAll = [];
$procedureCheck = [];
$count = 0;

$ezform_subject = EzfQuery::getEzformOne($schedule_data['subject_ezf_id']);
$subject_field = $schedule_data['subject_field'];
if(!isset($visit_budget))$visit_budget = [];
//$data = SubjectManagementQuery::GetTableData($ezform_subject, ['id' => $data_id], 'one');
//$additionalData = SubjectManagementQuery::GetTableData('subject_additional_payment', ['subject_target_id' => $data_id, 'visit_name' => $visit_id], 'one');

if (isset($budget_id) && count($budget_id) > 0):
    ?>

    <div id="display-view-data">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" >
                <thead style="font-size: 16px;font-weight: bold;">
                    <tr class="info">
                        <td  style="width: 300px;text-align: center;">
                            Procedure Name
                        </td>
                        <?php
                        foreach ($budget_fields as $valField):
                            $field_procedure = EzfQuery::getFieldByName($options['budget_ezf_id'], $valField);
                            ?>
                            <td  style="width: 300px;text-align: center;">
                            <?= $field_procedure['ezf_field_label'] ?>
                            </td>
    <?php endforeach; ?>
                        <td  style="width: 300px;text-align: center;">
                            Status
                        </td>
                        <td  style="width: 300px;text-align: center;">
                            Approve for pay
                        </td>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $sectionProcedure = [];
                    $c = 0;
                    foreach ($budget_id as $key => $value):
                        $net_total = [];
                        $sectionAll = [];
                        $proNoBudget = [];
                        $sumBudget = [];
                        $row_total = 0;

                        $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['id' => $value], 'one');

                        $val = $data_budget['pro_name'];
                        $approvedData = SubjectManagementQuery::GetTableData('subject_visit_approved', ['subject_target_id' => $data_id, 'visit_name' => $visit_id, 'procedure_name' => $val], 'one');
                        $prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, ['id' => $val], 'one');

                        $sectionProcedure[$c]['all'][$key]['id'] = $val;
                        $sectionProcedure[$c]['all'][$key]['procedure_name'] = $prodecureData['procedure_name'];
                        $sectionProcedure[$c]['all'][$key]['budget'] = $data_budget['budget'];
                        $sectionProcedure[$c]['all'][$key]['financial_type'] = $data_budget['financial_type'];
                        $approved = '0';
                        if ($approvedData) {
                            $sectionProcedure[$c]['approved'][$key]['id'] = $val;
                            $sectionProcedure[$c]['approved'][$key]['procedure_name'] = $prodecureData['procedure_name'];
                            $sectionProcedure[$c]['approved'][$key]['budget'] = $data_budget['budget'];
                            $sectionProcedure[$c]['approved'][$key]['financial_type'] = $data_budget['financial_type'];
                            $approved = '1';
                        }

                        foreach ($budget_fields as $valField):
                            
                            $budget[$valField] = !isset($visit_budget) ? $data_budget[$valField] : isset($visit_budget[$valField]) ? $visit_budget[$valField] : null;
                            if(isset($budget[$valField]))$sumBudget[$valField] += $budget[$valField];

                        endforeach;

                        $revenue += $budget[$budget_fields[0]];
                        $expense += $budget[$budget_fields[1]] + $budget[$budget_fields[2]] + $budget[$budget_fields[3]];
                        $income_lumpsum = $revenue - $expense;
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo SubjectManagementQuery::GetTableData($ezform_procedure, ['id' => $val], 'one')['procedure_name'];
                                ?>
                            </td>
                            <?php foreach ($budget_fields as $valField): ?>
                                <td align="center">
                                    <div class="col-md-10 col-sm-offset-1">
                                        <label class="" ><?= number_format($budget[$valField] == '' ? 0 : $budget[$valField]) ?></label>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                            <td align="center">
                                <div class="col-md-10 col-sm-offset-1">
                                    <?php if ($approved == '1'): ?>
                                        <label class="label label-success" style="font-size: 14px;">Done</label>
                                    <?php else: ?>
                                        <label class="label label-warning" style="font-size: 14px;">Waiting</label>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td align="center">
                                <?php if ($approved == '1'): ?>
                                    <label class="label label-success " style="font-size: 14px;" disabled="disabled">
                                        <i class="fa fa-check-circle-o"></i> Approved</label>
                                <?php else: ?>
                                    <button class="btn btn-success subject-approved-add" data-id="<?= $data_id ?>" data-visit_name="<?= $visit_name ?>" data-visit_id="<?= $visit_id ?>" data-visit_field="<?= $visit_field ?>" data-pro_name="<?= $val ?>">
                                        <i class="fa fa-legal"></i> Approve</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                </tfoot>
            </table>
        </div>

    </div>
    <div class="col-md-12">
        <a href="javascript:void(0)" data-url="<?=
        \yii\helpers\Url::to([
            '/subjects/reports/payment-inform',
            'module_id' => $module_id,
            'data_id' => $data_id,
            'group_name' => $group_name,
            'group_id' => $group_id,
            'income_lumpsum' => $income_lumpsum,
            'visit_name' => $visit_name,
            'sectionProcedure' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($sectionProcedure)),
            'procedure_id' => $options['procedure_widget_id'],
            'budget_id' => $options['budget_ezf_id'],
            'type' => 'patient',
            'financial_type' => 'additional',
        ])
        ?>" id="additional-inform-print" class="pull-right" style="font-size:40px;"><i class="fa fa-print"></i></a>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>

<div  id="display-summary">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" >
            <thead>
                <tr class="success">
                    <td><strong>Revenue</strong></td>
                    <td><strong>Expense</strong></td>
                    <td><strong>Income (lump sum)</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?= number_format($revenue == null ? 0 : $revenue) ?></strong></td>
                    <td><strong><?= number_format($expense == null ? 0 : $expense) ?></strong></td>
                    <td><strong><?= number_format($income_lumpsum) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('.subject-approved-add').on('click', function () {

        var button = $(this);
        var data_id = $(this).attr('data-id');
        var visit_name = $(this).attr('data-visit_name');
        var visit_id = '<?= $visit_id ?>';
        var visit_field = $(this).attr('data-visit_field');
        var pro_name = $(this).attr('data-pro_name');
        var procedure_widget_id = '<?= $procedure_widget_id ?>';
        var budget_ezf_id = '<?= $budget_ezf_id ?>';
        var module_id = '<?= $module_id ?>';
        bootbox.confirm({
            title: '<?= Yii::t('subjects', 'Confirm') ?>',
            message: '<?= Yii::t('subjects', 'Are you sure to approve ?') ?>',
            callback: function (result) {
                if (result) {
                    button.prop("disabled", true);
                    handleAction();
                }
            }
        });

        var handleAction = function () {
            $.get('/subjects/subject-management/save-approved', {'target': data_id, 'visit_name': visit_name, 'visit_id': visit_id, 'pro_name': pro_name, 'procedure_widget_id': procedure_widget_id, 'budget_ezf_id': budget_ezf_id, 'visit_field': visit_field}, function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    laterApproved();
                }
            });
        }

    });
    $('#additional-inform-print').click(function () {
        var url = $(this).attr('data-url');
        var modal = $('#modal-payment-inform');
        modal.modal('show');
        modal.find('.modal-content').load(url);
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
