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

$schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
$procedure_widget_ref = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);
$schedule_data = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
$ezform_section = EzfQuery::getEzformOne($options['section_ezf_id']);

$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);
$_SESSION['group_id'] = $group_id;
$table_width = "100%";

$ezform_procedure = EzfQuery::getEzformOne($procedureOptions['procedure_ezf_id']);
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "(procedure_type IN(1,2)) AND (group_name='$group_id' OR group_name IS NULL OR group_name ='0' OR group_name ='' )");
$budgetData = SubjectManagementQuery::GetTableData($ezform_budget);
$sectionData = SubjectManagementQuery::GetTableData($ezform_section);

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

$reloadDiv = 'display-financial';
if ((count($visitSchedule)) > 1) {
    $table_width = '200';
    $table_width = $table_width + (200 * ((count($visitSchedule)) - 1));
}
?>
<div class="col-md-12">
    <?php if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) { ?>
        <?= EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label(Yii::t('ezform', '<i class="fa fa-plus"></i>')." Add new procedure")->options(['class' => 'btn btn-success'])->initdata(['pro_name' => '', 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->reloadDiv($reloadDiv)->buildBtnAdd(); ?>
    <?php } ?> 
</div>
<div class="clearfix"></div><br/>
<div id="display">
    <div class="col-md-5">
        <div class="table-responsive" id="table-financial-scope">
            <div id="content-table">
                <table class="table table-bordered table-striped" id="table-financial" width="100%">
                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px">
                            <td rowspan="2" style="text-align: center;background-color:whitesmoke;width: 75%;">

                                Procedure Name
                            </td>
                            <td rowspan="2" style="text-align: center;background-color:whitesmoke;width: 25%;">
                                Budget
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $proInBudget = [];
                        foreach ($sectionData as $sec_key => $sec_value) {
                            ?>
                            <tr style="height: 75px">
                                <td style="background-color:whitesmoke;">
                                    <strong>
                                        <?php
                                        echo $sec_value['section_name'];
                                        ?>
                                    </strong>
                                </td>
                                <td style="background-color:whitesmoke;" align="center" colspan="<?= count($schedule_data) + count($visitSchedule) + 1 ?>">
                                </td>
                            </tr>
                            <?php
                            foreach ($procedureAll as $key => $value) {
                                $row_total = 0;
                                $val = $value['procedure_name'];
                                $procedure_by = $value['procedure_by'];
                                $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, 'pro_name=' . $value['id'] . ' AND group_name="' . $group_id . '"', 'one');

                                $enable_visit = [];

                                if ($data_budget['section'] == $sec_value['id']) {

                                    if ($data_budget['enable_visit']) {
                                        $enable_visit = appxq\sdii\utils\SDUtility::string2Array($data_budget['enable_visit']);
                                    }
                                    $proInBudget[] = $value['id'];
                                    $subjectList = [];
                                    $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);
                                    $proId = "";

                                    if (is_array($data_subject)) {
                                        foreach ($data_subject as $keyPro => $valPro) {
                                            $subjectList[] = $valPro['id'];
                                        }
                                    }
                                    ?>
                                    <tr style="height: 75px">
                                        <td style="background-color:whitesmoke;">
                                            <?php
                                            if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                ?>
                                                <?php
                                                if ($data_budget['pro_name'] == $value['id']):
                                                    echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                                else:
                                                    echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->initdata(['pro_name' => $value['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                                endif;

                                                if ($value['type'] == '2'):
                                                    echo EzfHelper::btn($procedureOptions['procedure_ezf_id'])->reloadDiv($reloadDiv)->label("<i class='fa fa-trash'></i>")->options(['class' => 'btn btn-danger', 'style' => 'margin-left:5px'])->buildBtnDelete($value['id']);
                                                endif;
                                            else:
                                                echo Html::label($val, '', ['class' => 'label label-default']);
                                            endif;
                                            ?>
                                        </td>
                                        <td align="center" style="background-color:whitesmoke;">
                                            <div class="col-md-10 col-sm-offset-1">
                                                <label class="label label-info" style="font-size: 16px;"><?= number_format(!empty($data_budget['budget']) ? $data_budget['budget'] : 0, '2'); ?></label>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php
                                }
                            }
                        }
                        ?>
                        <tr style="height: 75px">
                            <td style="background-color:whitesmoke;">
                                <strong>
                                    Other.
                                </strong>
                            </td>
                            <td style="background-color:whitesmoke;" align="center" colspan="<?= count($schedule_data) + count($visitSchedule) + 1 ?>">
                            </td>
                        </tr>
                        <?php
// ส่วนของ Procedure ที่ยังไม่ได้เพิ่มข้อมุล Budget หรือไม่ได้กำหนด Section ==================================

                        foreach ($procedureAll as $key => $value) {
                            $row_total = 0;
                            $val = $value['procedure_name'];
                            $procedure_by = $value['procedure_by'];
                            $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'group_name' => $group_id], 'one');
                            $enable_visit = [];
                            if ($data_budget['enable_visit']) {
                                $enable_visit = appxq\sdii\utils\SDUtility::string2Array($data_budget['enable_visit']);
                            }

                            if (!in_array($value['id'], $proInBudget)) :
                                $proInBudget[] = $value['id'];
                                $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);
                                ?>
                                <tr style="height: 75px">
                                    <td style="background-color:whitesmoke;">
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :

                                            if ($data_budget['pro_name'] == $value['id']):
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                            else:
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->initdata(['pro_name' => $value['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                            endif;

                                            if ($value['type'] == '2'):
                                                echo EzfHelper::btn($procedureOptions['procedure_ezf_id'])->reloadDiv($reloadDiv)->label("<i class='fa fa-trash'></i>")->options(['class' => 'btn btn-danger', 'style' => 'margin-left:5px'])->buildBtnDelete($value['id']);
                                            endif;
                                        else:
                                            echo Html::label($val, '', ['class' => 'label label-default']);
                                        endif;
                                        ?>
                                    </td>
                                    <td align="center" style="background-color:whitesmoke;">
                                        <div class="col-md-10 col-sm-offset-1">
                                            <label class="label label-info" style="font-size: 16px;"><?= number_format(!empty($data_budget['budget']) ? $data_budget['budget'] : 0, '2'); ?></label>
                                        </div>
                                    </td>

                                </tr>

                                <?php
                            endif;
                            ?>

                            <?php
                            // ข้อมูล Procedure ที่มีใน Budget แต่ไม่มีการกำหนด Section =================================================

                            if ($procedure_by == 'financial' && $data_budget['section'] == '' && !in_array($value['id'], $proInBudget)) :
                                foreach ($data_subject as $key2 => $val2) {
                                    $subjectList[] = $val2['id'];
                                }
                                ?>
                                <tr style="height: 75px">
                                    <td style="background-color:whitesmoke;">
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                            if ($data_budget['pro_name'] == $value['id']):
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                            else:
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default pro_name_btn', 'title' => $val])->initdata(['pro_name' => $value['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                            endif;
                                        else:
                                            echo Html::label($val, '', ['class' => 'label label-default']);
                                        endif;
                                        ?>
                                    </td>
                                    <td align="center" style="background-color:whitesmoke;">
                                        <div class="col-md-10 col-sm-offset-1">
                                            <label class="label label-info" style="font-size: 16px;"><?= number_format(!empty($data_budget['budget']) ? $data_budget['budget'] : 0, '2'); ?></label>
                                        </div>
                                    </td>
                                    <?php
                                    $index = 0;


                                    if (isset($column_total[$index]))
                                        $column_total[$index] += $row_total;
                                    ?>
                                </tr>

                                <?php
                            endif;
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <tr style="height: 75px">
                            <td align="right" style="background-color:whitesmoke;">
                                <strong><?= Yii::t('ezform', 'Sub Total') ?></strong>
                            </td>
                            <td align="center" style="background-color:whitesmoke;">

                            </td>
                            <?php
                            $budget_oh = 0;
                            $sTotal = 0;
                            ?>
                        </tr>

                        <tr style="height: 75px">
                            <td align="right" style="background-color:whitesmoke;">
                                <strong><?= Yii::t('ezform', 'Over Head') ?></strong>
                            </td>
                            <td align="center" style="background-color:whitesmoke;">
                                <?php
//$data_procedure = SubjectManagementQuery::GetTableData($ezform_procedure, ['procedure_type' => '3'], 'one');
                                $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => 'Over Head', 'group_name' => $group_id], 'one');
                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                    if ($data_budget) {
                                        $budget_oh = $data_budget['budget'];
                                        echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label($budget_oh . '%')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->buildBtnEdit($data_budget['id']);
                                    } else {
                                        echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label('0 %')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => 'Over Head', 'group_name' => $group_id])->buildBtnAdd();
                                    }
                                else:
                                    if ($data_budget) {
                                        $budget_oh = $data_budget['budget'];
                                        echo Html::label($budget_oh . '%', '', ['class' => 'label label-default']);
                                    } else {
                                        echo Html::label('0 %', '', ['class' => 'label label-default']);
                                    }
                                endif;
                                ?>
                            </td>

                        </tr>
                        <tr style="height: 75px">
                            <td align="right" style="background-color:whitesmoke;">
                                <strong><?= Yii::t('ezform', 'Grand Total') ?></strong>
                            </td>
                            <td align="center" style="background-color:whitesmoke;">

                            </td>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-7 sdbox-col" style="margin-left:-16px;">
        <div class="table-responsive" id="table-financial-scope1">
            <div id="content-table1">
                <table class="table table-bordered table-striped" id="table-financial1" style="width:<?= $table_width ?>px;">
                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px">
                            <?php
                            $col_count = 0;
                            $budget_value = 0;
                            foreach ($visitSchedule as $key => $value) {
                                $col_count += 1;
                                $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                                $form_name = $value['visit_name'];
                                if (isset($ezform->ezf_name)) {
                                    if ($form_name == '') {
                                        $form_name = $ezform->ezf_name;
                                    }
                                }
                                ?>
                                <td  style="text-align: center;"><?= $form_name ?>

                                </td>
                            <?php } ?>
                            <td  style="text-align: center;"><strong><?= Yii::t('ezform', 'Total') ?></strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $net_total = [];
                        $sectionAll = [];
                        $proInBudget = [];
                        $column_total = array();
                        $i = 0;
                        for ($i = 0; $i < (count($visitSchedule) + 1); $i++) {
                            $column_total[$i] = 0;
                        }

                        foreach ($sectionData as $sec_key => $sec_value) {
                            ?>
                            <tr style="height: 75px">
                                <td>
                                    <strong>

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
                                $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'group_name' => $group_id], 'one');
                                $enable_visit = [];
                                if ($data_budget['enable_visit']) {
                                    $enable_visit = appxq\sdii\utils\SDUtility::string2Array($data_budget['enable_visit']);
                                }
                                if ($data_budget['section'] == $sec_value['id']) :
                                    $proInBudget[] = $value['id'];
                                    $subjectList = [];

                                    $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);
                                    $proId = "";

                                    if (is_array($data_subject)) {
                                        foreach ($data_subject as $keyPro => $valPro) {
                                            $subjectList[] = $valPro['visit_name'];
                                        }
                                    }
                                    ?>
                                    <tr style="height: 75px">

                                        <?php
                                        $index = 0;
                                        foreach ($visitSchedule as $key2 => $value2) {

                                            $form_name = $value2['visit_name'];
                                            $inputText = "";
                                            if (isset($column_total[$index]))
                                                $column_total[$index] += 0;
                                            $data_budget_visit = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id], 'one');

                                            if ($data_budget_visit && (in_array($value2['id'], $subjectList) || in_array($value2['id'], $enable_visit))) {

                                                $budget_value = $data_budget_visit['budget'] == '' ? '0.00' : number_format($data_budget_visit['budget'], '2');
                                                $row_total += $data_budget_visit['budget'];
                                                if (isset($column_total[$index]))
                                                    $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];

                                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                    if ($data_budget_visit['id'] != '') {
                                                        $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->buildBtnEdit($data_budget_visit['id']);
                                                    } else {
                                                        $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                                    }
                                                else:
                                                    $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                                endif;
                                            } else if (in_array($value2['id'], $subjectList) || in_array($value2['id'], $enable_visit)) {

                                                $budget_value = $data_budget['budget'] == '' ? '0.00' : number_format($data_budget['budget'], '2');
                                                $row_total += $data_budget['budget'];

                                                if (isset($column_total[$index]))
                                                    $column_total[$index] = $column_total[$index] + $data_budget['budget'];
                                                if ($budget_value != '') {
                                                    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                        $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();

                                                    else:
                                                        $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                                    endif;
                                                }
                                            } else if ($procedure_by == 'financial') {
                                                if ($val . '-' . $form_name == $data_budget_visit['pro_name']) {
                                                    $budget_value = $data_budget_visit['budget'] == '' ? '0.00' : number_format($data_budget_visit['budget'], '2');
                                                    $row_total += $data_budget_visit['budget'];
                                                    if (isset($column_total[$index]))
                                                        $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];

                                                    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                        if ($data_budget_visit['id'] != '') {
                                                            $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->buildBtnEdit($data_budget_visit['id']);
                                                        } else {
                                                            $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                                        }
                                                    else:
                                                        $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                                    endif;
                                                } else {
                                                    //$inputText = EzfHelper::btn($options['budget_ezf_id'])->label('0')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => $val . '-' . $form_name])->buildBtnAdd();
                                                }
                                            }
                                            ?>
                                            <td align="center" >
                                                <div class="col-md-10 col-sm-offset-1">
                                                    <?= $inputText ?>
                                                </div>    
                                            </td>
                                            <?php
                                            $index ++;
                                        }
                                        if (isset($column_total[$index]))
                                            $column_total[$index] += $row_total;
                                        ?>
                                        <td align="right"><strong><?= number_format($row_total, '2') ?></strong></td>
                                    </tr>

                                    <?php
                                endif;
                            }
                        }
                        ?>
                        <tr style="height: 75px">
                            <td>
                                <strong>
                                    Other.
                                </strong>
                            </td>
                            <td align="center" colspan="<?= count($schedule_data) + count($visitSchedule) + 1 ?>">
                            </td>
                        </tr>
                        <?php
// ส่วนของ Procedure ที่ยังไม่ได้เพิ่มข้อมุล Budget หรือไม่ได้กำหนด Section ==================================

                        foreach ($procedureAll as $key => $value) {
                            $subjectList = [];
                            $row_total = 0;
                            $val = $value['procedure_name'];
                            $procedure_by = $value['procedure_by'];
                            $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'group_name' => $group_id], 'one');
                            $data_procedure = SubjectManagementQuery::GetTableData($ezform_procedure, ['id' => $value['id']], 'one');
                            $enable_visit = [];
                            if ($data_budget['enable_visit']) {
                                $enable_visit = appxq\sdii\utils\SDUtility::string2Array($data_budget['enable_visit']);
                            }
                            if (!in_array($value['id'], $proInBudget)) :
                                $proInBudget[] = $value['id'];
                                $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);

                                if (is_array($data_subject)) {
                                    foreach ($data_subject as $keyPro => $valPro) {
                                        $subjectList[] = $valPro['visit_name'];
                                    }
                                }
                                ?>
                                <tr style="height: 75px">

                                    <?php
                                    $index = 0;

                                    foreach ($visitSchedule as $key2 => $value2) {

                                        $form_name = $value2['visit_name'];
                                        $inputText = "";
                                        if (isset($column_total[$index]))
                                            $column_total[$index] += 0;
                                        $data_budget_visit = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id], 'one');

                                        if ($data_budget_visit && in_array($value2['id'], $subjectList)) {
                                            $budget_value = $data_budget_visit['budget'] == '' ? '0.00' : number_format($data_budget_visit['budget'], '2');
                                            $row_total += $data_budget_visit['budget'];
                                            if (isset($column_total[$index]))
                                                $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];
                                            if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                if ($data_budget_visit['id'] != '') {
                                                    $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->buildBtnEdit($data_budget_visit['id']);
                                                } else {
                                                    $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                                }
                                            else:
                                                $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                            endif;
                                        } else if (in_array($value2['id'], $subjectList) || in_array($value2['id'], $enable_visit)) {
                                            $budget_value = $data_budget['budget'] == '' ? '0.00' : number_format($data_budget['budget'], '2');
                                            $row_total += $data_budget['budget'];

                                            if (isset($column_total[$index]))
                                                $column_total[$index] = $column_total[$index] + $data_budget['budget'];

                                            if ($budget_value != '') {
                                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                    $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();

                                                else:
                                                    $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                                endif;
                                            }
                                        }else if ($procedure_by == 'financial') {
                                            if ($val . '-' . $form_name == $data_budget_visit['pro_name']) {
                                                $budget_value = $data_budget_visit['budget'] == '' ? '0' : number_format($data_budget_visit['budget'], '2');
                                                $row_total += $data_budget_visit['budget'];
                                                if (isset($column_total[$index]))
                                                    $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];

                                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                    if ($data_budget_visit['id'] != '') {
                                                        $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->buildBtnEdit($data_budget_visit['id']);
                                                    } else {
                                                        $inputText = EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label("<span>$budget_value</span>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-warning'])->initdata(['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id, 'task_item' => null, 'other_group' => null, 'segment' => null])->buildBtnAdd();
                                                    }
                                                else:
                                                    $inputText = Html::label("<span>$budget_value</span>", '', ['class' => 'label label-default']);
                                                endif;
                                            } else {
                                                //$inputText = EzfHelper::btn($options['budget_ezf_id'])->label('0')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => $val . '-' . $form_name])->buildBtnAdd();
                                            }
                                        }
                                        ?>
                                        <td align="center" >
                                            <div class="col-md-10 col-sm-offset-1">
                                                <?= $inputText ?>
                                            </div>    
                                        </td>
                                        <?php
                                        $index ++;
                                    }
                                    if (isset($column_total[$index]))
                                        $column_total[$index] += $row_total;
                                    ?>
                                    <td align="right"><strong><?= number_format($row_total, '2') ?></strong></td>
                                </tr>

                                <?php
                            endif;
                            ?>

                            <?php
                            // ข้อมูล Procedure ที่มีใน Budget แต่ไม่มีการกำหนด Section =================================================

                            if ($procedure_by == 'financial' && $data_budget['section'] == '' && !in_array($value['id'], $proInBudget)) :
                                ?>
                                <tr style="height: 75px">

                                    <?php
                                    $index = 0;

                                    foreach ($visitSchedule as $key2 => $value2) {

                                        $form_name = $value2['visit_name'];
                                        $inputText = "";
                                        if (isset($column_total[$index]))
                                            $column_total[$index] += 0;

                                        $data_budget_visit = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $value['id'], 'visit_name' => $value2['id'], 'group_name' => $group_id], 'one');

                                        if ($data_budget_visit && in_array($value2['id'], $subjectList)) {
                                            $budget_value = $data_budget_visit['budget'] == '' ? '0' : number_format($data_budget_visit['budget'], '2');
                                            $row_total += $data_budget_visit['budget'];
                                            if (isset($column_total[$index]))
                                                $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];
                                        } else if (in_array($value2['id'], $enable_visit)) {
                                            $budget_value = $data_budget['budget'] == '' ? '0' : number_format($data_budget['budget'], '2');
                                            $row_total += $data_budget['budget'];
                                            if (isset($column_total[$index]))
                                                $column_total[$index] = $column_total[$index] + $data_budget['budget'];
                                        }else if ($procedure_by == 'financial') {
                                            if ($val . '-' . $form_name == $data_budget_visit['pro_name']) {
                                                $budget_value = $data_budget_visit['budget'] == '' ? '0' : number_format($data_budget_visit['budget'], '2');
                                                $row_total += $data_budget_visit['budget'];
                                                if (isset($column_total[$index]))
                                                    $column_total[$index] = $column_total[$index] + $data_budget_visit['budget'];
                                            } else {
                                                //$inputText = EzfHelper::btn($options['budget_ezf_id'])->label('0')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => $val . '-' . $form_name])->buildBtnAdd();
                                            }
                                        }
                                        ?>
                                        <td align="center" >
                                            <div class="col-md-10 col-sm-offset-1">
                                            </div>    
                                        </td>
                                        <?php
                                        $index ++;
                                    }
                                    if (isset($column_total[$index]))
                                        $column_total[$index] += $row_total;
                                    ?>
                                    <td align="right"><strong><?= number_format($row_total, '2') ?></strong></td>
                                </tr>

                                <?php
                            endif;
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <tr style="height: 75px">
                            <?php
                            $sTotal = 0;
                            foreach ($column_total as $key => $value) {
                                if ($key < count($column_total)):
                                    $sTotal += $value;
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($value, '2') ?></strong>
                                    </td>
                                    <?php
                                else:
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($sTotal, '2') ?></strong>
                                    </td>
                                <?php
                                endif;
                            }
                            ?>
                        </tr>

                        <tr style="height: 75px">

                            <?php
                            $ohTotal = 0;
                            foreach ($column_total as $key => $value) {

                                if ($key < count($column_total)):
                                    $sumPerAll[] = ((float) $budget_oh / 100) * (float) $value;
                                    $sumPer = ((float) $budget_oh / 100) * (float) $value;
                                    $ohTotal += $sumPer;
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($sumPer, 2) ?></strong>
                                    </td>
                                    <?php
                                else:
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($ohTotal, 2) ?></strong>
                                    </td>
                                <?php
                                endif;
                            }
                            ?>
                        </tr>
                        <tr style="height: 75px">

                            <?php
                            $gTotal = 0;
                            foreach ($column_total as $key => $value) {
                                if ($key < count($column_total)):
                                    $gTotal += ($sumPerAll[$key] + $value);
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($sumPerAll[$key] + $value, '2') ?></strong>
                                    </td>
                                    <?php
                                else:
                                    ?>
                                    <td align="center">
                                        <strong><?= number_format($gTotal, '2') ?></strong>
                                    </td>    
                                <?php
                                endif;
                            }
                            ?>
                        </tr>
                    </tfoot>
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

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var mx = 0;
        $("#table-financial-scope1").on({
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
        var screen_width = $(window).width() / 7;
        $('.pro_name_btn').each(function (i, e) {
            $(e).find('label').css('width', screen_width);
        })
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>