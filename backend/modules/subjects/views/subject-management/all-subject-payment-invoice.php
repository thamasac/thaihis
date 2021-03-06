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

$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);
$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);

$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$scheduleOptions = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);

if ($group_id == "0") {
    $firstGroup = SubjectManagementQuery::getFirstGroup($scheduleOptions['group_ezf_id']);
    $group_id = isset($firstGroup['id']) ? $firstGroup['id'] : '0';
}

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);
$table_width = "100%";

$ezform_procedure = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure);

if ((count($visitSchedule)) > 2) {
    $table_width = "250";
    $table_width = $table_width + (250 * ((count($visitSchedule)) - 1));
}


$procedureAll = [];
$procedureCheck = [];
$count = 0;
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

$subDisplay = $scheduleOptions['subject_field'];
$detail_form = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
$data_json = [];
?>
<style>
    .active-select{
        background-color: #fbf069;
    } 
</style>
<div class="modal-body">
    <div class="col-md-12 row">
        <div class="col-md-4">
            <?= Html::label(Yii::t('subjects', 'Start Date'), 'start_date') ?>
            <?=
            \kartik\date\DatePicker::widget([
                'name' => 'start_date',
                'id' => 'start_date',
                'value' => '',
            ])
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('subjects', 'End Date'), 'end_date') ?>
            <?=
            \kartik\date\DatePicker::widget([
                'name' => 'end_date',
                'id' => 'end_date',
                'value' => '',
            ])
            ?>
        </div>
        <div class="col-md-1 sdbox-col" style="margin-top:25px;">
            <?= Html::button(Yii::t('subjects', Yii::t('subjects', 'Preview')), ['class' => 'btn btn-primary btn-search']) ?>
        </div>
        <?=
        Html::a('<i class="fa fa-print"></i>' . Yii::t('subjects', ''), Url::to([
                    '/subjects/reports/print-invoice-request',
                    'profile_ezf_id' => $scheduleOptions['subject_ezf_id'],
                    'detail_ezf_id' => $scheduleOptions['11111']['main_ezf_id'],
                    'schedule_id' => $options['schedule_widget_id'],
                    'procedure_id' => $options['procedure_widget_id'],
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'budget_id' => $options['budget_ezf_id'],
                ]), [
            'style' => 'font-size:35px;', 'class' => 'print-all-subject pull-right',
            'target' => '_blank',
        ])
        ?>
    </div>
    <div class="clearfix"></div>
    <br/>
    <?php if (isset($start_date) || $start_date != null): ?>
        <label>ข้อมูล ณ วันที่ <?= SubjectManagementQuery::convertDate($start_date, 'full') ?> ถึง <?= SubjectManagementQuery::convertDate($end_date, 'full') ?></label>
    <?php endif; ?>

    <div class="clearfix"></div>
    <div class="config_before_print">
        <div class="row" >
            <div class="col-md-3">
                <?= Html::label("Customer Name : ", 'customer_name') ?>
                <?= Html::textInput('customer_name', '', ['class' => 'form-control', 'id' => 'customer_name']) ?>
            </div>
            <div class="col-md-3">
                <?= Html::label("Invoice No : ", 'invoice_no') ?>
                <?= Html::textInput('invoice_no', '', ['class' => 'form-control', 'id' => 'invoice_no']) ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row" >
            <div class="col-md-6">
                <?= Html::label("Address : ", 'address') ?>
                <?= Html::textarea('address', '', ['class' => 'form-control', 'id' => 'address']) ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <br/>
    </div>
    <div id="display-visit-data">
        <div class="table-responsive" id="table-allsubject-scope">
            <table class="table table-bordered table-striped" style="width:<?= $table_width ?>px;">
                <thead style="font-size: 16px;font-weight: bold;">
                    <tr>
                        <td  style="width: 200px;text-align: center;">
                            Screening Number.
                        </td>
                        <td  style="width: 200px;text-align: center;">
                            Subject Number.
                        </td>

                        <?php
                        $col_count = 0;
                        foreach ($visitSchedule as $key => $value) {
                            $col_count += 1;
                            $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                            $form_name = isset($value['visit_name']) ? $value['visit_name'] : $visitSchedule['11111']['visit_name'];
                            if ($form_name == '') {
                                $form_name = $ezform->ezf_name;
                            }
                            ?>
                            <td  style="text-align: center;"><?= $form_name ?>
                            </td>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 0;
                    $check_visit = false;
                    foreach ($data as $key => $value) {
                        $val = $value[$subDisplay];
                        $target = $value['target'];
                        if ($group_id == '' || $group_id == '0') {
                            $group = SubjectManagementQuery::getGroupByTarget($detail_form, $value['id']);
                            if ($group) {
                                $group_id = $group['group_name'];
                                $visit_group = GanttQuery::findArraybyFieldName($visitSchedule, $group_id, 'group_name');
                                if ($visit_group)
                                    $check_visit = true;
                            }else {
                                $firstGroup = SubjectManagementQuery::getFirstGroup($scheduleOptions['group_ezf_id']);
                                $group_id = isset($firstGroup['id']) ? $firstGroup['id'] : '0';
                            }
                        } else {
                            $check_visit = true;
                        }
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo "<strong>" . $val . "</strong>";
                                ?>
                            </td>
                            <td>
                                <?php
                                echo "<strong>" . $value['subject_no1'] . "</strong>";
                                ?>
                            </td>

                            <?php
                            $checkAll = true;

                            $main_actual_date = "";
                            $random_actual_date = "";
                            $display_date = "";
                            $subjectApproved = [];
                            $main_form = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
                            foreach ($visitSchedule as $key2 => $value2) :
                                $sumBudget = 0;
                                if ($value2['id'] == '11111' || $value2['id'] == '22222')
                                    $check_visit = true;
                                
                                if ($check_visit == true) {
                                    $index ++;
                                    $visit_name = $value2['visit_name'];
                                    $visit_id = $value2['id'];
                                    $visit_field = isset($visitSchedule['11111']['visit_name_mapping']) ? $visitSchedule['11111']['visit_name_mapping'] : 'visit_name';
                                    $actual_field = $value2['actual_date'];
                                    if ($actual_field == null)
                                        $actual_field = isset($visitSchedule['11111']['actual_date']) ? $visitSchedule['11111']['actual_date'] : 'date_visit';

                                    if (isset($start_date) && $start_date != null) {
                                        $dataSubject = SubjectManagementQuery::GetTableData($main_form, " $actual_field BETWEEN '$start_date' AND '$end_date' AND target='$target' AND $visit_field='$visit_id'", 'one');
                                    } else {
                                        $dataSubject = SubjectManagementQuery::GetTableData($main_form, ['target' => $target, $visit_field => $visit_id], 'one');
                                    }

                                    $status = "";
                                    $actual_date = $dataSubject[$actual_field];

                                    $subjectList = [];
                                    $data_subject = SubjectManagementQuery::getVisitProcedure($ezform_budget, $visit_id, $group_id);
                                    //\appxq\sdii\utils\VarDumper::dump($data_subject);
                                    $approved = 0;
                                    $approvedList = [];
                                    $additionalApp = [];
                                    $approvedList = SubjectManagementQuery::getVisitProcedureApproved($visit_id, $group_id, $target);
                                    $additionalList = SubjectManagementQuery::getVisitProcedureAddition($visit_id, $group_id, $target);
                                    $sumBudget = 0;

                                    foreach ($data_subject as $keySub => $valSub) {
                                        $budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $valSub['procedure_name'], 'visit_name' => $visit_id, 'group_name' => $group_id], 'one');

                                        if (!$budget)
                                            $budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $valSub['procedure_name'], 'group_name' => $group_id], 'one');
                                        $sumBudget += $budget['budget'];
                                    }
                                    foreach ($additionalList as $key => $val) {
                                        $addapp = SubjectManagementQuery::GetTableData('subject_visit_approved', ['visit_name' => $visit_id, 'procedure_name' => $val['procedure_name'], 'subject_target_id' => $target], 'one');
                                        if ($addapp)
                                            $additionalApp[] = $val;
                                    }
                                    if (isset($approvedList) && (count($approvedList) + count($additionalApp)) >= (count($data_subject) + count($additionalList))) {
                                        $approved = 1;
                                    }

                                    if ($actual_date != '' && $approved == '1' && count($data_subject) > 0) {
                                        $display_date = $actual_date;
                                        $status = "success";
                                    } else if ($actual_date != '') {
                                        $display_date = $actual_date;
                                        $status = "waiting";
                                    } else if ($actual_date == '') {
                                        if (isset($random_actual_date) && $random_actual_date != '') {
                                            $date = new DateTime($random_actual_date);
                                            if (isset($value2['plan_date'])) {
                                                $date->modify('+' . $value2['plan_date'] . ' day');
                                                $random_actual_date = $date->format('Y-m-d');
                                                $display_date = $random_actual_date;
                                            }
                                        }
                                        $status = "noting";
                                    }
                                }


                                if ($status == 'waiting') {
                                    $color = "background-color:#fff0b3;";
                                } else if ($status == 'success') {
                                    $color = "background-color:#5bd75b;";
                                } else {
                                    $color = "background-color:#f2f2f2;";
                                }
                                ?>
                                <td align="center" style="<?= $color ?>">
                                    <?php if ($check_visit == true && $status != "noting"): ?>
                                        <label><?= SubjectManagementQuery::convertDate($display_date) ?></label><br/>
                                        <label class="label label-info" style="font-size: 16px;"><?= number_format($sumBudget) ?></label>
                                        <?php if ($status == 'success') : ?>
                                            <?=
                                            Html::a('<i class="fa fa-print"></i>' . Yii::t('subjects', ''), Url::to([
                                                        '/subjects/reports/print-invoice-request',
                                                        'print_visit' => ($index - 1),
                                                        'profile_ezf_id' => $scheduleOptions['subject_ezf_id'],
                                                        'detail_ezf_id' => $scheduleOptions['11111']['main_ezf_id'],
                                                        'schedule_id' => $options['schedule_widget_id'],
                                                        'procedure_id' => $options['procedure_widget_id'],
                                                        'group_name' => $group_name,
                                                        'group_id' => $group_id,
                                                        'budget_id' => $options['budget_ezf_id'],
                                                    ]), [
                                                'style' => 'font-size:25px;', 'class' => 'print-single-visit ',
                                                'target' => '_blank',
                                            ])
                                            ?>
                                        <?php endif; ?><br/>
                                    <?php endif; ?>
                                </td>
                                <?php
                            endforeach;
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            echo $this->renderAjax('../pagination/view-paging', [
                'thisPage' => $thisPage,
                'pageLimit' => $pageLimit,
                'pageAmt' => $pageAmt,
                'reloadDiv' => 'display-allsubject',
            ]);
            ?>
        </div>
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
    $(function () {
        var mx = 0;
        $("#table-allsubject-scope").on({
            mousemove: function (e) {
                var mx2 = e.pageX - this.offsetLeft;
                if (mx)
                    this.scrollLeft = this.sx + mx - mx2;
            },
            mousedown: function (e) {
                this.sx = this.scrollLeft;
                mx = e.pageX - this.offsetLeft;
            },
            mouseup: function (e) {
                mx = 0;
            }
        });
        $(document).on("mouseup", function () {
            mx = 0;
        });
        var url = $('.print-all-subject').attr('href');

        $('.print-all-subject').attr('href', url + '&dat_json=<?= base64_encode(json_encode($data_json)) ?>')
        $('.print-single-visit').each(function (i, e) {
            var url2 = $(e).attr('href');
            $(e).attr('href', url2 + '&dat_json=<?= base64_encode(json_encode($data_json)) ?>')
        });
    });

    $('.print-all-subject').click(function () {
        var url = $(this).attr('href');
        var customer_name = $('#customer_name').val();
        var invoice_no = $('#invoice_no').val();
        var address = $('#address').val();

        $(this).attr('href', url + '&customer_name=' + customer_name + '&invoice_no=' + invoice_no + '&address=' + address + '')
    });

    $('.print-single-visit').click(function () {
        var url = $(this).attr('href');
        var customer_name = $('#customer_name').val();
        var invoice_no = $('#invoice_no').val();
        var address = $('#address').val();

        $(this).attr('href', url + '&customer_name=' + customer_name + '&invoice_no=' + invoice_no + '&address=' + address + '')
    });

    $('.btn-search').click(function () {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var url = '<?=
Url::to(['/subjects/subject-management/group-subject-payment',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'options' => $options,
    'view' => 'group-subject-payment-invoice',
])
?>';
        $('#modal_tracking_invoice').modal('show');
        $('#modal_tracking_invoice').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal_tracking_invoice').find('.modal-content').load(url + '&start_date=' + start_date + '&end_date=' + end_date);
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>


