<?php
use kartik\widgets\Select2;
use yii\web\JsExpression;
//\appxq\sdii\utils\VarDumper::dump($user_us);
$user_image = $user_us['avatar_base_url'] . '/' . $user_us['avatar_path']
?>
<div class="modal-header" style="border-radius:5px 5px 0px 0px ;background: #00A21E;color:#fff;">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><strong>เจ้าหน้าที่ลงข้อมูลประจำห้องตรวจ</strong></h4>
</div>
<div class="modal-body" >
    <i class="glyphicon glyphicon-flag fa-2x"></i><label style="font-size:20px;">ห้องตรวจ Ultrasound ที่ <?=($room_name)?></label>
    <br/>
    <img src="<?= $user_image == '' || $user_image == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $user_image; ?>" width="350" height="350" class="img-thumbnail" alt="Cinque" >
<?php
//         $resDoctor2 = yii\helpers\ArrayHelper::map($resDoctor, 'doctorcode', 'doctorfullname');
        if($user_us['firstname'] == '') $user_us['firstname']='US Mobile';
        echo Select2::widget([
                'name' => 'user-us',
                'id' => 'user-us',
                'value'=>$user_us['firstname'].' '.$user_us['lastname'],
                'options' => ['placeholder' => 'เลือกแพทย์ประจำห้องตรวจ...','data-id'=>$user_us['user_id'], 'disabled'=>'disabled'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 0, //ต้องพิมพ์อย่างน้อย 3 อักษร ajax จึงจะทำงาน
                    'ajax' => [
                        'url' => '/usfinding/monitoring/user-list',
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
    <!--<button type="button" id="confirm-user" class="btn btn-primary" data-dismiss="modal">Confirm</button>-->
</div>

<?php
$this->registerJs("
    $('#confirm-user').on('click', function(){
        console.log($('#user-us').val());
        var room_name = '$room_name';
        var room_type = '$room_type';
        onUserChange($('#user-us').val(), room_name,room_type);
    });

");
?>