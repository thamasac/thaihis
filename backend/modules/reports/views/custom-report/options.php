<?php 
    use yii\helpers\Html;
?>
<?php yii\bootstrap\ActiveForm::begin(['id'=>'frm-custom-report']);?>
<div class="form-group text-right">
     <button class="btn btn-success"><i class="fa fa-plus"></i> Add new template</button> 
</div>
<div class="form-group">
    <?= Html::label("Template name: ")?>
    <?php 
        echo Html::hiddenInput("id", $dataTemplate['id'], ['id'=>'data_id']);
        $data = (new \yii\db\Query())->select('*')->from('zdata_1537848949032767100')->where('rstat not in(0,3)')->all();
        $items = yii\helpers\ArrayHelper::map($data, "template_id", "template_name");
        echo Html::dropDownList('template_name', $dataTemplate['template_name'], $items, ['class'=>'form-control', 'id'=>'template_name']);
    ?> 
     
</div>
<div class="form-group">
    <?= Html::label("Default: ")?>
    <?= Html::radioList("default", $dataTemplate['default'], ['10'=>'Yes', '20'=>'No'], ['id'=>'default'])?>
</div>

<?php yii\bootstrap\ActiveForm::end();?>



<?php richardfan\widget\JSRegister::begin();?>
<script>
     $('#template_name').on('change', function(){
         
     });
</script>
<?php richardfan\widget\JSRegister::end();?>


