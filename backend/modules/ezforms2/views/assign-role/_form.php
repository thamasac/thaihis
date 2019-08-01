<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\date\DatePicker;
?>
<div class="col-md-12">
    <div class="panel panel-success" id="<?= $data["id"] ?>">
        <div class="panel-heading">
            <span class="text-left panel-title">
                <?php
                if ($data['role_detail']) {
                    echo $data['role_detail'];
                } else {
                    echo "ยังไม่ตั้งชื่อ";
                }
                ?>
            </span>
            <span class="text-right panel-title pull-right" style="margin-top:-5px;">

                <?php if ($delete == '0'): ?>

                    <button type="buttton" class="btn btn-danger btnDeleteForm" data-id="<?= $data['id'] ?>"><i class="fa fa-minus"></i></button>

                <?php endif; ?>
            </span>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-12">
                    <div data-id='<?= $data["id"] ?>'>
                        <div class="form-group">
                            <?= Html::hiddenInput("id", $data["id"]) ?>
                        </div>
                        <div class="form-group">
                            <?= Html::hiddenInput("role_id", $data["role_id"]) ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label><?= Yii::t('ezform2', 'Role Name') ?>:</label>
                                        <?= Html::textInput("role_detail", $data['role_detail'], ["class" => "form-control textInput", "data-id" => $data["id"], "data-role_id" => $data["role_id"]]); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label><?= Yii::t('ezform2', 'Initial') ?>:</label>
                                        <?= Html::textInput("role_name", $data['role_name'], ["class" => "form-control textInput", "data-id" => $data["id"], "data-role_id" => $data["role_id"]]); ?>
                                    </div>
                                </div>
                            </div>

                        </div> 

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= Yii::t('ezform2', 'User') ?>:</label>
                                    <?php
                                       echo Select2::widget([
                                        'name' => 'user_id',
                                        'id' => "select2_".$data["role_id"],
                                        'initValueText' =>1,   
                                        'options' => ['data-id'=>$data['id'],'class'=>'select2_user','placeholder' => Yii::t('ezform2', 'User'), 'multiple' => true,],
                                        'pluginOptions' => [
                                            'tags' => FALSE,
                                            'allowClear' => true,
                                            'minimumInputLength' => 0,
                                            'language' => [
                                                'errorLoading' => new yii\web\JsExpression("function () { return 'Waiting for results...'; }"),
                                            ],
                                            'ajax' => [
                                                'url' => Url::to(['/ezforms2/assign-role/get-user']),
                                                'dataType' => 'json',
                                                'data' => new yii\web\JsExpression('function(params) { return {q:params.term}; }')
                                            ],
                                            'escapeMarkup' => new yii\web\JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new yii\web\JsExpression('function(user) { return user.text; }'),
                                            'templateSelection' => new yii\web\JsExpression('function (user) { return user.text; }'),
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <?php 
                                            echo '<label>'.Yii::t('ezform2', 'Start Date').'</label>';
                                            echo DatePicker::widget([
                                                    'name' => 'start_date', 
                                                    'value' => $data['start_date'],
                                                    'removeButton' => false,
                                                    'options' => ['placeholder' => 'Select Start Date', 'data-id'=>$data['id']],
                                                    'pluginOptions' => [
                                                            'format' => 'yyyy-mm-dd',
                                                            'todayHighlight' => true
                                                    ]  
                                                    
]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <?php 
                                            echo '<label>'.Yii::t('ezform2', 'Expiry Date').'</label>';
                                            echo DatePicker::widget([
                                                    'name' => 'expiry_date', 
                                                    'value' => $data['expiry_date'],
                                                    'removeButton' => false,
                                                    'options' => ['placeholder' => 'Select Start Date', 'data-id'=>$data['id']],
                                                    'pluginOptions' => [
                                                            'format' => 'yyyy-mm-dd',
                                                            'todayHighlight' => true
                                                    ]
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="form-group">
                                         
                                        <?php 
                                            echo '<label>'.Yii::t('ezform2', 'Role Start').'</label>';
                                            echo DatePicker::widget([
                                                    'name' => 'role_start', 
                                                    'value' => $data['role_start'],
                                                    'removeButton' => false,
                                                    'options' => ['placeholder' => 'Select Role Start', 'data-id'=>$data['id']],
                                                    'pluginOptions' => [
                                                            'format' => 'yyyy-mm-dd',
                                                            'todayHighlight' => true
                                                    ]
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="form-group">
                                 
                                            <?php  echo Html::checkboxList("expire_status['role_stop_".$data['id']."']", $data['expire_status'], ['1' => Yii::t('ezform2', 'Expire Status')], ['class'=>'checkbox_expire_status','data-id'=>'role_stop_'.$data["id"]]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 role_stop_<?= $data['id']?>" style="display:none;">
                                <div class="form-group">
                                    <div class="form-group">
                                       
                                        <?php 
                                            echo '<label>'.Yii::t('ezform2', 'Role Stop').'</label>';
                                            echo DatePicker::widget([
                                                    'name' => 'role_stop', 
                                                    'value' => $data['role_stop'],
                                                    'options' => ['placeholder' => 'Select Role Stop', 'data-id'=>$data['id']],
                                                    'removeButton' => false,
                                                    'options'=>[],
                                                    'pluginOptions' => [
                                                            'format' => 'yyyy-mm-dd',
                                                            'todayHighlight' => true
                                                    ]
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $(".btnDeleteForm").click(function () {
        let id = $(this).attr('data-id');
        $('#' + id).remove();
        let url = '<?= yii\helpers\Url::to(['/ezforms2/assign-role/delete-form']) ?>'
        $.get(url, {id: id}, function (data) {
            console.log(data);
            $("#" + id).remove();
        });
        return false;
    });

    $('.textInput').change(function () {
        let id = $(this).attr('data-id');
        let role_id = $(this).attr('data-role_id');
        let data_value = $(this).val();
        let name = $(this).attr('name');

        let data = {name: name, value: data_value};

        let url = '<?= Url::to(['/ezforms2/assign-role/save-role']) ?>';
        $.post(url, {
            id: id,
            role_id: role_id,
            data: data
        }, function (data) {
            console.log(data);
        });
    });
    
    function setDataSelect2(){
       let url = '<?= Url::to(['/ezforms2/assign-role/get-user-all']) ?>';
       $.get(url,function(data){
          $('.select2_user').html(data);
       }); 
    }setDataSelect2(); //ค่าเริ่มต้น selec2
    
    
    $(".checkbox_expire_status input[type='checkbox']").on('change',function(){
        let item = $(this).is(':checked');
        let id = $(this).attr('name');
       
        let dataid = id.split("[");
        dataid = dataid[1].split("]");
        dataid = dataid[0].split("'");
        if(item){
            console.log(dataid[1]);
            $("."+dataid[1]).show();
        }else{
            $("."+dataid[1]).hide();
        }
     });
     
     $('.select2_user').change(function(e){
         let data_value = $(this).val();
         let dataid = $(this).attr('data-id');
         let name = $(this).attr('name');
         name = name.split('[');
         
         let url = '<?= Url::to(['/ezforms2/assign-role/save-user']) ?>';
         let data = {name: name[0], value: data_value};
        $.post(url, {
            id: dataid,
            data: data
        }, function (data) {
            console.log(data);
        });
     });   
     $('.krajee-datepicker').change(function(){
         let data_value = $(this).val();
         let dataid = $(this).attr('data-id');
         let name = $(this).attr('name');
         let url = '<?= Url::to(['/ezforms2/assign-role/save-date']) ?>';
         let data = {name: name, value: data_value};
        $.post(url, {
            id: dataid,
            data: data
        }, function (data) {
        });
     });
       
</script>
<?php \richardfan\widget\JSRegister::end(); ?>