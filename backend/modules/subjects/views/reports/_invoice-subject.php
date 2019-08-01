<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$ezform_budget = EzfQuery::getEzformOne($budget_id);
$profile_form = EzfQuery::getEzformOne($profile_ezf_id);
$detail_form = EzfQuery::getEzformOne($detail_ezf_id);
$data_subject = SubjectManagementQuery::getInvoiceSubjectReport($ezform_budget, $group_id);

$url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
//$url_curr="udon.work.ncrc.in.th";
$projectData = backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
$userdata = EzfQuery::getUserProfile(Yii::$app->user->id);
?>
<div class="row" style="font-size:18px;text-align: center;">
    <label ><strong>Invocie</strong></label><br/>
</div>
<div class="clearfix"></div>
<div class="row " style="padding:0 25px 0 25px;text-align:right;">
    <p class="pull-right">Invoice No. TH44-002</p>
    <p class="pull-right">Date 31 Jul 2017</p>
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Customer Name: </strong></label>.....................................................
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Address: </strong></label> 8 Temasek Boulevard#24-02 Suntec Tower Three Singapore 038988
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Protocol: </strong></label> <?=$projectData['projectname']?>
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Protocol: </strong></label> <?=$projectData['projectacronym']?>
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Protocol No: </strong></label> FF01
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <label><strong>Site no. </strong></label> TH44 Srinakarind hospital, Khon Kaen University
</div>
<div class="row " style="padding: 0 25px 0 25px;">
    <table width="100%" border="1" cellspacing="0">
        <thead>
            <tr>
                <td width="10%">Order</td>
                <td widgth="65%">List</td>
                <td width="25%" align="center">Amount(THB)</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $order = 1;
            $sumBudget = 0;
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);
            foreach ($dat_print as $key => $val):
                $sub_num = SubjectManagementQuery::GetTableData($profile_form, ['id' => $val['target']], 'one');
                //$sub_det = SubjectManagementQuery::GetTableData($detail_form, ['target' => $val['subject_target_id'], 'visit_name' => $val['visit_name']], 'one');
                $sumBudget += $val['budget'];
                ?>
                <tr>
                    <td align="center"><?= $order ?></td>
                    <td><?= $sub_num['subject_number'] . ' ' . $visitSchedule[$val['visit_name']]['visit_name'] . " Visit Date " . SubjectManagementQuery::convertDate($val['visit_date']) ?></td>
                    <td align="right"><?= number_format($val['budget']) ?></td>
                </tr>

                <?php
                $order ++;
                $numberStr = "";
                if(Yii::t('subjects', 'Check Language') == 'Eng'){
                    $numberStr=backend\modules\subjects\classes\ReportQuery::engFormat($sumBudget);
                }else{
                    $numberStr=backend\modules\subjects\classes\ReportQuery::num2wordsThai($sumBudget);
                }
            endforeach;
            ?>
             <tr>
                    <td align="center"><strong>รวม</strong></td>
                    <td align=""><?= $numberStr?></td>
                    <td align="right"><?= number_format($sumBudget) ?></td>
                </tr>
        </tbody>
    </table>
</div>
<div class="clearfix"></div>
<br/><br/>
<br/>
<div class="row " style="padding: 0 25px 0 25px;">
    <div align="right" width="250px" >
            <label align="center">( <?=$projectData['pi_name']?> )</label><br/>
            <label>Principal Investigator</label>
        
    </div>
</div>
<div class="clearfix"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    setTimeout(function () {
        window.print();
    }, 1000);
</script>
<?php \richardfan\widget\JSRegister::end(); ?>