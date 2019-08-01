<?php
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use yii\bootstrap\ActiveForm;
// get config
$genid = SDUtility::getMillisecTime();
$sqlsetting = base64_encode($options['sqlsetting']);
$reporttype = $options['reporttype'];
$selectdate = $options['selectdate'];
$selectsite = $options['selectsite'];
$selectbox = $options['textbox'];
$day = isset($options['day']) ? $options['day'] : false;
$month = isset($options['month']) ? $options['month'] : false;
$year = isset($options['year']) ? $options['year'] : false;
$valueRow = $options['reporttypeval'];
$valueCol = $options['reporttypevariable'];
$graphname = $options['graphname'] != '' ? $options['graphname'] : Yii::t('graphconfig', 'Untitled');
$selectsitedef = $options['selectsitedef'];
$showselectsite = $options['showselectsite'];
$width = isset($options['width']) ? $options['width'] : 12;
$c = explode('|', $selectsitedef);
$code = isset($c[0]) ? $c[0] : '';
$sitename = isset($c[1]) ? $c[1] : '';
$title=$options['title'];

$this->registerJs("
loadReportWidget('".$genid."');
function loadReportWidget(id){
    var parentid = id;
    var report_type =$('#'+id+' .advance_site_code').val();
    var start_date = $('#'+id+' .advance_date_start').val();
    var end_date = $('#'+id+' .advance_date_end').val();
    var site_code = $('#'+id+' .allsite').val();
    var selectbox = $('#'+id+' .advance_box').val();
    var sitechoose = $('#'+id+' .sitechoose').val();
    var valueRow = $('#'+id+' .valueRow').val();
    var valueCol = $('#'+id+' .valueCol').val();
    var sql = $('#'+id+' .sqlsetting').val();
    var title = $('#'+id+' .title').val();

    $('#'+id+' .reporttype-option').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');

    setTimeout(function(){ 
        $.ajax({
            method: 'GET',
            url: '" . Url::to(['/graphconfig/graphconfig/get-singlegraph-report-data']) . "',
            data: {selectbox:selectbox,end_date:end_date,
            start_date: start_date, parentid: parentid,report_type:report_type,
            site_code:site_code,sitechoose:sitechoose,valueCol:valueCol,valueRow:valueRow, sql:sql, title:title},
            dataType: 'HTML',
            success: function(result) {
                $('#'+id+' .reporttype-option').html(result);
            }
        });
    }, 500); 
}

");

?>
<div id="<?= $genid ?>" class="col-md-<?= $width ?>" >
    <input type="hidden" class="advance_site_code" value="<?php echo $reporttype ?>">
    <input type="hidden" class="advance_box" value="<?php echo $selectbox ?>">
    <input type="hidden" class="sitechoose" value="<?php echo $selectsitedef ?>">
    <input type="hidden" class="valueRow" value="<?php echo $valueRow ?>">
    <input type="hidden" class="valueCol" value="<?php echo $valueCol ?>">
    <input type="hidden" class="sqlsetting" value="<?php echo $sqlsetting ?>">
    <input type="hidden" class="title" value="<?php echo $title ?>">

    <div class="panel panel-default" style="border-color: #dae7f6;">

        <div class="panel-heading" style="text-align: center; background-color: #dae7f6; color: #000;">
            <i class="fa fa-pie-chart" aria-hidden="true"></i> <?= $graphname . ' ' ?><?= $day ? date("d") . ' ' : '' ?><?= $month ? date("M") . ' ' : '' ?><?= $year ? date("Y") . ' ' : '' ?>
        </div>

        <div class="panel-body">

            <?php
            if ($showselectsite == 1) {
                $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form-' . $genid]]);

                if ($selectsite == 1) {
                    ?>
                    <div style="margin-bottom:15px;" class="col-md-12">
                        <?php
                        $sitevalue = $code != '' ? $code . '|' . $sitename : '';
                        echo Select2::widget([
                            'name' => 'state_2',
                            'value' => $sitevalue,
                            'id' => "site$genid",
                            'options' => ['placeholder' => Yii::t('graphconfig', 'Search site ...'), 'readonly' => '', 'class' => 'advance_config_input allsite col-md-' . $width],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                                'ajax' => [
                                    'url' => '/graphconfig/graphconfig/sitecode-list',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            ],
                        ]);
                        ?>
                    </div>
                <?php } elseif ($selectsite == 0) {
                    ?>
                    <div class="col-md-12" style="margin-bottom:15px; font-size:16px;"><b>  <?= Yii::t('graphconfig', 'Your site') ?></b> : <?= $code ?>  <?= $sitename ?> </div>
                <?php } elseif ($selectsite == 2) {
                    ?>

                    <div class="col-md-12" style="margin-bottom:15px; font-size:16px;"><b>  <?= Yii::t('graphconfig', 'Your site') ?> </b> : <?= $code ?>  <?= $sitename ?> </div>
                    <?php
                } ActiveForm::end();
            }
            ?>

            <?php if ($selectdate == 1) { ?>

                <div class="col-md-6"><b><?= Yii::t('graphconfig', 'Start date') ?></b>
                    <input type="date"  class="form-control advance_date_start advance_config_input" name="cccc" id="start<?= $genid ?>" data-id="<?= $genid ?>" value="<?php echo date('Y-m-d', strtotime('-1 year')) ?>" >
                </div>
                <div class="col-md-6"><b><?= Yii::t('graphconfig', 'Stop date') ?></b>
                    <input type="date" class="form-control advance_date_end advance_config_input" name="ccccx" id="end<?= $genid ?>" data-id="<?= $genid ?>" value="<?php echo date('Y-m-d') ?>">
                </div>

            <?php }
            ?>
            <div class="reporttype-option col-md-12">

            </div>

        </div>
    </div>
</div>