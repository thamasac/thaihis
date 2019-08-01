<?php

use backend\modules\ezforms2\classes\EzfQuery;
use \appxq\sdii\helpers\SDNoty;
use appxq\sdii\widgets\ModalForm;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

$project_id = Yii::$app->request->get('data_id');
$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$schedule_data = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);

$res_owner_table = backend\modules\gantt\Module::$formsTable['res_own_ezf_id'];
$ezform_procedure = EzfQuery::getEzformOne('1520742721018881300');
$prodecureData = SubjectManagementQuery::GetTableData('pms_task_target', "(task_financial = '1') and (target = '$project_id') AND task_status=3");

$budget_pms_ezf_id = backend\modules\subjects\Module::$formsId['budget_pms_ezf_id'];
$ezform_budget = EzfQuery::getEzformOne($budget_pms_ezf_id);
$budgetData = SubjectManagementQuery::GetTableData($ezform_budget);

$ezform_section = EzfQuery::getEzformOne($options['section_ezf_id']);
$sectionData = SubjectManagementQuery::GetTableData($ezform_section);

$taskAll = [];
$taskCheck = [];
$count = 0;
$maxcolumn = 0;

foreach ($prodecureData as $key => $value) {
    $taskAll[$count]['id'] = $value['dataid'];
    $taskAll[$count]['seg_performance'] = $value['task_performance'];
    $taskAll[$count]['task_name'] = $value['task_name'];
    $taskAll[$count]['type'] = '1';
    $taskAll[$count]['credit_points'] = $value['credit_points'];
    $dataApprove = SubjectManagementQuery::GetTableData($res_owner_table, ['target' => $value['dataid']], 'one');
    $taskAll[$count]['received_points'] = $dataApprove['give_credit_points'];
    $taskCheck[] = $value['task_name'];
    $count++;
}

$budget_fields = $subject_payment_widget['budget_fields'];
?>

<div id="display-view-data">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" >
            <thead style="font-size: 16px;font-weight: bold;">
                <tr style="background-color: #003abc26;">
                    <td  style="width: 300px;text-align: center;">
                        Task Name
                    </td>
                    <td  style="width: 200px;text-align: center;">
                        Credit Points (Max/Received)
                    </td>

                    <?php
                    foreach ($budget_fields as $valField) {
                        $field_procedure = EzfQuery::getFieldByName($options['budget_ezf_id'], $valField);
                        ?>
                        <td  style="width: 300px;text-align: center;">
                            <?= $field_procedure['ezf_field_label'] ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td  style="width: 300px;text-align: center;">
                        Amount net
                    </td>
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
                $proNoBudget = [];
                $sumBudget = [];

                $sectionProcedure = [];
                $total_invoice = 0;
                $budget_net = 0;

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

                        <td align="center" colspan="<?= count($schedule_data) + 1 ?>">
                        </td>
                    </tr>
                    <?php
                    foreach ($taskAll as $key => $value) {
                        $val = $value['task_name'];
                        $credit_points = $value['credit_points'];
                        $received_points = $value['received_points'];

                        $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');

                        if ($data_budget['section'] == $sec_value['id'] && $data_budget['budget']) {

                            $proInBudget[] = $value['id'];

                            $proId = "";
                            $checktask = \backend\modules\gantt\classes\GanttQuery::getTaskStatus($value['id']);

                            if ($checktask['completed'] == '0')
                                continue;
                            if ($data_budget && $checktask['completed'] == '1') {
                                $approvedData = SubjectManagementQuery::GetTableData('subject_visit_approved', ['subject_target_id' => 'pms', 'procedure_name' => $value['id']], 'one');
                                $sectionProcedure[$sec_value['id']]['all'][$key]['id'] = $value['id'];
                                $sectionProcedure[$sec_value['id']]['all'][$key]['procedure_name'] = $val;
                                $math_money = 0;
                                $rec_percen = ($received_points / $credit_points) * 100;
                                if (isset($data_budget['budget']))
                                    $math_money = ($rec_percen / 100) * $data_budget['budget'];
                                $sectionProcedure[$sec_value['id']]['all'][$key]['budget'] = $math_money;
                                $sectionProcedure[$sec_value['id']]['all'][$key]['financial_type'] = $data_budget['financial_type'];
                                $approved = '0';
                                if ($approvedData) {
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['id'] = $value['id'];
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['procedure_name'] = $val;
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['budget'] = $math_money;
                                    $sectionProcedure[$sec_value['id']]['approved'][$key]['financial_type'] = $data_budget['financial_type'];
                                    $approved = '1';

                                    $total_invoice += $math_money;
                                }

                                $budget = [];

                                foreach ($budget_fields as $valField) {
                                    if ($checktask['actual_seg'] != '') {
                                        $seg_budget = SubjectManagementQuery::GetTableData($ezform_budget, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment =' . $checktask['actual_seg'] . ')', 'one');

                                        $budget_seg = !empty($seg_budget['budget']) ? $seg_budget['budget'] : 100;
                                        $budget_oh = !empty($data_budget['budget']) ? $data_budget['budget'] : 0;
                                        $per_budget = (((float) $budget_seg / 100) * (float) $budget_oh);
                                        if ($valField == 'budget') {
                                            $budget[$valField] = $seg_budget[$valField];
                                        } else if ($valField != 'budget') {
                                            $budget[$valField] = $seg_budget[$valField];
                                        }
                                    } else if ($checktask['actual_seg'] == '') {
                                        if (empty($sumBudget[$valField]))
                                            $sumBudget[$valField] = 0;
                                        $budget[$valField] = $data_budget[$valField];
                                        $sumBudget[$valField] += $budget[$valField];
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo '- ' . $val;
                                        ?>
                                    </td>
                                    <td align="center">
                                        <?php
                                        echo "<label>" . $credit_points . " / <span style='font-size:16px;color:green;'> " . $received_points . "</span> </label>";
                                        ?>
                                    </td>

                                    <?php
                                    for ($i = 0; $i < count($budget_fields); $i++) {
                                        if (!isset($row_total[0][$budget_fields[$i]]))
                                            $row_total[0][$budget_fields[$i]] = 0;
                                        $row_total[0][$budget_fields[$i]] += $budget[$budget_fields[$i]];
                                        $math_moneys[$i] = 0;
                                        $rec_percen = ($received_points / $credit_points) * 100;

                                        if (isset($budget[$budget_fields[$i]]))
                                            $math_moneys[$i] = $received_points * $budget[$budget_fields[$i]];
                                        ?>
                                        <td align="center">
                                            <div class="col-md-10 col-sm-offset-1">
                                                <label class=""><?= number_format($budget[$budget_fields[$i]] == '' ? 0 : $budget[$budget_fields[$i]])." x ".$received_points ?></label>
                                            </div>
                                        </td>
                                        <?php
                                    }
                                    $budget_net += $math_moneys[0];
                                    ?>
                                    <td align="center">
                                        <?php
                                        echo "<label>" . number_format($math_moneys[0], 2) . " </label>";
                                        ?>
                                    </td>    
                                    <td align="center">
                                        <div class="col-md-10 col-sm-offset-1">
                                            <?php if ($approved == '1'): ?>
                                                <label class="label label-success" style="font-size: 14px;">Done</label>
                                            <?php else: ?>
                                                <label class="label label-warning"
                                                       style="font-size: 14px;">Waiting</label>
                                                   <?php endif; ?>
                                        </div>
                                    </td>

                                    <td align="center">
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessBtn($module_id)) :
                                            if ($approved == '1'):
                                                ?>
                                                <label class="label label-success " style="font-size: 14px;"
                                                       disabled="disabled">
                                                    <i class="fa fa-check-circle-o"></i> Approved</label>
                                            <?php else: ?>
                                                <button class="btn btn-success subject-approved" data-id="pms"
                                                        data-pro_name="<?= $value['id'] ?>">
                                                    <i class="fa fa-legal"></i> Approve
                                                </button>
                                            <?php
                                            endif;
                                        else:
                                            if ($approved == '1'):
                                                ?>
                                                <label class="label label-success " style="font-size: 14px;" disabled="disabled">
                                                    <i class="fa fa-check-circle-o"></i> Approved</label>
                                            <?php else: ?>
                                                <label class="label label-warning " style="font-size: 14px;" disabled="disabled">
                                                    <i class="fa fa-exclamation-circle"></i> Not Approved</label>
                                            <?php
                                            endif;
                                        endif;
                                        ?>
                                    </td>
                                </tr>

                                <?php
                            }
                        }else {
                            $proNoBudget[$key] = $value;
                        }
                    }
                }
                ?>
                <tr>
                    <td><strong># Other</strong></td>
                    <td align="center" colspan="<?= count($schedule_data) + 1 ?>">
                    </td>
                </tr>
                <?php
                $proNoBudget = [];
                $proInBudget = [];
                $sumBudget = [];

                foreach ($taskAll as $key => $value) {
                    $credit_points = $value['credit_points'];
                    $received_points = $value['received_points'];
                    $val = $value['task_name'];
                    $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');

                    if ($data_budget['section'] == '' && $data_budget['budget']) {

                        $proInBudget[] = $value['id'];

                        $proId = "";
                        $checktask = \backend\modules\gantt\classes\GanttQuery::getTaskStatus($value['id']);

                        if ($checktask['completed'] == '0')
                            continue;
                        if ($data_budget && $checktask['completed'] == '1') {
                            $approvedData = SubjectManagementQuery::GetTableData('subject_visit_approved', ['subject_target_id' => 'pms', 'procedure_name' => $value['id']], 'one');
                            $sectionProcedure['other']['all'][$key]['id'] = $value['id'];
                            $sectionProcedure['other']['all'][$key]['procedure_name'] = $val;
                            $math_money = 0;

                            $rec_percen = ($received_points / $credit_points) * 100;
                            if (isset($data_budget['budget']))
                                $math_money = ($rec_percen / 100) * $data_budget['budget'];
                            $sectionProcedure['other']['all'][$key]['budget'] = $math_money;
                            $sectionProcedure['other']['all'][$key]['financial_type'] = $data_budget['financial_type'];
                            $approved = '0';
                            if ($approvedData) {
                                $sectionProcedure['other']['approved'][$key]['id'] = $value['id'];
                                $sectionProcedure['other']['approved'][$key]['procedure_name'] = $val;
                                $sectionProcedure['other']['approved'][$key]['budget'] = $math_money;
                                $sectionProcedure['other']['approved'][$key]['financial_type'] = $data_budget['financial_type'];
                                $total_invoice += $math_money;
                                $approved = '1';
                            }
                            $budget = [];
                            foreach ($budget_fields as $valField) {

                                if ($checktask['actual_seg'] != '') {
                                    $seg_point = $checktask['actual_seg'] == 0 ? 1 : $checktask['actual_seg'];
                                    $seg_budget = SubjectManagementQuery::GetTableData($ezform_budget, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment =' . $seg_point . ')', 'one');
                                    $budget_seg = !empty($data_budget['budget']) ? $data_budget['budget'] : 0;
                                    if ($seg_budget)
                                        $budget_seg = !empty($seg_budget['budget']) ? $seg_budget['budget'] : 100;

                                    $budget_oh = !empty($data_budget['budget']) ? $data_budget['budget'] : 0;
                                    $per_budget = (((float) $budget_seg / (float) $budget_oh) * 100 );

                                    if ($valField == 'budget') {
                                        $budget[$valField] = $budget_seg;
                                    } else if ($valField != 'budget') {
                                        $budget[$valField] = $seg_budget[$valField];
                                    }
                                } else if ($checktask['actual_seg'] == '') {
                                    if (empty($sumBudget[$valField]))
                                        $sumBudget[$valField] = 0;

                                    $budget[$valField] = $data_budget[$valField];
                                    $sumBudget[$valField] += $budget[$valField];
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    echo '- ' . $val;
                                    ?>
                                </td>
                                <td align="center">
                                    <?php
                                    echo "<label>" . $credit_points . " /  <span style='font-size:16px;color:green;'> " . $received_points . "</span> </label>";
                                    ?>
                                </td>
                                <?php
                                for ($i = 0; $i < count($budget_fields); $i++) {
                                    if (!isset($row_total[0][$budget_fields[$i]]))
                                        $row_total[0][$budget_fields[$i]] = 0;
                                    $row_total[0][$budget_fields[$i]] += $budget[$budget_fields[$i]];

                                    $math_moneys[$i] = 0;
                                    $rec_percen = ($received_points / $credit_points) * 100;

                                    if (isset($budget[$budget_fields[$i]]))
                                        $math_moneys[$i] = $received_points* $budget[$budget_fields[$i]];
                                    ?>
                                    <td align="center">
                                        <div class="col-md-10 col-sm-offset-1">
                                            <label class=""><?= number_format($budget[$budget_fields[$i]] == '' ? 0 : $budget[$budget_fields[$i]]) ." x ".$received_points?></label>
                                        </div>
                                    </td>
                                    <?php
                                }
                                $budget_net += $math_moneys[0];
                                ?>
                                <td align="center">
                                    <?php
                                    echo "<label>" . number_format($math_moneys[0], 2) . " </label>";
                                    ?>
                                </td>    
                                <td align="center">
                                    <div class="col-md-10 col-sm-offset-1">
                                        <?php if ($approved == '1'): ?>
                                            <label class="label label-success" style="font-size: 14px;">Done</label>
                                        <?php else: ?>
                                            <label class="label label-warning"
                                                   style="font-size: 14px;">Waiting</label>
                                               <?php endif; ?>
                                    </div>
                                </td>

                                <td align="center">
                                    <?php
                                    if (EzfAuthFuncManage::auth()->accessBtn($module_id)) :
                                        if ($approved == '1'):
                                            ?>
                                            <label class="label label-success " style="font-size: 14px;"
                                                   disabled="disabled">
                                                <i class="fa fa-check-circle-o"></i> Approved</label>
                                        <?php else: ?>
                                            <button class="btn btn-success subject-approved" data-id="pms"
                                                    data-pro_name="<?= $value['id'] ?>">
                                                <i class="fa fa-legal"></i> Approve
                                            </button>
                                        <?php
                                        endif;
                                    else:
                                        if ($approved == '1'):
                                            ?>
                                            <label class="label label-success " style="font-size: 14px;" disabled="disabled">
                                                <i class="fa fa-check-circle-o"></i> Approved</label>
                                        <?php else: ?>
                                            <label class="label label-warning " style="font-size: 14px;" disabled="disabled">
                                                <i class="fa fa-exclamation-circle"></i> Not Approved</label>
                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                </td>
                            </tr>

                            <?php
                        }
                    }else {
                        $proNoBudget[$key] = $value;
                    }
                }
                ?>

                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong></strong></td>
                    <?php foreach ($budget_fields as $valField): ?>
                        <td align='center'><strong><?php echo number_format(isset($row_total[0]) ? $row_total[0][$valField] : '0') ?></strong></td>
                    <?php endforeach; ?>
                        <td align='center'><strong><?php echo number_format($budget_net,2)?></strong></td>
                    <td align='center'></td>
                    <td align='center'></td>
                </tr>

            </tbody>

            <tfoot>
            </tfoot>
        </table>

    </div>

    <div class="col-md-12">
        <a href="javascript:void(0)" data-url="<?=
        \yii\helpers\Url::to([
            '/subjects/reports/payment-inform',
            'module_id' => $module_id,
//                'data_id' => $data_id,
//                'group_name' => $group_name,
//                'group_id' => $group_id,
//                'income_lumpsum' => $income_lumpsum,
            'total_budget' => $sumBudget,
            'total_invoice' => $total_invoice,
//                'visit_name' => $visit_name,
//                'visit_id' => $visit_id,
            'sectionProcedure' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($sectionProcedure)),
            'procedure_id' => $options['procedure_widget_id'],
            'budget_id' => $options['budget_ezf_id'],
            'section_ezf_id' => $options['section_ezf_id'],
            'type' => 'patient',
        ])
        ?>" id="payment-inform-print" class="pull-right" style="font-size:40px;"><i class="fa fa-print"></i></a>
    </div>

</div>

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
    $('.subject-approved').on('click', function () {

        var button = $(this);
        var data_id = $(this).attr('data-id');
        var pro_name = $(this).attr('data-pro_name');

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
            $.get('/subjects/subject-management/save-approved', {'target': data_id, 'pro_name': pro_name, 'visit_id': data_id}, function (result) {
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
yii\helpers\Url::to(['/subjects/subject-management/other-payment',
    'module_id' => $module_id,
    'options' => $options,
    'data_id' => $project_id,
    'subject_payment_widget' => $subject_payment_widget,
    'view' => 'other-payment'
])
?>';


        var showData = $(document).find('#display-view-data');
        $.ajax({
            url: url,
            method: "get",
            type: "html",

            success: function (result) {
                showData.empty();
                showData.html(result);
            }
        })
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>

































