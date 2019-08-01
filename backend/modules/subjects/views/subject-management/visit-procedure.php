<?php

use backend\modules\ezforms2\classes\EzfQuery;
use \appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */
$mid = isset($_GET['id']) ? $_GET['id'] : '';
$widget_ref = SubjectManagementQuery::getWidgetById($options['widget_id']);
$data = appxq\sdii\utils\SDUtility::string2Array($widget_ref['options']);

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['widget_id'], $group_id);
$table_width = "100%";

if ((count($visitSchedule)) > 1)
    $table_width = "250";
$table_width = $table_width + (250 * ((count($visitSchedule)) - 1));

$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
$ezform_procedure = EzfQuery::getEzformOne($options['procedure_ezf_id']);
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "procedure_type=1 AND (group_name='$group_id' OR group_name='0' OR group_name IS NULL)");
$this->registerCssFile("@web/css/checkbox-style.css?2");
?>
<style>
    .zui-table {
        border: none;
        border-right: solid 1px #DDEFEF;
        border-collapse: separate;
        border-spacing: 0;
        font: normal 13px Arial, sans-serif;
    }
    .zui-table thead th {
        background-color: #DDEFEF;
        border: none;
        color: #336B6B;
        padding: 10px;
        text-align: left;
        text-shadow: 1px 1px 1px #fff;
        white-space: nowrap;
    }
    .zui-table tbody td {
        border-bottom: solid 1px #DDEFEF;
        color: #333;
        padding: 10px;
        text-shadow: 1px 1px 1px #fff;
        white-space: nowrap;
    }
    .zui-wrapper {
        position: relative;
    }
    .zui-scroller {
        margin-left: 141px;
        overflow-x: scroll;
        overflow-y: visible;
        padding-bottom: 5px;
        width: 300px;
    }
    .zui-table .zui-sticky-col {
        border-left: solid 1px #DDEFEF;
        border-right: solid 1px #DDEFEF;
        left: 0;
        position: absolute;
        top: auto;
        width: 120px;
    }

</style>

<div class="" id="display">
    <div class="col-md-4 sdbox-col">
        <div class="table-responsive" id="table-procedure-scope">
            <div id="content-table">
                <table class="table table-bordered table-striped" id="table-procedure" style="width:100%">
                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px;">
                            <td rowspan="2" style="text-align:center;">NO.</td>
                            <td style="text-align: center;background-color:whitesmoke;" >
                                Visit
                            </td>
                            <td rowspan="2" width="70px;" style="text-align: center;background-color:whitesmoke;">
                                All
                            </td>
                        </tr>
                        <tr style="height: 50px;">
                            <td  style="text-align:center;">Procedure Name</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $number = 1;
                        foreach ($prodecureData as $key => $value) {
                            $val = $value['procedure_name'];
                            $subjectList = [];
                            $data_subject = SubjectManagementQuery::getSubjectProcedureByName($val, $group_id);
                            $proId = "";

                            if (is_array($data_subject)) {
                                foreach ($data_subject as $keyPro => $valPro) {
                                    $subjectList[] = $valPro['visit_name'];
                                }
                            }
                            ?>
                            <tr style="height: 70px;">
                                <td style="text-align: center;"><?= $number ?></td>
                                <td style="background-color:whitesmoke;" onmouseover="onShowButton(this);" onmouseout="onHideButton(this);">
                                    <?php if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) : ?>

                                            <?=
                                                EzfHelper::btn($options['procedure_ezf_id'])->reloadDiv('display-procedure')->options(['class' => 'btn btn-danger btn-xs'])->label('<i class="fa fa-trash"></i>')->buildBtnDelete($value['id']);
                                            ?>
                                            <?= EzfHelper::btn($options['procedure_ezf_id'])->reloadDiv('display-procedure')->modal('modal-ezform-procedure')->options(['class' => 'btn btn-primary btn-xs'])->label('<i class="fa fa-pencil"></i>')->buildBtnEdit($value['id']); ?>

                                    <?php endif; ?>
                                    <?php
                                    $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $val], 'one');
                                    ?>
                                    <?php
                                    echo "<strong>" . $val . "</strong>";
                                    ?>
                                </td>
                                <td style="text-align:center;padding-left: 10px;background-color:whitesmoke;">
                                    <div class="checkbox1 checkbox1-success">
                                        <?=
                                        Html::checkbox('checkbox' . $value['id'] . '-all', '', [
                                            'id' => 'checkbox-check' . $value['id'] . '-all',
                                            'class' => "check-subject-active-all checked-all",
                                            'data-index' => isset($index) ? $index : '',
                                            'data-id' => $value['id'],
                                            'data-name' => $val,
                                            'data-mid' => isset($mid) ? $mid : '',
                                            'data-widget_id' => $widget_id,
                                        ])
                                        ?>
                                        <?= Html::label('', 'checkbox-check' . $value['id'] . '-all') ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $number++;
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-md-8 sdbox-col" style="margin-left:-16px;">
        <div class="table-responsive" id="table-procedure-scope1">
            <div id="content-table1">
                <table class="table table-bordered table-striped" id="table-procedure1" style="width:<?=$table_width?>px;">
                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px;">
                            <?php
                            $col_count = 0;
                            $dataVisit = [];
                            foreach ($visitSchedule as $key => $value) {
                                $col_count += 1;
                                $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                                $form_name = $value['visit_name'];
                                $dataVisit[$value['id']]['number'] = $col_count;
                                $dataVisit[$value['id']]['visit_name'] = $form_name;
                                if ($form_name == '' && $ezform) {
                                    $form_name = $ezform->ezf_name;
                                }
                                ?>
                                <td  style="text-align: center;width:400px;">
                                    <label class="label label-default"><?=$col_count?></label>
                                    <?php if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) : 
                                        echo Html::button("<i class='fa fa-trash'></i>",['class'=>'btn btn-danger btn-xs btn-delete-visit','style'=>'margin-right:5px;',
                                            'data-data_id'=>$value['id'],
                                            'data-key_index'=>$key,
                                            'data-ezf_id'=>$value['ezf_id'],
                                            ]);
                                        echo Html::button("<i class='fa fa-pencil'></i>",['class'=>'btn btn-primary btn-xs btn-edit-visit',
                                            'data-data_id'=>$value['id'],
                                            'data-key_index'=>$key,
                                            'data-ezf_id'=>$value['ezf_id'],
                                            ]);
                                        
                                    endif;
                                    ?>
                                    <?= $form_name ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr style="height: 50px;">
                            <?php
                            $col_count = 0;
                            foreach ($visitSchedule as $key => $value) {
                                $col_count += 1;
                                $plan_date = isset($value['plan_date']) ? $value['plan_date'] : '';
                                $number_link = '';
                                $visit_link = '';
                                if(isset($value['visit_cal_date'])){
                                    $number_link = isset($dataVisit[$value['visit_cal_date']]['number'])?$dataVisit[$value['visit_cal_date']]['number']:'';
                                    $visit_link = isset($dataVisit[$value['visit_cal_date']]['visit_name'])?$dataVisit[$value['visit_cal_date']]['visit_name']:'';
                                }
                                ?>
                                <td  style="text-align: center;width:100px;">
                                    <?php if($value['id']!= '11111') :?>
                                        <label class="label label-info" title="<?=$visit_link?>"><?=$number_link?></label>  <i class="fa fa-long-arrow-left "></i>
                                        <?= \backend\modules\subjects\classes\ReportQuery::day2DayWeekMonth($plan_date) ?>
                                    <?php endif;?>
                                </td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($prodecureData as $key => $value) {
                            $val = $value['procedure_name'];
                            $subjectList = [];
                            $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);
                            $proId = "";

                            if (is_array($data_subject)) {
                                foreach ($data_subject as $keyPro => $valPro) {
                                    $subjectList[] = $valPro['visit_name'];
                                }
                            }
                            ?>
                            <tr style="height: 70px;">
                                <?php
                                $checkAll = true;
                                $index = 0;
                                foreach ($visitSchedule as $key2 => $value2) :
                                    $index ++;
                                    $form_name = $value2['visit_name'];
                                    $checked = "";

                                    if (in_array($value2['id'], $subjectList)) {
                                        $checked = "checked";
                                    } else {
                                        $checkAll = false;
                                    }
                                    ?>
                                    <td  align="center" style="padding-left: 0px;">
                                        <div class="checkbox1 checkbox1-success">
                                            <?=
                                            Html::checkbox('checkbox' . $value['id'] . '-' . $key2, $checked, [
                                                'id' => 'checkbox-' . $value['id'] . '-' . $key2,
                                                'class' => "check-subject-active",
                                                'data-index' => $index,
                                                'data-id' => $value['id'],
                                                'data-name' => $val,
                                                'data-mid' => $mid,
                                                'data-widget_id' => $widget_id,
                                                'data-visit_name' => $form_name,
                                                'data-visit_id' => $value2['id'],
                                            ])
                                            ?>
                                            <?= Html::label('', 'checkbox-' . $value['id'] . '-' . $key2) ?>
                                        </div>
                                    </td>
                                    <?php
                                endforeach;
                                ?>
                            </tr>
                        <span class="check-all-value" data-value="<?= $checkAll ?>" data-index="<?= $value['id'] ?>"></span>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-add-procedure',
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var request = 0;
    $(function () {
        var mx = 0;
        $("#table-procedure-scope1").on({
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
    });

    $(function () {
        setTimeout(function () {
            $('.check-all-value').each(function (i, e) {
                var val = $(e).attr('data-value');
                var index = $(e).attr('data-index');
                $('.checked-all').each(function (i2, e2) {
                    var id = $(e2).attr('data-id');
                    if (id == index) {
                        if (val == '1') {
                            $(e2).attr('checked', 'checked');
                        }
                    }
                })
            })
        }, 100);
    });
    $('.check-subject-active').on('click', function () {
        var module_id = $(this).attr('data-mid');
        var widget_id = $(this).attr('data-widget_id');
        var visit_name = $(this).attr('data-visit_name');
        var visit_id = $(this).attr('data-visit_id');
        var group_name = '<?= $group_name ?>';
        var group_id = '<?= $group_id ?>';
        var index = $(this).attr('data-index');
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var check = $(this).is(':checked');
        var check_val = 0;
        //console.log(check);
        if (check == true) {
            check_val = 1;
        }

        var data = {module_id: module_id, widget_id: widget_id, check_val: check_val, checked: check, name: name, id: id, visit_name: visit_name, visit_id: visit_id, index: index, group_name: group_name, group_id: group_id};
        $.get('/subjects/subject-management/update-subject-procedure', data, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                //location.reload(); 
            } else {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
        })
    });
    
    function onShowButton(e){
        //$(e).find('.btn_manage_procedure').css('display','block');
    }
    function onHideButton(e){
        //$(e).find('.btn_manage_procedure').css('display','none');
    }

    $('.checked-all').change(function () {
        var procedure_id = $(this).attr('data-id');
        var checked = $(this).is(':checked');
        if (checked == true) {
            $('[id^=checkbox-' + procedure_id + ']').prop('checked', 'checked');
            $('[id^=checkbox-' + procedure_id + ']').each(function (i, e) {

                var module_id = $(e).attr('data-mid');
                var widget_id = $(e).attr('data-widget_id');
                var visit_name = $(e).attr('data-visit_name');
                var visit_id = $(e).attr('data-visit_id');
                var group_name = '<?= $group_name ?>';
                var group_id = '<?= $group_id ?>';
                var index = $(e).attr('data-index');
                var id = $(e).attr('data-id');
                var name = $(e).attr('data-name');
                var check = $(e).is(':checked');
                var check_val = 1;
                var data = {module_id: module_id, widget_id: widget_id, check_val: check_val, checked: check, name: name, id: id, visit_name: visit_name, visit_id: visit_id, index: index, group_name: group_name, group_id: group_id};
                $.get('/subjects/subject-management/update-subject-procedure', data, function (result) {
                    if (result.status == 'success') {

                    } else {

                    }
                })

            });
        } else {
            $('[id^=checkbox-' + procedure_id + ']').prop('checked', false);
            $('[id^=checkbox-' + procedure_id + ']').each(function (i, e) {

                var module_id = $(e).attr('data-mid');
                var widget_id = $(e).attr('data-widget_id');
                var visit_name = $(e).attr('data-visit_name');
                var visit_id = $(e).attr('data-visit_id');
                var group_name = '<?= $group_name ?>';
                var group_id = '<?= $group_id ?>';
                var index = $(e).attr('data-index');
                var id = $(e).attr('data-id');
                var name = $(e).attr('data-name');
                var check = $(e).is(':checked');
                var check_val = 0;
                var data = {module_id: module_id, widget_id: widget_id, check_val: check_val, checked: check, name: name, id: id, visit_name: visit_name, visit_id: visit_id, index: index, group_name: group_name, group_id: group_id};
                $.get('/subjects/subject-management/update-subject-procedure', data, function (result) {

                    if (result.status == 'success') {

                    } else {

                    }
                })

            });
        }


    })
    $('.btn-add-procedure').click(function () {
        var widget_id = $(this).attr('data-widget_id');
        var group_id = '<?= $group_id ?>';
        var url = '/subjects/subject-management/form-add-procedure?widget_id=' + widget_id + '&group_id=' + group_id;
        $('#modal-add-procedure .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-add-procedure').modal('show')
                .find('.modal-content')
                .load(url);
    });
    $('.btn-delete-procedure').click(function () {
        if (request == 1)
            return;
        var widget_id = $(this).attr('data-widget_id');
        var name = $(this).attr('data-name');
        var id = $(this).attr('data-id');
        var url = '/subjects/subject-management/delete-procedure';
        request = 1;
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
            request = 0;
            $.get(url, {widget_id: widget_id, procedure_name: name, procedure_id: id}, function (result) {
                if (result.status == 'success') {
<?php echo SDNoty::show('result.message', 'result.status') ?>
                } else {
<?php echo SDNoty::show('result.message', 'result.status') ?>
                }
            })

        }, function () {
            request = 0;
        });
    });
    
        $('.btn-delete-visit').click(function () {

        var data_id = $(this).attr('data-data_id');
        var key_index = $(this).attr('data-key_index');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
            yii\helpers\Url::to([
                '/subjects/subject-management/delete-visit',
                'reloadDiv' => 'display-procedure',
                'widget_id' => $widget_id,
                'schedule_id'=>$schedule_id,
                'group_id'=>$group_id,
                'group_name'=>$group_name,
            ])
            ?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.get(url, {key_index: key_index, ezf_id: ezf_id, data_id: data_id}
            ).done(function (result) {
                if (result.status == 'success') {
                    var url = $('#display-procedure').attr('data-url');
                    getReloadDiv(url, 'display-procedure');
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
            });
        });
    });
    
    $('.btn-edit-visit').click(function () {
        var data_id = $(this).attr('data-data_id');
        var key_index = $(this).attr('data-key_index');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
            yii\helpers\Url::to([
                '/subjects/subject-management/config-visit-procedure',
                'reloadDiv' => 'display-procedure',
                'options' => $options,
                'widget_id' => $widget_id,
                'schedule_id'=>$schedule_id,
                'group_id'=>$group_id,
                'group_name'=>$group_name,
            ])
            ?>';
        url += '&key_index=' + key_index + '&ezf_id=' + ezf_id + '&data_id=' + data_id;

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>

