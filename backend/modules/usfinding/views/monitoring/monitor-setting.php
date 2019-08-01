<?php

use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Url;
use \frontend\modules\api\v1\classes\MainQuery;
use yii\helpers\Html;

$session = \Yii::$app->session;
$table_us = $session['table_us'];
$refresh_time = $session['refresh_time'];
$auto_reload = $session['auto_reload'];
$filter_count = $session['filter_count'];

if ($table_us == '')
    $table_us = 'tb_data_3';
if ($refresh_time == '')
    $refresh_time = '5';
if($filter_count == '' || $filter_count == null)
    $filter_count = '1';

if ($auto_reload == null) {
    $auto_reload = 'false';
}
//var_dump($auto_reload);
$checked = $auto_reload == 'false' ? '' : 'checked';
?>
<div class="modal-header" style="background: green;color:#fff;">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><strong>Monitor Setting</strong></h4>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">เลขที่ Worklist :</label>
            <?php
            //appxq\sdii\utils\VarDumper::dump($resWorkDefault);
            echo Select2::widget([
                'name' => 'worklistnumber',
                'id' => 'worklistnumber',
                'options' => ['placeholder' => 'เลือกข้อมูล...' ],
                'data'=>[$resWorkDefault['id']=>$resWorkDefault['id'] . ' : ' . $resWorkDefault["sitecode"]],
                'value'=>$resWorkDefault['id'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 0, //ต้องพิมพ์อย่างน้อย 3 อักษร ajax จึงจะทำงาน
                    'ajax' => [
                        'url' => '/usfinding/worklist/get-worklist-number',
                        'dataType' => 'json', //รูปแบบการอ่านคือ json
                        'data' => new JsExpression('function(params) { 
                            return {q:params.term}; 
                            }
                         '),
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ]
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <label>จำแนกข้อมูลโดย :  </label>
            <select id="filter-count" name="filter-count" class="form-control">
                <option value="1" <?=$filter_count=='1'?'selected':''?> >แพทย์ผู้ตรวจ</option>
                <option value="2" <?=$filter_count=='2'?'selected':''?> >เจ้าหน้าที่ลงข้อมูล</option>
                <option value="3" <?=$filter_count=='3'?'selected':''?> >ห้องตรวจ</option>
            </select>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <label>Time Reload (วินาที/ครั้ง) : </label>
            <input class="form-control" type="number" name="refresh_time" id="refresh_time" value="<?= $refresh_time ?>">
        </div>
        <div class="col-md-6">
            <label>Table Data :  </label>
            <input class="form-control" type="text" name="table_us" id="table_us" value="<?= $table_us ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <input  type="checkbox" name="auto_reload" id="auto_reload" <?= $checked ?>>
            <label>Auto Reload  </label>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-success" id="btn-save-setting">บันทึก</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

<?php
$this->registerJs("
   $('#btn-save-setting').on('click', function(){
       var table_us = $('#table_us').val();
       var refresh_time = $('#refresh_time').val();
       var worklistnumber= $('#worklistnumber').val();
       var filter_count= $('#filter-count').val();
       var auto_reload= $('#auto_reload').is(':checked');
       if(worklistnumber == ''){
            worklistnumber= $('#worklistnumber').attr('data-id');
        }
       
       $.ajax({
            url:'" . Url::to('/usfinding/monitoring/set-setting') . "',
            method:'POST',
            data:{
                table_us : table_us,
                refresh_time:refresh_time,
                worklistnumber:worklistnumber,
                auto_reload:auto_reload,
                filter_count:filter_count
            },
            type:'HTML',
            success:function(result){
                showMonitoringAfterSetting(worklistnumber);
               $('#modal-setting').modal('hide');
            },
            error:function(){
            
            }
        });
   });
   
   function showMonitoringAfterSetting(worklistnumber){
        var monDiv = $('#reportUSFinding');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var hospital = $('#inputHospital').val();
        
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate,
                worklistnumber:worklistnumber,
                hospital:hospital
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
");
?>
