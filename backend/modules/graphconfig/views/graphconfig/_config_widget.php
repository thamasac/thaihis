<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use appxq\sdii\utils\SDUtility;

if ($data) {
    $genid = $data['id'];
    $sqlsetting = $data['sql_command'];
    $reporttype = $data['report_type'];
    $order = $data['conf_order'];
    $config = json_decode($data['config_json'], true);
    $selectdate = $config['selectdate'];
    $selectsite = $config['selectsite'];
    $width = $config['width'];
    $value = $config['reporttypevariable'];
    $value2 = $config['reporttypeval'];
    $graphname = $config['graphname'];
    $textbox = $config['textbox'];
    $selectsitedef = $config['selectsitedef'];
    $showselectsite = $config['showselectsite'];
    $day = isset($config['day']) ? $config['day'] : '';
    $month = isset($config['month']) ? $config['month'] : '';
    $year = isset($config['year']) ? $config['year'] : '';
    $sitesp = explode('|', $selectsitedef);
    $scode = isset($sitesp[0]) ? $sitesp[0] : '';
    $sname = isset($sitesp[1]) ? $sitesp[1] : '';
} else {
    $genid = SDUtility::getMillisecTime();
    $sqlsetting = '';
    $reporttype = 0;
    $order = '99999';
    $selectdate = null;
    $selectsite = null;
    $width = '6';
    
    $value = '';
    $value2 = '';
    $graphname = '';
    $textbox = '';
    $selectsitedef = '';

    $showselectsite = '';

    $bgcolor = '#eee';
    $border_color = '#aaa';
    $scode = '';
    $sname = '';
    $day = '';
    $month = '';
    $year = '';
}


$datawidth = ['1' => '1/12 (8.33%)',
    '2' => '2/12 (16.67%)',
    '3' => '3/12 (25%)',
    '4' => '4/12 (33.33%)',
    '5' => '5/12 (41.67%)',
    '6' => '6/12 (50%)',
    '7' => '7/12 (58.33%)',
    '8' => '8/12 (66.67%)',
    '9' => '9/12 (75%)',
    '10' => '10/12 (83.33%)',
    '11' => '11/12 (91.67%)',
    '12' => '12/12 (100)',
];
?>
<div id="<?= $genid ?>" class="report-widget-box">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-danger advance-report-delete pull-right" type="button" id="<?= $genid ?>-remove"><i class="glyphicon glyphicon-remove"></i></button>
        </div>
        <div class="col-md-12"><label><?= Yii::t('graphconfig', 'SQL setting'); ?></label></div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <?= Html::textarea('forms-sqlsetting', $sqlsetting, ['class' => 'form-control sqlsetting', 'rows' => 10, 'id'=>'sqlsetting']) ?>
            <?= Html::hiddenInput('forms-id', $genid, ['class' => 'id', 'id'=>"forms-id"]) ?>
            <?= Html::hiddenInput('forms-module', $module, ['class' => 'module']) ?>
        </div>
        <div class="col-md-1">
            <div>
               <button class="btn btn-primary sql-exec pull-right" data-id="<?= $genid ?>" type="button" id="sql-exec"><i class="glyphicon glyphicon-refresh"></i></button><br />
            </div>
            <div><b><?= Yii::t('graphconfig', 'SQL test'); ?></b></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Graph type'); ?></label></div>
        <div class="col-md-12">
            <?=
            Html::radioList('forms-reporttype', $reporttype, [0 =>  Yii::t('graphconfig', 'Data table'),
                1 => Yii::t('graphconfig', 'Pie graph'),
                2 => Yii::t('graphconfig', 'Line graph'),
                3 => Yii::t('graphconfig', 'Bar graph')], ['class' => 'form-control reporttype', 'id'=>'reporttype'])
            ?>
            <?= Html::hiddenInput('forms-reporttypevariable', $value, ['class' => 'reportval', 'id'=>'reportval']) ?>
            <?= Html::hiddenInput('forms-reporttypeval', $value2, ['class' => 'reportval2', 'id'=>'reportval2']) ?>
        </div>
    </div>
    <div class="row " id="reporttype-option">

    </div>

    <div class="row">
        <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Option') ?></label></div>
        <div class="col-md-12">
            <?= Html::checkbox('forms-selectdate', $selectdate, []) ?> <?= Yii::t('graphconfig', 'Can select date') ?>  
            <?= Html::checkbox('forms-showselectsite', $showselectsite, []) ?> <?= Yii::t('graphconfig', 'Show site') ?> <br />
            <?=
            Html::radioList('forms-selectsite', $selectsite, [0 => Yii::t('graphconfig', 'Select own site'),
                1 => Yii::t('graphconfig', 'Select all'),
                2 => Yii::t('graphconfig', 'Select site')], ['class' => 'form-control selectsite', 'id'=>'selectsite'])
            ?><br />
        </div>
        <div class="col-md-12 selectsitedef">
            <br>
            <?php
            $sitevalue = $scode != ''? $scode . '|' . $sname : '';
         
            echo Select2::widget([
                'name' => 'forms-selectsitedef',
                'value' =>$sitevalue,
                'options' => ['placeholder' => Yii::t('graphconfig', 'Search site ...'), 'readonly' => '', 'class' => 'selectsitedef', 'id' => 'selectsitedef'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 2,
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

    </div>

    <div class="row">
        <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Text box') ?></label></div>
        <div class="col-md-12">
        <?= Html::textarea('forms-textbox', $textbox, ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Graph name') ?></label></div>
            <div class="col-md-6">
                <?= Html::textInput('forms-graphname', $graphname, ['class' => 'form-control form-input']) ?>
            </div>
            <div class="col-md-2">
                 <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('forms-day',$day,['label'=>Yii::t('graphconfig', 'Add Day To Title')]) ?>
            </div>
             <div class="col-md-2">
                 <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('forms-month',$month,['label'=>Yii::t('graphconfig', 'Add Month To Title')]) ?>
            </div>
             <div class="col-md-2">
                 <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('forms-year',$year,['label'=>Yii::t('graphconfig', 'Add Year To Title')]) ?>
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Width') ?></label></div>
            <div class="col-md-12"><?= Html::dropDownList('forms-width', $width, $datawidth, ['class' => 'form-control form-input']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Order') ?></label></div>
            <div class="col-md-12"><?= Html::dropDownList('forms-order', $order, [], ['class' => 'form-control form-input order', 'id'=>'forms-order']) ?>
                
            </div>

        </div>
    </div>
</div>
<?php 
$delconfirm = Yii::t('graphconfig', 'Delete confirm!');
$this->registerJs("
     loadReportOption('$genid');
         checkSelecthosp('$genid');
             $('#selecthospdef-box').hide();
//

$('#sql-exec').click(function(){
    var id =  $(this).attr('data-id');
   loadReportOption(id);
});
    
$('#reporttype').change( function(){
   var id = $(this).attr('data-id');
   loadReportOption(id);
 
});

$('.advance-report-delete').click( function(){
        if(confirm('".Yii::t('graphconfig', 'Delete confirm!')."')){
            var id = $('#forms-id').val();
            $.ajax({
                method: 'POST',
                url: '" . Url::to(['/graphconfig/graphconfig/remove-config']) . "',
                data: {id:id },
                dataType: 'HTML',
                success: function(result) {
                   $('#'+id).remove();
                   $('#modal-advance-config-edit').modal('hide');
                }
            });

        }
    });
    
function loadReportOption(id){
    $('#reporttype-option').html('<i class=\"fa fa-spinner fa-spin\" style=\"font-size:24px\"></i>');
    var parentid = id;
    var radiotype = $('#reporttype :radio:checked').val();
    var sql =  $('#sqlsetting').val();
    var val = $('#reportval').val();
    var val2 = $('#reportval2').val();
    setTimeout(function(){ 
        $.ajax({
            method: 'POST',
            url: '" . Url::to(['/graphconfig/graphconfig/get-report-option']) . "',
            data: {type:radiotype, sql: sql, parentid: parentid, val : val, val2 : val2},
            dataType: 'HTML',
            success: function(result) {
                $('#reporttype-option').html(result);
            }
        });
    }, 500);
      
}
$('#selectsite').change(function(){
    var id = $(this).attr('data-id');
    checkSelecthosp(id);
});
function checkSelecthosp(id){
    if($('#selectsite :radio:checked').val() == 2){
        $('#selectsitedef').show();
    }else{
        $('#selectsitedef').hide();
    }
}
"); ?>