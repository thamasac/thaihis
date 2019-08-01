<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if($target){
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
$sub_ezf_id = isset($sub_ezf_id) ? $sub_ezf_id : '';

?>
<div class="form-group row">
    <div class="col-md-5" >
        <?php
        $attrname_ezf_id = 'options[sub_ezf_id][]';
        
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $sub_ezf_id,
            'options' => [
                'placeholder' => Yii::t('ezform', 'Form'), 
                'id' => 'config_ezf_id_'.$dataid,
                ],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-1" >
        <?= Html::Button('<i class="fa fa-trash-o" aria-hidden="true"></i>', ['class' => 'btn btn-danger remove-btn'  ]) ?>
    </div>
</div>
<?php
$this->registerJS("
    $( '.remove-btn' ).click(function() {
        $(this).parent().parent().remove();
    });
");
?>