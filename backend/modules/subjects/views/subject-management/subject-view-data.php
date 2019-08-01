<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

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

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);
$table_width = "100%";

$ezform_procedure = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "procedure_type IN (1,2)  AND (group_name='$group_id' OR group_name='0' OR group_name IS NULL)");
$budgetData = SubjectManagementQuery::GetTableData($ezform_budget);
$sectionData = SubjectManagementQuery::GetTableData($ezform_section);

$procedureAll = [];
$procedureCheck = [];
$count = 0;

$subjectList = [];

$data_subject = SubjectManagementQuery::getSubjectProcedureByVisit($visit_name, $options['procedure_widget_id'], $group_id);

$proId = "";

if (is_array($data_subject)) {
    foreach ($data_subject as $keyPro => $valPro) {
        $subjectList[] = $valPro['procedure_name'];
    }
}
foreach ($prodecureData as $key => $value) {
    if ($value['procedure_type'] == '1') {
        $procedureAll[$count]['id'] = $value['id'];
        $procedureAll[$count]['procedure_name'] = $value['procedure_name'];
        $procedureAll[$count]['type'] = $value['procedure_type'];
        $procedureAll[$count]['procedure_by'] = 'procedure';
        $procedureCheck[] = $value['procedure_name'];
        $count++;
    }
}

foreach ($prodecureData as $key => $value) {
    if ($value['procedure_type'] == '2') {
        $procedureAll[$count]['id'] = $value['id'];
        $procedureAll[$count]['procedure_name'] = $value['procedure_name'];
        $procedureAll[$count]['type'] = $value['procedure_type'];
        $procedureAll[$count]['procedure_by'] = 'financial';
        $count++;
    }
}

$dataSchedule = [];
$actual_field = '';
foreach ($visitSchedule as $key => $val) {
    $ezform = EzfQuery::getEzformOne($val['ezf_id']);
    if (!isset($ezform->ezf_table)) {
        $ezform = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
    }
    if ($actual_field == '') {
        $actual_field = isset($visitSchedule['11111']['actual_date']) ? $visitSchedule['11111']['actual_date'] : 'date_visit';
    }

    $data = SubjectManagementQuery::GetTableData($ezform, ['target' => $data_id, 'visit_name' => $val['id']], 'one');

    $actual_date = $data[$actual_field];
    $date = new DateTime($actual_date);
    
    $planDistance = "";
    if (isset($val['plan_date']) && $val['plan_date'] != '') {
        $planDistance = $date->modify('+' . $val['plan_date'] . ' day');
        $planDistance = $date->format('Y-m-d');
    }

    $dataSchedule[$key]['actual_date'] = $actual_date;
    $dataSchedule[$key]['plan_date'] = $planDistance;
}
$budget_fields = $subject_payment_widget['budget_fields'];

$visit_type = "";
$visit_date = "";
?>

<div id="display-visit-data">
    <label style="font-size: 16px;" class="label label-primary">Screening Number: <?= $subject_id ?></label>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width:<?= $table_width ?>px;">
            <thead style="font-size: 16px;font-weight: bold;">
                <tr class="info">
                    <td colspan="4"><label> Visit Name : <?= $visit_name ?></label></td>
                </tr>
                <tr>
                    <td><label>Actual Date</label></td>
                    <td><label>Plan Date</label></td>
                    <td><label>Earliest Date</label></td>
                    <td><label>Latest Date</label></td>
                </tr>
                <tr>
                    <?php
                    $main_actual_date = '';
                    $random_actual_date = '';
                    $latestDate = null;
                    if ($visit_id == '11111' || $visit_id == '22222') :
                        $form = $visitSchedule[$visit_id];
                        $actual_field = $form['actual_date'];
                        if ($actual_field == '') {
                            $actual_field = isset($visitSchedule['11111']['actual_date']) ? $visitSchedule['11111']['actual_date'] : 'date_visit';
                        }
                        if ($visit_id == '11111'): // Form Screen
                            $ezform_main = EzfQuery::getEzformOne($form['ezf_id']);
                            $data = SubjectManagementQuery::GetTableData($ezform_main, ['target' => $data_id, 'visit_name' => $visit_id], 'one');
                            $main_actual_date = $data[$actual_field];
                            ?>
                            <td><?= SubjectManagementQuery::convertDate($main_actual_date) ?></td>

                            <?php
                        elseif ($visit_id == '22222'): // From Randomization
                            $ezform_random = EzfQuery::getEzformOne($form['ezf_id']);
                            if (!isset($ezform_random->ezf_table)) {
                                $ezform_random = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
                            }
                            $actual_field = $form['actual_date'];
                            if ($actual_field == '') {
                                $actual_field = isset($visitSchedule['11111']['actual_date']) ? $visitSchedule['11111']['actual_date'] : 'date_visit';
                            }
                            $data = SubjectManagementQuery::GetTableData($ezform_random, ['target' => $data_id, 'visit_name' => $visit_id], 'one');

                            if (isset($actual_field)) {
                                if (isset($data[$actual_field]))
                                    $random_actual_date = $data[$actual_field];
                                else
                                    $random_actual_date = '';
                            }

                            $date = new DateTime($main_actual_date);

                            if (isset($form['plan_date']) && isset($form['earliest_date']) && isset($form['latest_date'])) {
                                $planDate = $date->modify('+' . $form['plan_date'] . ' day');
                                $planDate = $date->format('Y-m-d');

                                $date = new DateTime($planDate);
                                $earDate = $date->modify('+' . $form['earliest_date'] . ' day');
                                $earDate = $date->format('Y-m-d');
                                if(isset($form['latest_date'])&&$form['latest_date']>0){
                                    $latestDate = $date->modify('+' . $form['latest_date'] . ' day');
                                    $latestDate = $date->format('Y-m-d');
                                }
                            }
                            ?>
                            <td  ><?= SubjectManagementQuery::convertDate($random_actual_date) ?></td>
                            <td class="plan_date_column">
                                <?= SubjectManagementQuery::convertDate($planDate) ?>
                            </td>
                            <td class="earliest_date_column">
                                <?= SubjectManagementQuery::convertDate($earDate) ?>
                            </td>
                            <td class="latest_date_column">
                                <?= SubjectManagementQuery::convertDate($latestDate) ?>
                            </td>
                        <?php endif; ?>
                        <?php
                    else:
                        $form = $visitSchedule[$visit_id];
                        $ezform = EzfQuery::getEzformOne($form['ezf_id']);
                        $actual_field = $form['actual_date'];
                        $visit_field = $form['visit_name_mapping'];
                        $visit_name = $form['visit_name'];

                        if (!isset($ezform->ezf_table)) {
                            $ezform = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
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

                        $data = SubjectManagementQuery::GetTableData($ezform, ['target' => $data_id, 'visit_name' => $visit_id], 'one');

                        if (isset($actual_field)) {
                            if (isset($data[$actual_field]))
                                $actual_date = $data[$actual_field];
                            else
                                $actual_date = '';
                        }
                        if ($form['field_cal_date'] == 'actual_date')
                            $actual_cal = $dataSchedule[$form['visit_cal_date']]['actual_date'];
                        else
                            $actual_cal = $dataSchedule[$form['visit_cal_date']]['plan_date'];

                        $date = new DateTime($actual_cal);

                        if (isset($form['plan_date']) && $actual_cal != '' && isset($form['earliest_date']) && isset($form['latest_date'])) {
                            $planDate = $date->modify('+' . $form['plan_date'] . ' day');
                            $planDate = $date->format('Y-m-d');

                            $date = new DateTime($planDate);
                            $earDate = $date->modify('+' . $form['earliest_date'] . ' day');
                            $earDate = $date->format('Y-m-d');
                            $latestDate = $date->modify('+' . $form['latest_date'] . ' day');
                            $latestDate = $date->format('Y-m-d');
                        }
                        ?>
                        <td  ><?= SubjectManagementQuery::convertDate($actual_date) ?></td>
                        <td class="plan_date_column">
                            <?= SubjectManagementQuery::convertDate($planDate) ?>
                        </td>
                        <td class="earliest_date_column">
                            <?= SubjectManagementQuery::convertDate($earDate) ?>
                        </td>
                        <td class="latest_date_column">
                            <?= SubjectManagementQuery::convertDate($latestDate) ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <thead>
        </table>        
    </div>
</div>
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
                $net_total = [];
                $sectionAll = [];
                $proNoBudget = [];
                $column_total = array();
                $sumBudget = [];
                $revenue = 0;
                $expense = 0;
                $income_lumpsum = 0;
                $sectionProcedure = [];

                $i = 0;
                for ($i = 0; $i < (count($visitSchedule) + 3); $i++) {
                    $column_total[$i] = 0;
                }

                foreach ($sectionData as $sec_key => $sec_value) {
                    ?>
                    <tr>
                        <td>
                            <strong>
                                <?php
                                echo $sec_value['section_name'];
                                ?>
                            </strong>
                        </td>
                        <td align="center" colspan="<?= count($schedule_data) + count($visitSchedule) + 1 ?>">
                        </td>
                    </tr>
                    <?php
                    foreach ($procedureAll as $key => $value) {

                        $row_total = 0;
                        $val = $value['procedure_name'];
                        $procedure_by = $value['procedure_by'];

                        $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, "pro_name='{$value['id']}' AND financial_type<>'4' ", 'one');

                        $enableVisit = appxq\sdii\utils\SDUtility::string2Array($data_budget['enable_visit']);
                        $visit_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'visit_name' => $visit_id], 'one');
                        if ($data_budget['section'] == $sec_value['id']) :
                            $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id, $visit_id);

                            if ($data_subject || in_array($visit_id, $enableVisit)):
                                $approvedData = SubjectManagementQuery::GetTableData('subject_visit_approved', ['subject_target_id' => $data_id, 'visit_name' => $visit_id, 'procedure_name' => $value['id']], 'one');
                                $sectionProcedure[$sec_value['id']]['all'][$key]['id'] = $value['id'];
                                $sectionProcedure[$sec_value['id']]['all '][$key]['procedure_name'] = $val;
                                $sectionProcedure[$sec_value['id']]['all'][$key]['budget'] = $visit_budget ? $visit_budget['budget'] : $data_budget['budget'];
                                $sectionProcedure[$sec_value['id']]['all'][$key]['financial_type'] = $data_budget['financial_type'];
                                $approved = '0';
                                if ($approvedData) {
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['id'] = $value['id'];
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['procedure_name'] = $val;
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['budget'] = $visit_budget ? $visit_budget['budget'] : $data_budget['budget'];
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['financial_type'] = $data_budget['financial_type'];
                                    $approved = '1';
                                }
                                $budget = [];
                                foreach ($budget_fields as $valField):
                                    if (empty($sumBudget[$valField]))
                                        $sumBudget[$valField] = 0;
                                    $budget[$valField] = $visit_budget == false ? $data_budget[$valField] : $visit_budget[$valField];
                                    $sumBudget[$valField] += $budget[$valField];
                                endforeach;
//                                if (strpos(strtoupper($sec_value['section_name']), strtoupper('Professional fee')) !== false || strpos(strtoupper($sec_value['section_name']), strtoupper('Subject stipend')) !== false) {
//                                    $expense += $budget;
//                                }
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo $val;
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
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessBtn($module_id)) :
                                            if ($approved == '1'):
                                                ?>
                                                <label class="label label-success " style="font-size: 14px;" disabled="disabled">
                                                    <i class="fa fa-check-circle-o"></i> Approved</label>
                                            <?php else: ?>
                                                <button class="btn btn-success subject-approved" data-id="<?= $data_id ?>" data-visit_name="<?= $visit_name ?>" data-visit_id="<?= $visit_id ?>" data-visit_field="<?= $visit_field ?>" data-pro_name="<?= $value['id'] ?>">
                                                    <i class="fa fa-legal"></i> Approve</button>
                                            <?php
                                            endif;
                                        else:
                                            if ($approved == '1'):
                                                ?>
                                                <label class="label label-success " style="font-size: 14px;" disabled="disabled">
                                                    <i class="fa fa-check-circle-o"></i> Approved</label>
                                            <?php else: ?>
                                                <label class="label label-warning " style="font-size: 14px;" disabled="disabled">
                                                    <i class="fa fa-check-circle-o"></i> Not Approved</label>
                                            <?php
                                            endif;
                                        endif;
                                        ?>
                                    </td>
                                </tr>

                                <?php
                            endif;
                        else:
                            $proNoBudget[$key] = $value;
                        endif;
                    }
                }
                ?>

                <?php
                if (isset($sumBudget[$budget_fields[0]])) {
                    $revenue = isset($sumBudget[$budget_fields[0]]) ? $sumBudget[$budget_fields[0]] : '0';
                    $expense += ($sumBudget[$budget_fields[1]] + $sumBudget[$budget_fields[2]] + $sumBudget[$budget_fields[3]]);
                }
                $income_lumpsum = $revenue - $expense;
                ?>      
                <tr>
                    <td><strong>Total</strong></td>
                    <?php foreach ($budget_fields as $valField): ?>
                        <td align='center'><strong><?= number_format(isset($sumBudget[$valField]) ? $sumBudget[$valField] : '0') ?></strong></td>
                    <?php endforeach; ?>

                    <td align='center'></td>
                    <td align='center'></td>
                </tr>

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
        'subject_id' => $subject_id,
        'module_id' => $module_id,
        'data_id' => $data_id,
        'group_name' => $group_name,
        'group_id' => $group_id,
        'income_lumpsum' => $income_lumpsum,
        'total_budget' => $sumBudget,
        'visit_name' => $visit_name,
        'visit_id' => $visit_id,
        'sectionProcedure' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($sectionProcedure)),
        'procedure_id' => $options['procedure_widget_id'],
        'budget_id' => $options['budget_ezf_id'],
        'section_ezf_id' => $options['section_ezf_id'],
        'type' => 'patient',
    ])
    ?>" id="payment-inform-print" class="pull-right" style="font-size:40px;"><i class="fa fa-print"></i></a>
</div>

<div class="col-md-6 alert alert-info">
    <?php
    $budgetAdditional = SubjectManagementQuery::GetTableData($ezform_budget, 'financial_type="4" AND (visit_name="' . $visit_id . '" OR INSTR (enable_visit,\'"' . $visit_id . '"\'))');
    $addition = SubjectManagementQuery::GetTableData('subject_additional_payment', ['subject_target_id' => $data_id, 'visit_name' => $visit_id], 'all');
    $additionalList = [];
    foreach ($addition as $key => $val) {
        $additionalList[] = $val['budget_id'];
    }

    $additionalItems = [];
    foreach ($budgetAdditional as $key => $val) {
        $additionalItems[$key]['id'] = $val['id'];
        $data_pro = SubjectManagementQuery::GetTableData($ezform_procedure, ['id' => $val['pro_name']], 'one');
        $additionalItems[$key]['pro_name'] = $data_pro['procedure_name'];
    }

    echo Html::label('Additional Payment ', 'additional-payment');
    echo kartik\select2\Select2::widget([
        'name' => 'additional_payment',
        'value' => isset($additionalList) ? $additionalList : '',
        'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'),
            'id' => 'additional_payment', 'data-visit_name' => $visit_name, 'data-visit_id' => $visit_id, 'data-group_id' => $group_id
            , 'multiple' => true],
        'data' => ArrayHelper::map($additionalItems, 'id', 'pro_name'),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
</div>
<div class="clearfix"></div>

<div id="show-additional-payment">

</div>
<div class="clearfix"></div>
<br/>
<?php
echo ModalForm::widget([
    'id' => 'modal-payment-inform',
    'size' => 'modal-lg',
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
        var revenue = '<?= $revenue ?>';
        var expense = '<?= $expense ?>';
        var income_lumpsum = '<?= $income_lumpsum ?>';

        var url = '<?=
yii\helpers\Url::to(['/subjects/subject-management/subject-additional-payment',
    'options' => $options,
    'budget_fields' => $budget_fields,
])
?>';
        var showData = $(document).find('#show-additional-payment');
        var visit_name = $('#additional_payment').attr('data-visit_name');
        var group_id = $('#additional_payment').attr('data-group_id');
        var data_id = '<?= $data_id ?>';
        var visit_id = '<?= $visit_id ?>';
        var visit_field = '<?= $visit_field ?>';
        var procedure_widget_id = '<?= $procedure_widget_id ?>';
        var budget_ezf_id = '<?= $options['budget_ezf_id'] ?>';
        var budget_id = JSON.stringify($('#additional_payment').val());
        showData.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {visit_name: visit_name, visit_id: visit_id, visit_field: visit_field, data_id: data_id, budget_id: budget_id, revenue: revenue, expense: expense, income_lumpsum: income_lumpsum, group_id: group_id, procedure_widget_id: procedure_widget_id, budget_ezf_id: budget_ezf_id},
            success: function (result) {
                showData.html(result);
            }
        });
    });

    $('#additional_payment').on('change', function () {
        var revenue = '<?= $revenue ?>';
        var expense = '<?= $expense ?>';
        var income_lumpsum = '<?= $income_lumpsum ?>';

        var url = '<?=
yii\helpers\Url::to(['/subjects/subject-management/subject-additional-payment',
    'options' => $options,
    'budget_fields' => $budget_fields,
])
?>';
        var showData = $(document).find('#show-additional-payment');
        var visit_name = $(this).attr('data-visit_name');
        var visit_id = $(this).attr('data-visit_id');
        var group_id = $(this).attr('data-group_id');
        var data_id = '<?= $data_id ?>';
        var visit_id = '<?= $visit_id ?>';
        var visit_field = '<?= $visit_field ?>';
        var procedure_widget_id = '<?= $procedure_widget_id ?>';
        var budget_id = JSON.stringify($(this).val());
        var budget_ezf_id = '<?= $options['budget_ezf_id'] ?>';
        var module_id = '<?= $module_id ?>';

        showData.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {visit_name: visit_name, visit_id: visit_id, visit_field: visit_field, data_id: data_id, budget_id: budget_id, revenue: revenue, expense: expense, income_lumpsum: income_lumpsum, group_id: group_id, procedure_widget_id: procedure_widget_id, budget_ezf_id: budget_ezf_id},
            success: function (result) {
                showData.html(result);
            }
        });

        $.get('/subjects/subject-management/save-additional', {'target': data_id, visit_name: visit_name, visit_id: visit_id, 'budget_id': budget_id, budget_ezf_id: budget_ezf_id}, function (result) {
            console.log(result);
        });
    });

    $('.subject-approved').on('click', function () {

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

    $('#payment-inform-print').click(function () {
        var url = $(this).attr('data-url');
        var modal = $('#modal-payment-inform');
        modal.modal('show');
        modal.find('.modal-content').load(url);
    });
    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }

    function laterApproved() {
        var url = '<?=
yii\helpers\Url::to(['/subjects/subject-management/subject-view-data',
    'options' => $options,
    'subject_id' => $subject_id,
    'subject_payment_widget' => $subject_payment_widget,
])
?>';
        var showData = $(document).find('#show-data-table');
        var visit_id = '<?= $visit_id ?>';
        var name = '<?= $visit_name ?>';
        var field = '<?= $visit_field ?>';
        var group_name = '<?= $group_name ?>';
        var group_id = '<?= $group_id ?>';
        var data_id = '<?= $data_id ?>';
        var module_id = '<?= $module_id ?>';
        //showData.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: "get",
            type: "html",
            data: {visit_id: visit_id, name: name, data_id: data_id, field: field, group_name: group_name, group_id: group_id, module_id: module_id},
            success: function (result) {
                showData.empty();
                showData.html(result);
            }
        })
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>