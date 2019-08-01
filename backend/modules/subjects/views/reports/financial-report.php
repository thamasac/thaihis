<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Url;
use backend\modules\subjects\classes\ReportQuery;

$this->registerJsFile('@web/js/excellentexport.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$categories = [];
foreach ($month as $val) {
    $categories[] = $val[0];
}

$dataChart2 = [];
$dataChart = [
    ['Total Balance', (float) ($sumBalance)],
    ['Total Expense', (float) ($sumExpense)],
];

$title = [
    'text' => 'Total Revenue <br/>' . number_format($sumRevenue),
    'align' => 'center',
    'verticalAlign' => 'middle'
];
$title2 = [
    'text' => 'Total Revenue ' . number_format($sumRevenue),
    'align' => 'center',
    'verticalAlign' => 'middle'
];

foreach ($month as $key2 => $value) :
    $balance[$key2] = $totalRevenue[$key2] - $totalExpense[$key2];
endforeach;

foreach ($month as $key2 => $value) :
    if ($key2 > 0):
        $totalBalance[$key2] = $balance[$key2] + $totalBalance[$key2 - 1];
    endif;
endforeach;

$series [] = ['name' => 'Balance', 'data' => $totalBalance];
$series [] = ['name' => 'Expense', 'data' => $totalExpense];

?>
<style>
    .active-select{
        background-color: #fbf069;
    } 
</style>
<br/>
<?php if (isset($start_date) && $start_date != null): ?>
    <div class="col-md-12">
        <label style="font-size:18px;">ข้อมูล ณ วันที่ <?= SubjectManagementQuery::convertDate($start_date, 'full') ?> ถึง <?= SubjectManagementQuery::convertDate($end_date, 'full') ?></label>
    </div>
<?php endif; ?>
<br/>
<div class="col-md-4" id="show-highchart">
    <?= \backend\modules\subjects\classes\HighchartReportBuider::ui()->dataChart($dataChart)->title($title)->graphheight("400")->type("pie")->buildHighchart() ?>
</div >

<br/>
<div class="col-md-8" id="show-line-highchart">
    <?=
    $this->renderAjax('line-chart-report', [
        'title' => $title2,
        'dataChart' => $dataChart,
        'type' => isset($type)?$type:'',
        'series' => isset($series)?$series:'',
        'renderDiv' => isset($renderDiv)?$renderDiv:'',
        'categories' => isset($categories)?$categories:'',
        'graphheight' => isset($graphheight)?$graphheight:'',
    ])
    ?>
</div>
<div class="clearfix"></div>
<div class="modal-body">
    <br/>

    <div id="display_revenue_report">
        <?= Html::a('<i class="	fa fa-download"></i> ' . Yii::t('ezform', 'Export report'), 'javascript:void(0)', ['class' => 'btn btn-success btn_export_report pull-right']) ?>
        <br/><br/>
        <div id='table_report_financial'  class="table-responsive">
            <table  class="table table-bordered table-striped"  style="width:100%;">
                <thead style="font-size: 16px;font-weight: bold;background-color:gainsboro;">
                    <tr>
                        <td width="15%">Items</td>
                        <?php foreach ($month as $key => $value): ?>
                            <td align='center'><?= $value[0] ?></td>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="13"><h4>Revenue</h4></td>
                    </tr>
                    <?php
                    foreach ($dataRevenue['items'] as $key => $value) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo "<strong>" . $value . "</strong>";
                                ?>
                            </td>

                            <?php
                            foreach ($month as $key2 => $value2) :
                                $amount = $dataRevenue['amount'][$key][$key2];
                                ?>
                                <td align="center" >
                                    <label><?= number_format($amount) ?></label><br/>
                                </td>
                                <?php
                            endforeach;
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td><label>Total Revenue</label></td>
                        <?php
                        foreach ($totalRevenue as $key2 => $value2) :
                            ?>
                            <td align="center" >
                                <label><?= number_format($value2) ?></label><br/>
                            </td>
                            <?php
                        endforeach;
                        ?>
                    </tr>
                    <tr>
                        <td colspan="13"><h4>Expense</h4></td>
                    </tr>
                    <?php
                    foreach ($dataExpense['items'] as $key => $value) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo "<strong>" . $value . "</strong>";
                                ?>
                            </td>

                            <?php
                            foreach ($month as $key2 => $value2) :
                                $amount = $dataExpense['amount'][$key][$key2];
                                ?>
                                <td align="center" >
                                    <label><?= number_format($amount) ?></label><br/>
                                </td>
                                <?php
                            endforeach;
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td><label>Total Expense</label></td>
                        <?php
                        foreach ($totalExpense as $key2 => $value2) :
                            ?>
                            <td align="center" >
                                <label><?= number_format($value2) ?></label><br/>
                            </td>
                            <?php
                        endforeach;
                        ?>
                    </tr>
                    <tr>
                        <td colspan="13"><h4>Balance</h4></td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Balance</strong>
                        </td>

                        <?php
                        foreach ($month as $key2 => $value) :
                            $balance[$key2] = $totalRevenue[$key2] - $totalExpense[$key2];
                            ?>
                            <td align="center" >
                                <label><?= number_format($balance[$key2]) ?></label><br/>
                            </td>
                            <?php
                        endforeach;
                        ?>
                    </tr>
                </tbody>
                <tfoot>

                    <tr>
                        <td width="15%"><label>Total Income</label></td>
                        <?php
                        foreach ($month as $key2 => $value) :
                            if ($key2 > 0):
                                $totalBalance[$key2] = $balance[$key2] + $totalBalance[$key2 - 1];
                                ?>
                                <td align="center" >
                                    <label><?= number_format($totalBalance[$key2]) ?></label><br/>
                                </td>
                            <?php else: ?>
                                <td align="center" >
                                    <label><?= number_format($totalBalance[0]) ?></label><br/>
                                </td>
                            <?php
                            endif;
                        endforeach;
                        ?>
                    </tr>
                </tfoot>
            </table>

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
    $('.btn-search').click(function () {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var url = '<?=
Url::to(['/subjects/subject-management/all-subject-payment',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'view' => 'all-subject-payment-invoice',
])
?>';
        $('#modal_tracking_invoice').modal('show');
        $('#modal_tracking_invoice').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal_tracking_invoice').find('.modal-content').load(url + '&start_date=' + start_date + '&end_date=' + end_date);
    });

    $('.btn_export_report').click(function () {
        var dataTable = $("#table_report_financial");
        var headerName = ("<tr><td colspan='13'><h4>Financial Report</h4></td></tr>");
        dataTable.prepend(headerName);
        this.download = "financial-report.xls"
        ExcellentExport.excel(this, 'table_report_financial', 'Financial Report Sheet');
    });

    function getUiAjax(url, div) {
        $.get(url, function (data) {
            $('#' + div).html(data);
        })
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>


