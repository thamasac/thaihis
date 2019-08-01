<?php

use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;

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
$project_id = Yii::$app->request->get('data_id');
$budget_pms_ezf_id = backend\modules\subjects\Module::$formsId['budget_pms_ezf_id'];
$budget_pms_ezf = EzfQuery::getEzformOne($budget_pms_ezf_id);

$ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
$ezform_section = EzfQuery::getEzformOne($options['section_ezf_id']);
$sectionData = SubjectManagementQuery::GetTableData($ezform_section);

$table_width = "100%";


$ezform_procedure = EzfQuery::getEzformOne('1520742721018881300');
$prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "(sent_module_1 = '1') and (project_id = '$project_id')");


$taskAll = [];
$taskCheck = [];
$count = 0;
$maxcolumn = 0;

foreach ($prodecureData as $key => $value) {
    $taskAll[$count]['id'] = $value['id'];
    $taskAll[$count]['seg_performance'] = $value['seg_performance'];
    $taskAll[$count]['task_name'] = $value['task_name'];
    $taskAll[$count]['type'] = '1';
    $taskAll[$count]['task_by'] = 'task';
    $taskCheck[] = $value['task_name'];

    if ($maxcolumn < $value['seg_performance']) {
        $maxcolumn = $value['seg_performance'];
    }
    $count++;
}


$reloadDiv = 'display-pms';
if (($maxcolumn) > 1) {
    $table_width = '300';
    $table_width = $table_width + (150 * (($maxcolumn) - 1));
}
?>

<div id="display">
    <div class="col-md-5">
        <div class="table-responsive" id="table-financial-scope">
            <div id="content-table">
                <table class="table table-bordered table-striped" id="table-financial" width="100%">
                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px">
                            <td rowspan="2" style="text-align: center;background-color:#85b3b6;width: 75%;">
                                Task Name
                            </td>
                            <td rowspan="2" style="text-align: center;background-color:#84b3b68c;width: 25%;">
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
                                <td style="background-color:whitesmoke;" align="center">
                                </td>
                            </tr>
                            <?php
                            foreach ($taskAll as $key => $value) {
                                $row_total = 0;
                                $val = $value['task_name'];
                                $task_by = $value['task_by'];
                                $data_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');

                                if ($data_budget['section'] == $sec_value['id']) :
                                    $proInBudget[] = $value['id'];
                                    ?>
                                    <tr style="height: 75px">
                                        <td style="background-color:white;">
                                            <?php
                                            if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                ?>
                                                <?php
                                                if ($data_budget['task_item'] == $value['id']):
                                                    echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                                else:
                                                    echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'], 'other_group' => 'pms'])->buildBtnAdd();
                                                endif;
                                            else:
                                                echo Html::label($val, '', ['class' => 'label label-default', 'style' => 'font-size:15px']);
                                            endif;
                                            ?>
                                        </td>
                                        <td align="center" style="background-color:white;">
                                            <div class="col-md-10 col-sm-offset-1">
                                                <label class="label label-info" style="font-size: 16px;"><?= number_format(!empty($data_budget['budget']) ? $data_budget['budget'] : 0, '2'); ?></label>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php
                                endif;
                            }
                        }
                        ?>
                        <tr style="height: 75px">
                            <td style="background-color:whitesmoke;">
                                <strong>
                                    Other.
                                </strong>
                            </td>
                            <td style="background-color:whitesmoke;" align="center" >
                            </td>
                        </tr>
                        <?php
// ส่วนของ Task ที่ยังไม่ได้เพิ่มข้อมุล Budget หรือไม่ได้กำหนด Section ==================================

                        foreach ($taskAll as $key => $value) {
                            $row_total = 0;
                            $val = $value['task_name'];
                            $task_by = $value['task_by'];
                            $data_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');


                            if (!in_array($value['id'], $proInBudget)) :
                                $proInBudget[] = $value['id'];
                                ?>
                                <tr style="height: 75px">
                                    <td style="background-color:#fafafa;">
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                            if ($data_budget):
                                                echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                            else:
                                                echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'], 'other_group' => 'pms', 'segment' => ''])->buildBtnAdd();
                                            endif;
                                        else:
                                            echo Html::label($val, '', ['class' => 'label label-default', 'style' => 'font-size:15px']);
                                        endif;
                                        ?>
                                    </td>
                                    <td align="center" style="background-color:#fafafa;">
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

                            if ($task_by == 'task' && $data_budget['section'] == '' && !in_array($value['id'], $proInBudget)) :
                                ?>
                                <tr style="height: 75px">
                                    <td style="background-color:#fafafa;">
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                            if ($data_budget['task_item'] == $value['id']):
                                                echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                            else:
                                                echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'], 'other_group' => 'pms', 'segment' => ''])->buildBtnAdd();
                                            endif;
                                        else:
                                            echo Html::label($val, '', ['class' => 'label label-default', 'style' => 'font-size:15px']);
                                        endif;
                                        ?>
                                    </td>
                                    <td align="center" style="background-color:#fafafa;">
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
<!--                        <tr style="height: 75px">

                            <td align="center" style="background-color:whitesmoke;">-->
                        <?php
//                                $data_budget = SubjectManagementQuery::GetTableData($ezform_budget, ['task_item' => 'Credit Points(PMS)'], 'one');
//                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
//                                    if ($data_budget) {
//                                        $budget_credit = $data_budget['budget'];
//                                        echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label($budget_credit == '' ? '0' : $budget_credit )->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->buildBtnEdit($data_budget['id']);
//                                    } else {
//                                        echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->label('0')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => 'Credit Points(PMS)', 'other_group' => 'pms', 'segment' => ''])->buildBtnAdd();
//                                    }
//                                else:
//                                    echo Html::label($budget_credit == '' ? '0' : $budget_credit);
//                                endif;
                        ?>
                        <!--                            </td>
                        
                                                </tr>-->
                        <tr style="height: 75px">
                            <td align="right" style="background-color:whitesmoke;">
                                <strong><?= Yii::t('ezform', 'Sub Total') ?></strong>
                            </td>
                            <td align="center" style="background-color:whitesmoke;">

                            </td>
                            <?php
                            $budget_oh = 0;
                            ?>
                        </tr>

                        <tr style="height: 75px">
                            <td align="right" style="background-color:whitesmoke;">
                                <strong><?= Yii::t('ezform', 'Over Head') ?></strong>
                            </td>
                            <td align="center" style="background-color:whitesmoke;">
                                <?php
                                $data_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, ['task_item' => 'Over Head(PMS)'], 'one');
                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                    if ($data_budget) {
                                        $budget_oh = $data_budget['budget'];
                                        echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label($budget_oh == '' ? '0 %' : $budget_oh . '%')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->buildBtnEdit($data_budget['id']);
                                    } else {
                                        echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label('0 %')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => 'Over Head(PMS)', 'other_group' => 'pms', 'segment' => ''])->buildBtnAdd();
                                    }
                                else:

                                    if ($data_budget) {
                                        $budget_oh = $data_budget['budget'];
                                        echo Html::label($budget_oh == '' ? '0 %' : $budget_oh . '%');
                                    } else {
                                        echo Html::label('0 %');
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


    <!--- ขึ้นตารางใหม่ Performance Level -->
    <div class="col-md-7 sdbox-col" style="margin-left:-16px;">
        <div class="table-responsive" id="table-financial-scope1">
            <div id="content-table1">
                <table class="table table-bordered table-striped" id="table-financial1" style="width:<?= $table_width ?>px;">

                    <thead style="font-size: 16px;font-weight: bold;">
                        <tr style="height: 50px">
                            <?php for ($i = 0; $i < $maxcolumn; $i++) { ?>
                                <td style="text-align: center;background-color:#bebebe">
                                    Performance Level <?= $i + 1 ?>
                                </td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $proInBudget = [];
                        $row_total = [];
                        foreach ($sectionData as $sec_key => $sec_value) {
                            ?>
                            <tr style="height: 75px">
                                <?php for ($i = 0; $i < $maxcolumn; $i++) { ?>
                                    <td style="background-color:whitesmoke;"></td>
                                <?php } ?>
                            </tr>
                            <?php
                            foreach ($taskAll as $key => $value) {

                                $val = $value['task_name'];
                                $task_by = $value['task_by'];
                                $data_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');

                                if ($data_budget['section'] == $sec_value['id']) :
                                    $proInBudget[] = $value['id'];
                                    ?>
                                    <tr style="height: 75px;background-color:whitesmoke;" align="center">
                                        <?php for ($i = 0; $i < $value['seg_performance']; $i++) { ?>
                                            <td style="background-color:white;" >
                                                <?php
                                                if (!isset($row_total[$i]))
                                                    $row_total[$i] = 0;

                                                $seg_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and segment =' . ($i + 1), 'one');
                                                $budget = isset($data_budget['budget'] )?$data_budget['budget'] :0;
                                                $budget_oh = !empty($data_budget['budget']) ? $data_budget['budget'] : 0;
                                                $budget_seg = !empty($seg_budget['percen']) ? $seg_budget['percen'] : 100;
                                                $per_budget = (((float) $budget_seg / 100) * (float) $budget_oh);
                                                $row_total[$i] += $per_budget;

                                                if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :

                                                    if ($seg_budget) {
                                                        echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label($budget_seg . '% <label style="color:blue">(' . number_format($per_budget, 2) . ')</label>')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->buildBtnEdit($seg_budget['id']);
                                                    } else {
                                                        echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label('100% <label style="color:blue">(' . number_format($budget_oh, 2) . ')</label>')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'], 'other_group' => 'pms','budget'=>$budget, 'segment' => ($i + 1)])->buildBtnAdd();
                                                    }
                                                else:
                                                    if ($seg_budget) {
                                                        echo Html::label($budget_seg . '% <label style="color:blue">(' . number_format($per_budget, 2) . ')</label>');
                                                    } else {
                                                        echo Html::label('100% <label style="color:blue">(' . number_format($budget_oh, 2) . ')</label>');
                                                    }
                                                endif;
                                                ?>
                                            </td>
                                        <?php }
                                        ?>
                                        <?php for ($i = $value['seg_performance']; $i < $maxcolumn; $i++) { ?>
                                            <td style="background-color:whitesmoke;">
                                            </td>
                                        <?php } ?>
                                    </tr>

                                    <?php
                                endif;
                            }
                        }
                        ?>
                        <tr style="height: 75px">
                            <?php for ($i = 0; $i < $maxcolumn; $i++) { ?>
                                <td style="background-color:whitesmoke;"></td>
                            <?php } ?>
                        </tr>
                        <?php
// ส่วนของ Task ที่ยังไม่ได้เพิ่มข้อมุล Budget หรือไม่ได้กำหนด Section [Other] ==================================

                        foreach ($taskAll as $key => $value) {

                            $val = $value['task_name'];
                            $task_by = $value['task_by'];
                            $data_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and ( segment is NULL or segment = "")', 'one');


                            if (!in_array($value['id'], $proInBudget)) :
                                $proInBudget[] = $value['id'];
                                ?>
                                <tr style="height: 75px"  align="center">
                                    <?php for ($i = 0; $i < $value['seg_performance']; $i++) { ?>
                                        <td style="background-color:#fafafa;" align="center">
                                            <?php
                                            if (!isset($row_total[$i]))
                                                $row_total[$i] = 0;

                                            $seg_budget = SubjectManagementQuery::GetTableData($budget_pms_ezf, 'task_item = ' . $value['id'] . ' and task_item is not NULL and segment =' . ($i + 1), 'one');
                                            $budget = isset($data_budget['budget'] )?$data_budget['budget'] :0;
                                            $budget_oh = !empty($data_budget['budget']) ? $data_budget['budget'] : 0;
                                            $budget_seg = !empty($seg_budget['percen']) ? $seg_budget['percen'] : 100;
                                            $per_budget = (((float) $budget_seg / 100) * (float) $budget_oh);
                                            $row_total[$i] += $per_budget;

                                            if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                                if ($seg_budget):
                                                    echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label($budget_seg . '% <label style="color:blue">(' . number_format($per_budget, 2) . ')</label>')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->buildBtnEdit($seg_budget['id']);
                                                else:
                                                    echo EzfHelper::btn($budget_pms_ezf_id)->modal('modal-ezform-financial')->label('100% <label style="color:blue">(' . number_format($budget_oh, 2) . ')</label>')->reloadDiv($reloadDiv)->options(['class' => 'btn btn-default'])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'],'budget'=>$budget, 'other_group' => 'pms', 'segment' => ($i + 1)])->buildBtnAdd();
                                                endif;
                                            else:
                                                if ($seg_budget):
                                                    echo Html::label($budget_seg . '% <label style="color:blue">(' . number_format($per_budget, 2));
                                                else:
                                                    echo Html::label('100% <label style="color:blue">(' . number_format($budget_oh, 2) . ')</label>');
                                                endif;
                                            endif;
                                            ?>
                                        </td>
                                    <?php } ?>
                                    <?php for ($i = $value['seg_performance']; $i < $maxcolumn; $i++) { ?>
                                        <td style="background-color:whitesmoke;">
                                        </td>
                                    <?php } ?>
                                </tr>

                                <?php
                            endif;
                            ?>

                            <?php
// ข้อมูล Task ที่มีใน Budget แต่ไม่มีการกำหนด Section =================================================

                            if ($task_by == 'task' && $data_budget['section'] == '' && !in_array($value['id'], $proInBudget)) :
                                ?>
                                <tr style="height: 75px">
                                    <td style="background-color:#fafafa;" >
                                        <?php
                                        if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) :
                                            if ($data_budget['task_item'] == $value['id']):
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->buildBtnEdit($data_budget['id']);
                                            else:
                                                echo EzfHelper::btn($options['budget_ezf_id'])->modal('modal-ezform-financial')->reloadDiv($reloadDiv)->label("<label style='text-overflow: ellipsis;width:180px;overflow: hidden;'>" . $val . "</label>")->options(['class' => 'btn btn-default task_item_btn', 'title' => $val])->initdata(['pro_name' => '', 'group_name' => '', 'task_item' => $value['id'], 'other_group' => 'pms'])->buildBtnAdd();
                                            endif;
                                        else:
                                            echo Html::label($val, '', ['class' => 'label label-default', 'style' => 'font-size:15px']);
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
                        }
                        ?>
                    </tbody>

                    <tfoot>

                        <tr style="height: 75px;background-color:whitesmoke;" align="center">
                            <?php
                            for ($i = 0; $i < count($row_total); $i++) {
                                if (!isset($grand_total[$i]))
                                    $grand_total[$i] = 0;
                                $grand_total[$i] += $row_total[$i];
                                ?>

                                <td style="background-color:white;"><strong><?= number_format($row_total[$i], '2') ?></strong></td>
                            <?php } ?>
                        </tr>

                        <tr style="height: 75px;background-color:whitesmoke;"  align="center">

                            <?php
                            //Over Head
                            $per_over = SubjectManagementQuery::GetTableData($budget_pms_ezf, ['task_item' => 'Over Head(PMS)'], 'one');

                            for ($i = 0; $i < count($row_total); $i++) {
                                $data_over = (((float) isset($per_over['budget']) ? $per_over['budget'] : 0 / 100) * (float) $row_total[$i]);

                                if (!isset($grand_total[$i]))
                                    $grand_total[$i] = 0;
                                $grand_total[$i] += $data_over;
                                ?>
                                <td style="background-color:white;"><strong><?= number_format($data_over, '2') ?></strong></td>
                            <?php }
                            ?>
                        </tr>

                        <tr style="height: 75px;background-color:whitesmoke;"  align="center">
                            <?php
//Grand Total
                            $grand_total = isset($grand_total) ? $grand_total : null;
                            if ($grand_total) {
                                for ($i = 0; $i < count($grand_total); $i++) {
                                    ?>
                                    <td style="background-color:white;"><strong><?= number_format($grand_total[$i], '2') ?></strong></td>
                                            <?php
                                        }
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
        $('.task_item_btn').each(function (i, e) {
            $(e).find('label').css('width', screen_width);
        })
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>