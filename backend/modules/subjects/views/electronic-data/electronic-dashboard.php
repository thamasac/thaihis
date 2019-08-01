<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\gantt\classes\GanttQuery;

if (!isset($group_id))
    $group_id = null;

$table_width = "100";
if (!isset($group_name)) {
    $group_name = '';
}

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget2($schedule_id, $group_id);
$visitSchedule = appxq\sdii\utils\SDUtility::string2Array($visitSchedule);
if (!isset($user_create))
    $user_create = \Yii::$app->user->id;
if ((count($visitSchedule)) > 2)
    $table_width = $table_width + (300 * (count($visitSchedule) ));

$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);

$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$procedureForm = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);
$user_id = Yii::$app->user->id;
?>
<div class="clearfix"></div>
<br/>

<div id="display-schedule">
    <div class="pull-left">
        <label class="label label-default">All Forms</label>
        <label class="label label-success">Completed</label>
        <label class="label label-warning">Waiting</label>
        <label class="label label-danger">Not Process</label>
    </div>
    <!--            <div class="pull-right">
    <?= Html::button("<i class='fa fa-download'></i> Export data of CRFs", ['id' => 'btn_export_crf', 'class' => 'btn btn-info']) ?>
                </div>-->
    <br/><br/>
    <div class="table-responsive" id="table-schedule-scope">
        <div class="" id="content-table">

            <table class="table table-bordered table-striped table-schedule" id="table-schedule" style="width:<?= $table_width ?>px;">
                <thead style="font-size: 16px;font-weight: bold;">
                    <tr>
                        <td style="width: 170px;text-align: center;">Screening Number.</td>
                        <td style="width: 170px;text-align: center;">Subject Number.</td>

                        <?php
                        $count = 1;
                        foreach ($visitSchedule as $key => $value) {
                            //\appxq\sdii\utils\VarDumper::dump($value);
                            $ezform = null;
                            if (isset($value['ezf_id']))
                                $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                            $form_name = $value['visit_name'];

                            if ($form_name == '') {
                                $form_name = $ezform['ezf_name'];
                            }
                            ?>
                            <td class="main-head" style="text-align: center;max-width:250px;min-width:200px;">
                                <?= $form_name ?>

                            </td>
                            <?php
                            $count++;
                        }
                        ?>

                    </tr>

                </thead>
                <tbody>
                    <?php
                    $allforms = [];
                    $actual_date = '';
                    $random_actual_date = '';
                    $actual_date_list = [];
                    $plan_date_list = [];
                    $dataProcedure = SubjectManagementQuery::GetTableData($procedureForm);
                    $visitProcedure = [];
                    if ($group_id != '') {
                        $visitProcedure = SubjectManagementQuery::GetTableDataNotEzform('zdata_visit_procedure', " group_name='{$group_id}' ");
                    } else {
                        $visitProcedure = SubjectManagementQuery::GetTableDataNotEzform('zdata_visit_procedure', " IFNULL(group_name,'')<>'' ");
                    }
                    ?>
                    <?php
                    $detail_ezf_id = $visitSchedule['11111']['ezf_id'];
                    $detail_form = EzfQuery::getEzformOne($detail_ezf_id);
                    $check_visit = false;
                    $dataDetailAll = SubjectManagementQuery::GetTableData($detail_form);
                    $data = appxq\sdii\utils\SDUtility::string2Array($data);
                    foreach ($data as $key => $value):
                        $dataDetailSubject = GanttQuery::findArraybyFieldName($dataDetailAll, $value['target'], 'target', 'all');
                        //\appxq\sdii\utils\VarDumper::dump($dataDetailSubject);
                        if ($group_id == '') {
                            $group = SubjectManagementQuery::getGroupByTarget($detail_form, $value['target']);
                            if ($group) {
                                $group_id = $group['group_name'];
                                $visit_group = GanttQuery::findArraybyFieldName($visitSchedule, $group_id, 'group_name');
                                if ($visit_group)
                                    $check_visit = true;
                            }
                        }else {
                            $check_visit = true;
                        }
                        ?>
                        <tr>
                            <td><strong><?= $value['subject_number'] ?></strong></td>
                            <td><strong><?= $value['subject_no'] ?></strong></td>
                            <?php
                            foreach ($visitSchedule as $key2 => $value2) :
                                if (GanttQuery::findArraybyFieldName($dataDetailSubject, $value2['id'], 'visit_name'))
                                    $check_visit = true;
                                else
                                    $check_visit = false;

                                if ($value2['id'] == '11111' || $value2['id'] == '22222')
                                    $check_visit = true;
                                
                                $formList = isset($value2['form_list']) ? \appxq\sdii\utils\SDUtility::string2Array($value2['form_list']) : [];
                                if ($check_visit == true) {
                                    
                                    $success = 0;
                                    $waiting = 0;
                                    $no_process = 0;
                                    $form_all = 0;

                                    $proData = GanttQuery::findArraybyFieldName($visitProcedure, $value2['id'], 'visit_name', 'all');
                                    $proDataVisit = null;
                                    $proForms = [];
                                    if ($proData) {
                                        foreach ($proData as $keyPro => $valPro) {
                                            $proDataVisit = GanttQuery::findArraybyFieldName($dataProcedure, $valPro['procedure_name'], 'id');

                                            if (isset($proDataVisit['ezform_crf']) && $proDataVisit['ezform_crf'] != null) {
                                                $formArr = appxq\sdii\utils\SDUtility::string2Array($proDataVisit['ezform_crf']);
                                                $proForms = array_merge($proForms, $formArr);
                                            }
                                        }
                                    }


                                    if (count($formList) > 0) {
                                        $formList = array_merge($formList, $proForms);
                                    } else {
                                        $formList = $proForms;
                                    }

                                    if (count($formList) > 0) {
                                        $addForm = [];
                                        foreach ($formList as $valForm) {
                                            if (!in_array($valForm, $addForm))
                                                $addForm[] = $valForm;
                                        }

                                        $formList = $addForm;
                                    }

                                    if (is_array($formList)) {
                                        foreach ($formList as $keyForm => $valForm) {
                                            $ezformThis = EzfQuery::getEzformOne($valForm);

                                            if (!in_array($valForm, $allforms)) {
                                                $allforms[$valForm] = $ezformThis;
                                            }

                                            if ($ezformThis) {
                                                $dataForm = SubjectManagementQuery::GetTableData($ezformThis, " (target='{$value['target']}' OR subject_link='{$value['target']}') AND visit_link='{$value2['id']}' ", 'one');
                                                $form_all += 1;
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
                                }

                                $finalStatus = "";
                                $visitStatus = "1";
                                if (count($formList) == 0) {
                                    $visitStatus = "0";
                                    $finalStatus = "background-color:#e6e6e6;";
                                } elseif ($success >= count($formList)) {
                                    $visitStatus = "1";
                                    $finalStatus = "background-color:#98e698;";
                                }

                                $formList = appxq\sdii\utils\SDUtility::array2String($formList);
                                ?>
                                <td align="center" style="font-size:18px; <?= $finalStatus ?>"  >
                                    <?php if ($check_visit == true && $visitStatus != '0'): ?>
                                        <a href="javascript:void(0)" data-visit="<?= $value2['id'] ?>"  data-target="<?= $value['target'] ?>" data-type="1" data-form-list="<?= base64_encode($formList) ?>" class="label label-default status_button "><?= $form_all ?></a> /
                                        <a href="javascript:void(0)" data-visit="<?= $value2['id'] ?>" data-target="<?= $value['target'] ?>" data-type="2" data-form-list="<?= base64_encode($formList) ?>" class="label label-success status_button"><?= $success ?></a> /
                                        <a href="javascript:void(0)" data-visit="<?= $value2['id'] ?>" data-target="<?= $value['target'] ?>" data-type="3" data-form-list="<?= base64_encode($formList) ?>" class="label label-warning status_button"><?= $waiting ?></a> /
                                        <a href="javascript:void(0)" data-visit="<?= $value2['id'] ?>" data-target="<?= $value['target'] ?>" data-type="4" data-form-list="<?= base64_encode($formList) ?>" class="label label-danger status_button"><?= $no_process ?></a>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            echo $this->renderAjax('../pagination/view-paging', [
                'thisPage' => $thisPage,
                'pageLimit' => $pageLimit,
                'pageAmt' => isset($pageAmt) ? $pageAmt : '0',
                'reloadDiv' => 'display-schedule',
            ]);
            ?>
        </div>
    </div>
</div>

<div class="modal" id="modal-crf-export" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 40%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">All CRFs forms</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $export_permission = false;

                if (Yii::$app->user->can('administrator')) {
                    $export_permission = true;
                }
                if ($allforms) {

                    foreach ($allforms as $val) {
                        if (!$export_permission) {
                            $ezformCrfs = EzfQuery::getEzformOne($val['ezf_id']);
                            $ezf_options = \appxq\sdii\utils\SDUtility::string2Array($ezformCrfs['ezf_options']);
                            if (isset($ezf_options['lock_data']) && $ezf_options['lock_data'] != '1') {
                                $export_permission = true;
                            }
                        }
                        echo Html::button("<i class='fa fa-download'></i> Export >>> " . $val['ezf_name']
                                , ['class' => 'btn btn-success btn_export_crf'
                            , 'data-ezf_id' => $val['ezf_id']
                            , 'data-schedule_id' => $val['ezf_id']
                            , 'data-group_id' => $val['ezf_id']
                        ]);
                        echo "<br/><br/>";
                    }
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-gantt-individual" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 90%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gantt Individual</h5>
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
    'id' => 'modal-show-formlist',
    'size' => 'modal-lg'
])
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
    });

    $('.status_button').click(function () {
        var form_list = $(this).attr('data-form-list');
        var type = $(this).attr('data-type');
        var target = $(this).attr('data-target');
        var visit_id = $(this).attr('data-visit');
        var modal_show = $('#modal-show-formlist');
        var url = "/subjects/electronic-data/dashboard-modal?form_list=" + form_list + "&type=" + type + "&target=" + target + "&visit_id=" + visit_id;
        var modal_content = modal_show.find('.modal-content');
        modal_content.attr('id', 'modal-content-formlist');
        modal_content.attr('data-url-old', url);
        modal_show.modal('show');
        modal_show.find('.modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        modal_show.find('.modal-content').load(url);
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

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url + '&widget_id=' + widget);
    })

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
    })

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
        var group_id = '<?= $group_id ?>';
        modalGantt.modal('show');
        modalGantt.find('.modal-content .modal-body').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var data = {schedule_id: '<?= $widget_id ?>', skinName: 'default', data_id: data_id, data_ptid: data_ptid, group_id: group_id};
        $.get('/gantt/gantt/patient', data, function (result) {
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
])
?>';
        url += '&key_index=' + key_index + '&widget_id=' + widget + '&ezf_id=' + ezf_id + '&data_id=' + data_id;

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

    $('#modal-show-formlist').on('hidden.bs.modal', function () {
        getReloadDiv($('#display_main_edc').attr('data-url'), 'display_main_edc');
    });

    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }

    $('#btn_export_crf').click(function () {
        $('#modal-crf-export').modal();
    });

    $('.btn_export_crf').click(function () {
        var ezf_id = $(this).attr('data-ezf_id');
        var schedule_id = '<?= $schedule_id ?>';
        var group_id = '<?= $group_id ?>';
        var url = "/subjects/electronic-data/export-subject-crfs?ezf_id=" + ezf_id + "&schedule_id=" + schedule_id + "&group_id=" + group_id;

        $.get(url, function (result) {
            var data = JSON.parse(result);
<?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>;
            $('#modal-export .modal-content').html(data.html);
            $('#modal-export').modal('hide');
        })
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
