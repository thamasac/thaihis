<?php
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<div class="modal-header" style="border-radius:5px 5px 0px 0px ;background: #00A21E;color:#fff;">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><strong>แพทย์ประจำห้องตรวจ</strong></h4>
</div>
<div class="modal-body" >
    <i class="glyphicon glyphicon-flag fa-2x"></i><label style="font-size:20px;">ห้องตรวจ Ultrasound ที่ <?=($room_name)?></label>
<?php
//         $resDoctor2 = yii\helpers\ArrayHelper::map($resDoctor, 'doctorcode', 'doctorfullname');
        echo Select2::widget([
                'name' => 'doctor',
                'id' => 'doctor',
                'value'=>$doctor['doctorfullname'],
                'options' => ['placeholder' => 'เลือกแพทย์ประจำห้องตรวจ...','data-id'=>$doctor['doctorcode'],'disabled'=>'disabled'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 0, //ต้องพิมพ์อย่างน้อย 3 อักษร ajax จึงจะทำงาน
                    'ajax' => [
                        'url' => '/usfinding/monitoring/doctor-list',
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
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <!--<button type="button" id="confirm-doctor" class="btn btn-primary" data-dismiss="modal">Confirm</button>-->
</div>

<?php
$this->registerJs("
    $('#confirm-doctor').on('click', function(){
        console.log($('#doctor').val());
        var room_name = '$room_name';
        onDoctorChange($('#doctor').val(), room_name);
    });

");
?>