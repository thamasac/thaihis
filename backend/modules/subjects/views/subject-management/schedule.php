<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use yii\helpers\Url;

$table_width = "100%";
if (!isset($group_name)) {
    $group_name = '';
}

if ((count($visitSchedule)) > 2)
    $table_width = $table_width + (550 * (count($visitSchedule)));
?>
<a href="javascript:void(0)" id="btn-back-group"  style="font-size: 18px;"><?= Yii::t('subjects', 'Group List') ?> </a> <label style="font-size:18px;">> <?= Yii::t('subjects', 'Group') ?> <?= $group_name ?></label> 
<br/><br/>
<div class="col-md-8 sdbox-col" style="padding-right:0;">
    <?php if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) { ?>
        <?= Html::button("<i class='fa fa-plus btn-xs'></i> " . Yii::t('subjects', 'New visit'), ['class' => 'btn btn-success pull-left btn-add-form', 'style' => 'margin-right:10px;', 'data-widget' => $widget_id,]) ?>
        <?= Html::button(" <i class='fa fa-file-excel-o'></i> " . Yii::t('ezform', 'Export visit to excel'), ['class' => 'btn btn-warning pull-left btn_export_visit_excel', 'style' => 'margin-right:10px;']); ?>
        <?= Html::button(" <i class='glyphicon glyphicon-import'></i> " . Yii::t('ezform', 'Restore visit from excel'), ['class' => 'btn btn-info pull-left btn_restore_visit_excel']); ?>

    <?php } ?>
    <div class="clearfix"></div>
</div>
<div class="col-md-4">
    <div class="col-md-4">
        <?= Html::checkbox('plan_date_check', 1, ['id' => 'plan_date_check']) ?>
        <?= Html::label(Yii::t('ezform', 'Plan Date'), 'plan_date_check') ?>

    </div>
    <div class="col-md-4">
        <?= Html::checkbox('earliest_date_check', 1, ['id' => 'earliest_date_check']) ?>
        <?= Html::label(Yii::t('ezform', 'Earliest Date'), 'earliest_date_check') ?>
    </div>
    <div class="col-md-4">
        <?= Html::checkbox('latest_date_check', 1, ['id' => 'latest_date_check']) ?>
        <?= Html::label(Yii::t('ezform', 'Latest Date'), 'latest_date_check') ?>
    </div>
</div>

<div class="clearfix"></div>
<br/>
<div id="display-content-schedule">
    <div class="table-responsive" id="table-schedule-scope">
        <div class="" id="content-table">
            <table class="table table-bordered table-striped table-schedule" id="table-schedule" style="width:<?= $table_width ?>px;">
                <thead style="font-size: 16px;font-weight: bold;">
                    <tr>
                        <td  rowspan="2" style="width: 170px;text-align: center;">Screening Number</td>
                        <td  rowspan="2" style="width: 170px;text-align: center;">Subject Number</td>
                        <?php
                        $count = 1;

                        foreach ($visitSchedule as $key => $value) {
                            if ($value['id'] == '11111' || $value['id'] == '22222') {
                                if (isset($value['ezf_id']))
                                    $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                                $form_name = $value['visit_name'];

                                if ($form_name == '') {
                                    $form_name = $ezform->ezf_name;
                                }
                                ?>

                                <td class="main-head" colspan="<?= $value['id'] == '22222' ? '4' : '' ?>" style="text-align: center;min-width:250px;">
                                    <label class="label label-default pull-left"><?= $count ?></label> <?= $form_name ?>
                                    <?php
                                    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
                                        echo Html::button("<i class='glyphicon glyphicon-pencil'></i>", [
                                            'class' => 'btn btn-primary  btn-xs pull-right btn-edit-form',
                                            'style' => 'margin-left:5px;',
                                            'data-data_id' => $value['id'],
                                            'data-ezf_id' => $ezform->ezf_id,
                                            'data-widget' => $widget_id,
                                            'data-key_index' => $key,
                                        ]) . ' ';
                                        echo Html::button("<i class='glyphicon glyphicon-trash'></i>", [
                                            'class' => 'btn btn-danger  btn-xs  pull-right btn-delete-visit',
                                            'data-visit_id' => $value['id'],
                                            'data-ezf_id' => $ezform->ezf_id,
                                            'data-widget' => $widget_id,
                                            'data-key_index' => $key,
                                        ]) . ' ';
                                    }
                                    ?>
                                </td>

                                <?php
                                $count++;
                            } else {
                                if ($value['ezf_id'] != '')
                                    $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                                $form_name = $value['visit_name'];

                                if ($form_name == '' && $ezform->ezf_name != null) {
                                    $form_name = $ezform->ezf_name;
                                }
                                ?>
                                <td class="main-head" colspan="4" style="text-align: center;min-width:350px;">
                                    <label class="label label-default pull-left"><?= $count ?></label> <?= $form_name ?>
                                    <?php
                                    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
                                        echo Html::button("<i class='glyphicon glyphicon-pencil'></i>", [
                                            'class' => 'btn btn-primary  btn-xs pull-right btn-edit-form',
                                            'style' => 'margin-left:5px;',
                                            'data-data_id' => $value['id'],
                                            'data-ezf_id' => $ezform->ezf_id,
                                            'data-widget' => $widget_id,
                                            'data-key_index' => $key,
                                        ]) . ' ';
                                        echo Html::button("<i class='glyphicon glyphicon-trash'></i>", [
                                            'class' => 'btn btn-danger  btn-xs  pull-right btn-delete-form',
                                            'data-data_id' => $value['id'],
                                            'data-ezf_id' => $ezform->ezf_id,
                                            'data-widget' => $widget_id,
                                            'data-key_index' => $key,
                                        ]) . ' ';
                                    }
                                    ?>
                                </td>
                                <?php
                                $count++;
                            }
                        }
                        ?>

                    </tr>
                    <tr>

                        <?php foreach ($visitSchedule as $key => $value) :
                            ?>

                            <?php if ($value['id'] == '11111'): ?>
                                <td >Actual Date</td>
                            <?php else: ?>
                                <td >Actual Date</td>
                                <td class="plan_date_column">Plan Date</td>
                                <td class="earliest_date_column">Earliest Date</td>
                                <td class="latest_date_column">Latest Date</td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($options['subject_ezf_id']))
                        $subjectForm = EzfQuery::getEzformOne($options['subject_ezf_id']);
                    if (isset($options['11111']['main_ezf_id']))
                        $ezform_main = EzfQuery::getEzformOne($options['11111']['main_ezf_id']);
                    if (isset($options['22222']['random_ezf_id']))
                        $form_random = EzfQuery::getEzformOne($options['22222']['random_ezf_id']);
                    $fieldDisplay = $options['subject_field'];
                    $data = [];

                    $field_visit = isset($options['11111']['main_visit_name']) ? $options['11111']['main_visit_name'] : 'visit_name';
                    $visit_name = $options['22222']['form_name'];
                    if ($field_visit == '')
                        $field_visit = 'visit_name';
                    $data = SubjectManagementQuery::GetScheduleActivity($subjectForm, $ezform_main->ezf_table, $fieldDisplay, $ezform_main->ezf_table . '.' . $field_visit . '="22222" AND ' . $ezform_main->ezf_table . '.group_name="' . $group_id . '"', $pageStart, $pageLimit);

                    $actual_date = '';
                    $random_actual_date = '';
                    $actual_date_list = [];
                    $plan_date_list = [];
                    ?>
                    <?php
                    foreach ($data as $key => $value):
                        ?>
                        <tr>
                            <td style="text-align: right;">
                                <?php
                                $varActual = isset($options['22222']['main_actual_date']) ? $options['22222']['main_actual_date'] : '';
                                if ($varActual == '')
                                    $varActual = isset($options['11111']['main_actual_date']) ? $options['11111']['main_actual_date'] : '';
                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
                                    if ($options['individual_widget_id'] != null) {
                                        echo Html::a($value[$fieldDisplay], '#', [
                                            'class' => 'schedule-drilldown',
                                            'data-dataid' => $value['target'],
                                            'subject_number' => $value[$fieldDisplay],
                                            'data-actual_date' => isset($value[$varActual]) ? $value[$varActual] : '',
                                        ]);
                                    } else {
                                        echo Html::label($value[$fieldDisplay]);
                                    }
                                } else {
                                    echo Html::label($value[$fieldDisplay]);
                                }
                                ?>

                            </td>
                            <td style="text-align: right;">
                                <?=$value['subject_no']?>
                            </td>
                            <?php
                            // form ที่ถูกเพิ่มจาก visit schedule

                            foreach ($visitSchedule as $key2 => $value2) :
                                $visit_field = $value2['visit_name_mapping'];
                                if ($visit_field == '')
                                    $visit_field = "visit_name";
                                if ($value2['id'] == '11111'): // Form Screen

                                    if (isset($form_random->ezf_table))
                                        $patient = SubjectManagementQuery::GetScheduleActivity($subjectForm, $form_random->ezf_table, $fieldDisplay, $visit_field . '="' . $value2['id'] . '" AND ' . $form_random->ezf_table . '.target' . '="' . $value['target'] . '"', null, null, 'one');


                                    if ($patient) {
                                        $actual_field = isset($value2['actual_date']) ? $value2['actual_date'] : 'date_visit';
                                        $main_actual_date = $patient[$actual_field];
                                        $date = new DateTime($main_actual_date);
//                                    if (isset($value2['main_earliest_distance']) && isset($value2['main_latest_distance'])) {
//                                        $earDate = $date->modify('+' . $value2['main_earliest_distance'] . ' day');
//                                        $earDate = $date->format('Y-m-d');
//                                        $latestDate = $date->modify('+' . $value2['main_latest_distance'] . ' day');
//                                        $latestDate = $date->format('Y-m-d');
//                                    }
                                    }
                                    ?>
                                    <td><?= SubjectManagementQuery::convertDate($main_actual_date) ?></td>

                                <?php elseif ($value2['id'] == '22222'): // From Randomization
                                    $actual_field = isset($value2['actual_date']) ? $value2['actual_date'] : 'date_visit';
                                    if (isset($value2['actual_date']) || $value2['actual_date'] == '')
                                        $value2['actual_date'] = $visitSchedule['11111']['actual_date'];

                                    if (!isset($value2['visit_name_mapping']) || $value2['visit_name_mapping'] == '')
                                        $value2['visit_name_mapping'] = $visitSchedule['11111']['visit_name_mapping'];

                                    if (isset($form_random->ezf_table))
                                        $patient = SubjectManagementQuery::GetScheduleActivity($subjectForm, $form_random->ezf_table, $fieldDisplay, $visit_field . '="' . $value2['id'] . '" AND ' . $form_random->ezf_table . '.target' . '="' . $value['target'] . '"', null, null, 'one');

                                    if (isset($patient[$actual_field]))
                                        $random_actual_date = $patient[$actual_field];
                                    else
                                        $random_actual_date = '';


                                    $date = new DateTime($main_actual_date);

                                    $earDate = null;
                                    $latestDate = null;
                                    if (isset($value2['plan_date']) && isset($value2['earliest_date']) && isset($value2['latest_date'])) {
                                        $planDate = $date->modify('+ ' . $value2['plan_date'] . ' day');
                                        $planDate = $date->format('Y-m-d');
                                        $actual_plan = new DateTime($planDate);
                                        $earDate = $actual_plan->modify(' ' . $value2['earliest_date'] . ' day');
                                        $earDate = $actual_plan->format('Y-m-d');
                                        $actual_plan = new DateTime($planDate);
                                        if (isset($value2['latest_date']) && $value2['latest_date'] > 0) {
                                            $latestDate = $actual_plan->modify('+ ' . $value2['latest_date'] . ' day');
                                            $latestDate = $actual_plan->format('Y-m-d');
                                        }
                                    }

                                    $actual_date_list[$value2['id']]['actual_date'] = $random_actual_date;
                                    $actual_date_list[$value2['id']]['plan_date'] = $planDate;
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
                                    <?php
                                else:
                                    $earDate = null;
                                    $planDate = null;
                                    $latestDate = null;
                                    $color_status = "";

                                    $ezform = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
                                    $patient = null;
                                    if (!isset($value2['visit_name_mapping']) || $value2['visit_name_mapping'] == '')
                                        $value2['visit_name_mapping'] = $visitSchedule['11111']['visit_name_mapping'];
                                    if (!isset($value2['actual_date']) || $value2['actual_date'] == '')
                                        $value2['actual_date'] = $visitSchedule['11111']['actual_date'];
                                    if (isset($ezform->ezf_table))
                                        $patient = SubjectManagementQuery::GetScheduleActivity($subjectForm, $ezform->ezf_table, $fieldDisplay, $visit_field . '="' . $value2['id'] . '" AND ' . $form_random->ezf_table . '.target' . '="' . $value['target'] . '"', null, null, 'one');


                                    $actual_field = isset($value2['actual_date']) ? $value2['actual_date'] : 'date_visit';
                                    if ($patient)
                                        $actual_date = $patient[$actual_field];
                                    else
                                        $actual_date = '';

                                    $planDistance = "";
                                    if (isset($actual_date_list[$value2['visit_cal_date']]) && $value2['field_cal_date'] == 'actual_date') {
                                        $planDistance = $actual_date_list[$value2['visit_cal_date']]['actual_date'];
                                    } elseif (isset($actual_date_list[$value2['visit_cal_date']]) & $value2['field_cal_date'] == 'plan_date') {
                                        $planDistance = $actual_date_list[$value2['visit_cal_date']]['plan_date'];
                                    }

                                    if (isset($planDistance) && $planDistance != '') {
                                        $date = new DateTime($planDistance);
                                        if (isset($value2['plan_date'])) {
                                            $planDateMo = $date->modify('+' . $value2['plan_date'] . ' day');
                                            $planDate = $planDateMo->format('Y-m-d');
                                        }
                                    }

                                    if (isset($value2['earliest_date']) && $value2['earliest_date'] != '' && $planDate != null) {
                                        $actual_plan = new DateTime($planDate);
                                        $earDateMo = $actual_plan->modify('+' . $value2['earliest_date'] . ' day');
                                        $earDate = $actual_plan->format('Y-m-d');
                                    }
                                    if (isset($value2['latest_date']) && $value2['latest_date'] != '' && $planDate != null) {
                                        $actual_plan = new DateTime($planDate);
                                        $latestDateMo = $actual_plan->modify('+' . $value2['latest_date'] . ' day');
                                        $latestDate = $actual_plan->format('Y-m-d');
                                    }

                                    $actual_date_list[$value2['id']]['actual_date'] = $actual_date;
                                    $actual_date_list[$value2['id']]['plan_date'] = $planDate;
                                    if (date($actual_date) > date($latestDate)) {
                                        $color_status = "#F5A9A9";
                                    } elseif (date('Y-m-d') <= date($latestDate) && date('Y-m-d') >= date($earDate)) {
                                        $color_status = "#F5F6CE";
                                    } elseif (date('Y-m-d') > date($latestDate) && $actual_date == '') {
                                        $color_status = "#F5A9A9";
                                    }
                                    if ($planDistance == '' || $planDistance == null)
                                        $color_status = "#d9d9d9";
                                    ?>

                                    <td style="background-color: <?= $color_status ?>"><?= SubjectManagementQuery::convertDate($actual_date) ?></td>
                                    <td  style="background-color: <?= $color_status ?>;" class="plan_date_column">
                                        <?= SubjectManagementQuery::convertDate($planDate) ?>
                                    </td>

                                    <td  style="background-color: <?= $color_status ?>" class="earliest_date_column">
                                        <?= SubjectManagementQuery::convertDate($earDate) ?>
                                    </td>
                                    <td  style="background-color: <?= $color_status ?>" class="latest_date_column">
                                        <?= SubjectManagementQuery::convertDate($latestDate) ?>
                                    </td>
                                <?php
                                endif;
                            endforeach;
                            //\appxq\sdii\utils\VarDumper::dump($pageLimit);
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            echo $this->renderAjax('../pagination/view-paging', [
                'thisPage' => $thisPage,
                'pageLimit' => $pageLimit,
                'pageAmt' => $pageAmt,
                'reloadDiv' => 'display-schedule',
            ]);
            ?>

        </div>
    </div>
</div>
<div class="backgroud_blur" style="background-color: black">

</div>

<div class="modal" id="modal-gantt-individual" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 95%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gantt Chart Schedule Individual</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-ezform-gantt',
    'size' => 'modal-xl',
]);
?>
<?=
ModalForm::widget([
    'id' => 'modal-create-ezform',
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

    $(function () {
        var mx = 0;
        $("#table-schedule-scope").on({
            mousemove: function (e) {
                var mx2 = e.pageX - this.offsetLeft;
                if (mx)
                    this.scrollLeft = this.sx + mx - mx2;
            },
            mousedown: function (e) {
                this.sx = this.scrollLeft;
                mx = e.pageX - this.offsetLeft;
            }
        });
        $(document).on("mouseup", function () {
            mx = 0;
        });
        var url = $('#display-schedule').attr('data-url') + '&group_id=<?= $group_id ?>';
        $('#display-schedule').attr('data-url', url);

    });

    $('.btn-add-form').click(function () {
        var widget = $(this).attr('data-widget');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => 'display-schedule',
    'options' => $options,
    'group_name' => $group_name,
    'group_id' => $group_id,
    'widget_id' => $widget_id,
])
?>';

        $('#modal-ezform-config .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url + '&widget_id=' + widget);
    });

    $('.btn-delete-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var key_index = $(this).attr('data-key_index');
        var widget_id = $(this).attr('data-widget');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/delete-visit',
    'reloadDiv' => 'display-schedule',
    'group_name' => $group_name,
    'group_id' => $group_id,
    'widget_id' => $widget_id,
    'module_id' => $module_id,
])
?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.get(url, {key_index: key_index, widget_id: widget_id, ezf_id: ezf_id, data_id: data_id}
            ).done(function (result) {
                if (result.status == 'success') {
                    var url = $('#display-schedule').attr('data-url') + '&group_name=<?= $group_name ?>';
                    getReloadDiv(url, 'display-schedule');
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
            });
        });
    });

    $('#plan_date_check').on('change', function () {
        if ($('#plan_date_check').is(':checked')) {
            $('.plan_date_column').each(function (i, e) {
                $(e).css('display', '');
            })

            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span + 1));
            })
            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth + 500) + 'px')
        } else {
            $('.plan_date_column').each(function (i, e) {
                $(e).css('display', 'none');
            })
            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span - 1));
            })

            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth - 500) + 'px')
        }
    });

    $('#earliest_date_check').on('change', function () {
        if ($('#earliest_date_check').is(':checked')) {
            $('.earliest_date_column').each(function (i, e) {
                $(e).css('display', '');
            })
            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span + 1));
            })
            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth + 500) + 'px')
        } else {
            $('.earliest_date_column').each(function (i, e) {
                $(e).css('display', 'none');
            })
            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span - 1));
            })
            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth - 500) + 'px')
        }
    });

    $('#latest_date_check').on('change', function () {
        if ($('#latest_date_check').is(':checked')) {
            $('.latest_date_column').each(function (i, e) {
                $(e).css('display', '');
            })
            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span + 1));
            })
            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth + 500) + 'px')
        } else {
            $('.latest_date_column').each(function (i, e) {

                $(e).css('display', 'none');
            })
            $('.main-head').each(function (i, e) {
                var span = $(e).prop('colSpan');
                $(e).prop('colSpan', (span - 1));
            })
            var tableWidth = $('.table-schedule').width();
            $('.table-schedule').css('width', (tableWidth - 500) + 'px')
        }
    });

    $('.schedule-drilldown').on('click', function () {
        var modalGantt = $('#modal-gantt-individual');
        var data_id = $(this).attr('data-dataid');
        var data_ptid = $(this).attr('data-ptid');
        subject_number
        var subject_number = $(this).attr('subject_number');
        var group_id = '<?= $group_id ?>';
        modalGantt.modal('show');
        modalGantt.find('.modal-content .modal-body').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var data = {widget_id: '<?= $options['individual_widget_id'] ?>', schedule_id: '<?= $widget_id ?>', subject_number: subject_number, skinName: 'default', data_id: data_id, data_ptid: data_ptid, group_id: group_id};
        $.get('/gantt/gantt/gantt-individual', data, function (result) {
            modalGantt.find('.modal-content .modal-body').empty();
            modalGantt.find('.modal-content .modal-body').html(result);
        })
    })

    $('.btn-edit-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var key_index = $(this).attr('data-key_index');
        var widget = $(this).attr('data-widget');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => 'display-schedule',
    'options' => $options,
    'group_name' => $group_name,
    'group_id' => $group_id,
    'widget_id' => $widget_id,
    'module_id' => $module_id,
])
?>';
        url += '&key_index=' + key_index + '&widget_id=' + widget + '&ezf_id=' + ezf_id + '&data_id=' + data_id;

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });
    $('.btn_restore_visit_excel').click(function () {
        var url = '<?=
\yii\helpers\Url::to(["/subjects/subject-management/visit-import-excel"
    , 'group_id' => $group_id, 'options' => $options, 'widget_id' => $widget_id, 'module_id' => $module_id,
    'schedule_id' => $schedule_id, 'procedure_id' => $procedure_id,])
?>';
        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }

    $('#modal-ezform-gantt').on('hidden.bs.modal', function (e) {
        $(document).find('body').addClass('modal-open');
    });

//    $(document).on('click', '.ezform-create', function () {
//        var data_modal = $(this).attr('data-modal');
//        var url = $(this).attr('data-url');
//        $('#' + data_modal).find('modal-content').load(url);
//    });

    $('.btn-delete-visit').click(function () {
        var visit_id = $(this).attr('data-visit_id');
        var widget_id = '<?= $widget_id ?>';
        var enable_visit = '0';
        var url_ena = "/subjects/subject-management/enabled-visit";
        var data = {visit_id: visit_id, widget_id: widget_id, enable_visit: enable_visit};
        var url = $('#display-schedule').attr('data-url') + '&group_name=<?= $group_name ?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
            $.get(url_ena, data, function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    getReloadDiv(url, 'display-schedule');
                } else {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
                }
            });

        });

    });

    $('.btn_export_visit_excel').click(function () {
        var group_name = "";
        var group_id = "";

        var url = '<?=
Url::to(['/subjects/subject-management/export-visit-schedule',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'module_id' => $module_id,
    'group_id' => $group_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
    'export' => true,
]);
?>'
        $.get(url, {}, function (result) {
            var data = JSON.parse(result);
<?= SDNoty::show('data.message', 'data.status') ?>;
            $('#modal-export .modal-content').html(data.html);
            $('#modal-export').modal('hide');
        });
    });

    $('#btn-back-group').click(function () {
        window.history.replaceState({}, '', $('#show-schedule').attr('data-urlgroup'));
        getReloadDiv($('#show-schedule').attr('data-url'), 'show-schedule');
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
