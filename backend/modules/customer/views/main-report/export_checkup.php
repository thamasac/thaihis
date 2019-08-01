<?php

use yii\helpers\Url;

$addonScript = "";
if ($dataForm['report_type'] == 'report_checkup') {
    $urlSummary = Url::to(['/customer/main-report/checkup-project-summary', 'visit_date' => $dataForm['create_date'], 'project_id' => $dataForm['order_tran_dept'], 'size' => '6', 'project_type' => $dataForm['project_type']]);
    $urlDetailAll = Url::to(['/customer/main-report/print-detail', 'visit_date' => $dataForm['create_date'], 'right_code' => $dataForm['order_tran_code'], 'project_id' => $dataForm['order_tran_dept']]);

    $addonScript = "
        myWindow = window.open('$urlSummary', '_blank');
        myWindow = window.open('$urlDetailAll', '_blank');
        ";
} elseif ($dataForm['report_type'] == 'report_checkup_excel') {
    $url = Url::to(['/customer/main-report/checkup-project-summary-excel', 'visit_date' => $dataForm['create_date'], 'project_id' => $dataForm['order_tran_dept'], 'project_type' => $dataForm['project_type']]);
    $addonScript = "
        myWindow = window.open('$url', '_blank');
    ";
}

$this->registerJs(" 
        $addonScript
    ");
?>