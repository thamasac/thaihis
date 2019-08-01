<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

if ($data) {
    $genid = $data['id'];
    $sqlsetting = $data['sql_command'];
    $reporttype = $data['report_type'];
    $config = json_decode($data['config_json'], true);
    $selectdate = $config['selectdate'];
    $selectsite = $config['selectsite'];
    $selectbox = $config['textbox'];
    $day = isset($config['day']) ? $config['day'] : false;
    $month = isset($config['month']) ? $config['month'] : false;
    $year = isset($config['year']) ? $config['year'] : false;
    $width = $config['width'];
    $graphname = $config['graphname'] != '' ? $config['graphname'] : Yii::t('graphconfig', 'Untitled');
    $valueRow = $config['reporttypeval'];
    $valueCol = $config['reporttypevariable'];
    $selectsitedef = $config['selectsitedef'];
    $showselectsite = $config['showselectsite'];
    $c = explode('|', $selectsitedef);
    $code = isset($c[0]) ? $c[0] : '';
    $sitename = isset($c[1]) ? $c[1] : '';
}
?>
<div id="<?= $genid ?>" class="col-md-<?= $width ?>" >
    <input type="hidden" class="advance_site_code" value="<?php echo $reporttype ?>">
    <input type="hidden" class="advance_width" value="<?php echo $width ?>">
    <input type="hidden" class="advance_box" value="<?php echo $selectbox ?>">
    <input type="hidden" class="sitechoose" value="<?php echo $selectsitedef ?>">
    <input type="hidden" class="valueRow" value="<?php echo $valueRow ?>">
    <input type="hidden" class="valueCol" value="<?php echo $valueCol ?>">

    <div class="panel panel-default" style="border-color: #dae7f6;">

        <div class="panel-heading" style="text-align: center; background-color: #dae7f6; color: #000;">
            <i class="fa fa-pie-chart" aria-hidden="true"></i> <?= $graphname . ' ' ?><?= $day ? date("d") . ' ' : '' ?><?= $month ? date("M") . ' ' : '' ?><?= $year ? date("Y") . ' ' : '' ?>
            <a class="btn-maximize" data-url="<?= Url::to(['/graphconfig/graphconfig/get-report-data']) ?>" title="<?= Yii::t('graphconfig', 'Click to open new page.') ?>" style="cursor:pointer"><i class="fa fa-list-alt  pull-right" aria-hidden="true"></i></a>
            <a class="btn-clone"  data-url="<?= Url::to(['/graphconfig/graphconfig/get-report-data']) ?>"  title="<?= Yii::t('graphconfig', 'Click to expand.') ?>" style="cursor:pointer"><i class="fa fa-copy pull-right" aria-hidden="true"></i></a>
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
                                'minimumInputLength' => 3,
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
                    <input type="date"  class="form-control advance_date_start advance_config_input" name="cccc" id="start<?= $genid ?>" value="<?php echo date('Y-m-d', strtotime('-1 year')) ?>" >
                </div>
                <div class="col-md-6"><b><?= Yii::t('graphconfig', 'Stop date') ?></b>
                    <input type="date" class="form-control advance_date_end advance_config_input" name="ccccx" id="end<?= $genid ?>" value="<?php echo date('Y-m-d') ?>">
                </div>

            <?php }
            ?>
            <div class="reporttype-option col-md-12">

            </div>

        </div>
    </div>
</div>


<?php $this->registerJs("
     loadReportOption('$genid');

$('#$genid .advance_config_input').change( function(){
    var id = '$genid';
   loadReportOption(id);
 
});
$('#$genid .btn-clone').click(function(){
    var parentid = '$genid';
    var report_type =$('#'+parentid+' .advance_site_code').val();
    var start_date = $('#'+parentid+' .advance_date_start').val();
    var end_date = $('#'+parentid+' .advance_date_end').val();
    var onwith = $('#'+parentid+' .advance_width').val();
    var site_code = $('#'+parentid+' .allsite').val();
    var selectbox = $('#'+parentid+' .advance_box').val();
    var sitechoose = $('#'+parentid+' .sitechoose').val();
    var valueRow = $('#'+parentid+' .valueRow').val();
    var valueCol = $('#'+parentid+' .valueCol').val();
    var title = '$graphname';
    var url = $(this).attr('data-url'); 
    
    $('#modal-ezform-main .modal-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-main').modal('show');
        $.ajax({
            method: 'GET',
            url: url,
            data: {selectbox:selectbox,onwith:onwith,end_date:end_date,
                start_date: start_date, parentid: parentid,report_type:report_type,
                site_code:site_code,sitechoose:sitechoose,valueCol:valueCol,valueRow:valueRow,modal:1,title:title},
                dataType: 'HTML',
                success: function(result) {
                $('#modal-ezform-main .modal-content').html(result);
                return false;
            }
    });

});
$('#$genid .btn-maximize').click(function(e){
    var parentid = '$genid';
    var report_type =$('#'+parentid+' .advance_site_code').val();
    var start_date = $('#'+parentid+' .advance_date_start').val();
    var end_date = $('#'+parentid+' .advance_date_end').val();
    var onwith = $('#'+parentid+' .advance_width').val();
    var site_code = $('#'+parentid+' .allsite').val();
    var selectbox = $('#'+parentid+' .advance_box').val();
    var sitechoose = $('#'+parentid+' .sitechoose').val();
    var valueRow = $('#'+parentid+' .valueRow').val();
    var valueCol = $('#'+parentid+' .valueCol').val();
    var title = '$graphname';
    var newpage = 1;
     e.preventDefault(); 
    var url = $(this).attr('data-url'); 
    window.open(url+'?selectbox='+selectbox+'&onwith='+onwith+'&end_date='+end_date+'&start_date='+start_date+'&parentid='+parentid+'&report_type='+report_type+'&site_code='+site_code+'&sitechoose='+sitechoose+'&valueCol='+valueCol+'&valueRow='+valueRow+'&newpage='+newpage+'&title='+title, '_blank'); 
});
"); ?>