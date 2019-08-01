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
$headReport = "";
$payee = "";
if ($type == "patient") {
    $headReport = "  ";
    $payee = $subject_id;
}
$ezform_section = EzfQuery::getEzformOne($section_ezf_id);
$ezform_budget = EzfQuery::getEzformOne($budget_id);

$subjectList = [];
$sectionPro = \appxq\sdii\utils\SDUtility::string2Array($sectionProcedure);
foreach ($checkbox as $ck => $ckVal) {
    foreach ($sectionPro[$ckVal['section']]['approved'] as $key => $value) {
        $subjectList[$ckVal['section']][] = $value;
    }
}

$url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
$projectData = backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
$userdata = EzfQuery::getUserProfile(Yii::$app->user->id);

?>

<html>
    <head>
        <style>
            .printarea{
                padding: 50px 35px;
            }
            .header{
                font-weight : bold;
                text-align: center;
                font-size:18px;
            }
            .payee-info-table{
                width:100%;
                font-size:18px;
                border-collapse: collapse;
            }
            .payee-info-table td{
                padding:5px;
            }
            .payee-info-table2{
                width:100%;
                font-size:18px;
                border-collapse: collapse;
                font-weight : bold;
            }
            .payee-info-header{
                font-weight:bold;
            }

            .border-table{
                font-size:18px;
                width:100%;
                border-collapse: collapse;
            } 
            .border{
                border: 1px solid #000;
            }
            .noborder{
                border: 0 ;
            }
            .footer{
                text-align:right;
                font-size:18px;
                padding-right:20px;
                padding-top: 60px;
            }
            .text-bold{
                font-weight:bold;
            }
        </style>
    </head>
    <body>
        <div class="printarea">

            <p class="header">Site Payment Information </p>
            <table class="payee-info-table">
                <tr>
                    <td class="payee-info-header" width="25%">Payee</td>
                    <td width="45%">..................................</td>
                    <td class="payee-info-header"  width="30%">Payment Address:</td>
                </tr>
                <tr>
                    <td class="payee-info-header">Amount</td>
                    <td><?= number_format($icome) ?> THB </td>
                    <td rowspan="7" valign="top"> 123 mitraparp Highway, Muang, Khon Kaen 40002, Thailand <br />
                        tel <br />
                        fax <br />
                    </td>
                </tr>
                <tr>
                    <td class="payee-info-header">Study name</td>
                    <td><?=$projectData['projectname']?></td>
                </tr>
                <tr>
                    <td class="payee-info-header">Request By</td>
                    <td><?=$userdata['firstname'].' '.$userdata['lastname'] ?></td>
                </tr>
                <tr>
                    <td class="payee-info-header">Request Date</td>
                    <td><?= SubjectManagementQuery::convertDate(date('Y-m-d'))?></td>
                </tr>
                <tr>
                    <td class="payee-info-header">Approved By</td>
                    <td>Accountant Name</td>
                </tr>
                <tr>
                    <td class="payee-info-header">Approved Date</td>
                    <td>30Jan2018</td>
                </tr>

            </table>   
            <div>
                <p class="header">Itemized cost</p>

                <table width="100%" class="payee-info-table2">
                    <tr>
                        <td width="50%">Payee: Clinical research center </td>
                        <td width="50%">Study: GeCCICA</td>
                    </tr>
                </table>
                <table class="border-table">
                    <tr class="border">
                        <td class="border" width="40%">Item</td>
                        <td class="border" width="25%">type</td>
                        <td class="border" width="20%">Transaction Date</td>
                        <td class="border" width="15%">Amount</td>
                    </tr>
                    <?php
                    $total_budget =0;
                    foreach ($subjectList as $key => $value):
                        $amount = 0;
                        $approveDate='';
                        $financialType = '';
                        $section = SubjectManagementQuery::GetTableData($ezform_section,['id'=>$key],'one');
                        
                        foreach ($value as $pro => $valPro) {
                            $budget = SubjectManagementQuery::GetTableData($ezform_budget, ['pro_name' => $valPro['id'], 'visit_name' => $visit_id, 'group_name'=>$group_id], 'one');
                            $approved = SubjectManagementQuery::GetTableData('subject_visit_approved', ['procedure_name' => $valPro['id'], 'visit_name' => $visit_id], 'one',null,['order'=>'desc','column'=>'approved_date']);
                            if($approveDate=='')$approveDate=$approved['approved_date'];
                            if($financialType=='')$financialType=$valPro['financial_type'];
                            $amount += $valPro['budget'];
                            $total_budget += $valPro['budget'];
                        }
                        
                        $ezf_data= SubjectManagementQuery::GetTableData('zdata_financial_type', ['id'=>$financialType],'one');
                        
                        ?>
                        <tr class="border">
                            <td class="border"><?= $section['section_name'] ?></td>
                            <td class="border"><?=$ezf_data['financial_type']?></td>
                            <td class="border"><?= date('d/m/Y', strtotime($approveDate)) ?></td>
                            <td class="border"><?= number_format($amount) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="border">
                        <td class="border">&nbsp;</td>
                        <td class="border">&nbsp;</td>
                        <td class="border">&nbsp;</td>
                        <td class="border">&nbsp;</td>
                    </tr>
                    <tr >
                        <td ></td>
                        <td class="text-bold">Itemize total</td>
                        <td ></td>
                        <td class="text-bold"><?= number_format($total_budget) ?> THB</td>

                    </tr>
                </table>
                <p class="footer">
                    Payee Signature.........................................<br/>
                    Print name..................................................<br/>
                    Date............................................................<br/>
                    <?= $id ?>
                </p>
            </div>

        </div>

        <script>
            setTimeout(function () {
                window.print();
            }, 1000);

        </script>

    </body>

</html>    
