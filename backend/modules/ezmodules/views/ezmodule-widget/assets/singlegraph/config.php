<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use appxq\sdii\utils\SDUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);

$selectsitedef = isset($options['selectsitedef'])? $options['selectsitedef'] : '|';
$sitesp = explode('|', $selectsitedef);
$scode = isset($sitesp[0]) ? $sitesp[0] : '';
$sname = isset($sitesp[1]) ? $sitesp[1] : '';
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

    <div class="modal-header" style="margin-bottom: 15px;">
        <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
    </div>
    <div class="col-md-12" style="padding : 15px 10px;" >
        <span> <?= Yii::t('graphconfig', 'Variable can use in SQL COMMAND') ?></span>
        <table class="table">
            <tr>
                <td>_USERSITECODE_</td>
                <td><?= Yii::t('graphconfig', 'User sitecode') ?></td>
                <td>_SITECODE_</td>
                <td><?= Yii::t('graphconfig', 'Select sitecode') ?></td>
            </tr>
            <tr>
                <td>_STARTDATE_</td>
                <td><?= Yii::t('graphconfig', 'Start date') ?></td>
                <td>_STOPDATE_</td>
                <td><?= Yii::t('graphconfig', 'Stop date') ?></td>
            </tr>
        </table>
    </div>
    <!--config start-->
    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
            <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:''), ['class'=>'form-control', 'placeholder'=>Yii::t('ezform', 'Title')])?>
        </div>
        <div class="col-md-6">
            <?= Html::label(Yii::t('graphconfig', 'Graph Config'), 'options[reporttype]', ['class' => 'control-label']) ?>
            <?= Html::radioList('options[reporttype]', (isset($options['reporttype'])?$options['reporttype']:0), [0 =>  Yii::t('graphconfig', 'Data Table'),
                1 => Yii::t('graphconfig', 'Pie graph'),
                2 => Yii::t('graphconfig', 'Line graph'),
                3 => Yii::t('graphconfig', 'Bar graph')], ['class' => 'form-control reporttype', 'id'=>'reporttype']) ?>
            <?= Html::hiddenInput('options[reporttypevariable]', (isset($options['reporttypevariable'])? $options['reporttypevariable']: '' ), ['class' => 'reportval', 'id'=>'reportval']) ?>
            <?= Html::hiddenInput('options[reporttypeval]', (isset($options['reporttypeval'])?$options['reporttypeval']:''), ['class' => 'reportval2', 'id'=>'reportval2']) ?>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <?= Html::label(Yii::t('graphconfig', 'SQL Builder'), 'ezf_sql', ['class' => 'control-label']) ; ?>
        </div>
        <div class="col-md-10">
            <?php
            $sql_init = [];
            if($options['ezf_sql']){
                $sql_init = \backend\modules\ezforms2\classes\EzfQuery::getEzSqlIn(isset($options['ezf_sql'])?$options['ezf_sql']:null);
            }
            echo appxq\sdii\widgets\SDSqlBuilder::widget([
                'name' => 'options[ezf_sql]',
                'id' => 'ezf_sql',
                'value'=>isset($options['ezf_sql'])?$options['ezf_sql']:null,
                'initValueText'=>isset($sql_init[0]['sql_name'])?$sql_init[0]['sql_name']:null,
            ]); ?>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success builder-load pull-right" type="button" id="builder-load">Load Builder</button><br />
        </div>
        <?php
        /*
                $model->ezf_sql = SDUtility::string2Array($model->ezf_sql); // initial value
                $dataSql = [];
                if(!empty($model->ezf_sql)){
                    $sql_init = \backend\modules\ezforms2\classes\EzfQuery::getEzSqlIn(implode(',', $model->ezf_sql));
                    if($sql_init){
                        $dataSql = ArrayHelper::map($sql_init, 'id', 'sql_name');
                    }
                }

                $form->field($model, 'ezf_sql')->widget('appxq\sdii\widgets\SDSqlBuilder', [
                'id' => 'ezf_sql',
                'data' => $dataSql,
                'options' => ['multiple'=>1],
                ]);
        */
        ?>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <?= Html::label(Yii::t('graphconfig', 'SQL setting'), 'options[sqlsetting]', ['class' => 'control-label']) ?>
            <?= Html::textarea('options[sqlsetting]', (isset($options['sqlsetting'])?$options['sqlsetting']:''), ['class' => 'form-control sqlsetting', 'rows' => 10, 'id'=>'sqlsetting']) ?>
        </div>

    </div>

    <div class="form-group row">
        <div class="col-md-10" id="reporttype-option"></div>
        <div class="col-md-2">
            <button class="btn btn-success sql-exec pull-right" type="button" id="sql-exec">SQL test</button><br />
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('graphconfig', 'Option'), 'options[selectdate]', ['class' => 'control-label']) ?> <br />
            <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[selectdate]', (isset($options['selectdate']) ? $options['selectdate'] : 0 ), ['label'=>Yii::t('graphconfig', 'Can select date')]) ?>
            <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[showselectsite]', (isset($options['showselectsite']) ? $options['showselectsite'] : 0 ), ['label'=>Yii::t('graphconfig', 'Show site')]) ?>
            <?= Html::radioList('options[selectsite]', (isset($options['selectsite'])? $options['selectsite'] : 0), [0 => Yii::t('graphconfig', 'Select own site'),
                1 => Yii::t('graphconfig', 'Select all'),
                2 => Yii::t('graphconfig', 'Select site')], ['class' => 'form-control selectsite', 'id'=>'selectsite'])
            ?>
            <div class="col-md-12 selectsitedef">
                <br>
                <?php
                $sitevalue = $scode != ''? $scode . '|' . $sname : '';

                echo Select2::widget([
                    'name' => 'options[selectsitedef]',
                    'value' =>$sitevalue,
                    'options' => ['placeholder' => Yii::t('graphconfig', 'Search site ...'), 'readonly' => '', 'class' => 'selectsitedef', 'id' => 'selectsitedef'],
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
        </div>
        <div class="col-md-6">
            <?= Html::label(Yii::t('graphconfig', 'Text box'), 'options[textbox]', ['class' => 'control-label']) ?>
            <?= Html::textarea('options[textbox]', (isset($options['textbox'])?$options['textbox']:''), ['class'=>'form-control', 'placeholder'=>Yii::t('graphconfig', 'Text box')])?>
        </div>
    </div>

    <div class="form-group row">

        <div class="col-md-12">
            <?= Html::label(Yii::t('graphconfig', 'Graph name'), 'options[graphname]', ['class' => 'control-label']) ?>
        </div>
        <div class="col-md-6">
            <?= Html::textInput('options[graphname]', (isset($options['graphname'])?$options['graphname']:''), ['class'=>'form-control', 'placeholder'=>Yii::t('graphconfig', 'Graph name')])?>
        </div>
        <div class="col-md-2">
            <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[day]',(isset($options['day']) ? $options['day'] : 0) ,['label'=>Yii::t('graphconfig', 'Add Day To Title')]) ?>
        </div>
        <div class="col-md-2">
            <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[month]',(isset($options['month']) ? $options['month']  :0 ),['label'=>Yii::t('graphconfig', 'Add Month To Title')]) ?>
        </div>
        <div class="col-md-2">
            <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[year]',(isset($options['year']) ? $options['year'] : 0 ),['label'=>Yii::t('graphconfig', 'Add Year To Title')]) ?>
        </div>

    </div>
    <div class="form-group row">
        <div class="col-md-6">
            <div class="col-md-12"><label><?= Yii::t('graphconfig', 'Width') ?></label></div>
            <div class="col-md-12"><?= Html::dropDownList('options[width]', (isset($options['width'])?$options['width']:''), $datawidth, ['class' => 'form-control form-input']) ?>
            </div>
        </div>
    </div>

    <!--config end-->

<?php
$this->registerJS("
    loadReportOption();
    checkSelecthosp();
    $('#selecthospdef-box').hide();
//

$('#sql-exec').click(function(){
    var id =  $(this).attr('data-id');
   loadReportOption(id);
});
    
$('#builder-load').click(function(){
    var id = $('#ezf_sql').val();
    if(confirm('Load query from builder will replace on SQL setting. Confirm?')){
        $.ajax({
            method: 'POST',
            url: '" . Url::to(['/graphconfig/graphconfig/builder-load']) . "',
            data: {id:id},
            dataType: 'HTML',
            success: function(result) {
                var data = JSON.parse(result) ;
                $('#sqlsetting').html(data.sql);
            }
        });
    }
});
$('#reporttype').change( function(){
   var id = $(this).attr('data-id');
   loadReportOption(id);
 
});
    
function loadReportOption(id){
    $('#reporttype-option').html('<i class=\"fa fa-spinner fa-spin\" style=\"font-size:24px\"></i>');
    
    var radiotype = $('#reporttype :radio:checked').val();
    var sql = $('#sqlsetting').val();
    var val = $('#reportval').val();
    var val2 = $('#reportval2').val();
    setTimeout(function(){ 
        /* $.ajax({
            method: 'POST',
            url: '" . Url::to(['/graphconfig/graphconfig/get-report-option']) . "',
            data: {type:radiotype, sql: sql, val : val, val2 : val2},
            dataType: 'HTML',
            success: function(result) {
                $('#reporttype-option').html(result);
            }
        }); */
        $.post('" . Url::to(['/graphconfig/graphconfig/singlegraph-report-option']) . "',{type:radiotype, sql: sql, val : val, val2 : val2}
          ).done(function(result){
             $('#reporttype-option').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
          
    }, 500);
}
$('#selectsite').change(function(){
    if($('#selectsite :radio:checked').val() == 2){
        $('#selectsitedef').show();
    }else{
        $('#selectsitedef').hide();
    }
});
function checkSelecthosp(){
    if($('#selectsite :radio:checked').val() == 2){
        $('#selectsitedef').show();
    }else{
        $('#selectsitedef').hide();
    }
}
");
?>