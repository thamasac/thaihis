<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$user_id = \Yii::$app->user->id;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
$itemsWidget = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId($user_id);

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<div class="form-group row">

    <div class="col-md-6 ">
        <?php
        $attrname_widget_id = 'options[widget_id]';
        $value_widget_id = isset($options['widget_id']) ? $options['widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Schedule Widget'), $attrname_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'config_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_ezf_id = 'options[budget_ezf_id]';
        $value_ezf_id = isset($options['budget_ezf_id']) ? $options['budget_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Budget Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_budget_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_procedure_ezf_id = 'options[procedure_ezf_id]';
        $value_procedure_ezf_id = isset($options['procedure_ezf_id']) ? $options['procedure_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Procedure Form'), $attrname_procedure_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_procedure_ezf_id,
            'value' => $value_procedure_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_procedure_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>

<br><br>

<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    
    $(function(){
//        var options = <?php //json_encode($options);?>;
//        var data  = {options:JSON.stringify(options)};
//        $.get('/ezforms2/subject-management/add-input-procedure',data,function(result){
//             $('.show-procedure-name').append(result);
//        });
    })
    $('.btn-name-add').click(function(){
        $.get('/ezforms2/subject-management/add-input-procedure',{},function(result){
             $('.show-procedure-name').append(result);
        });
    })
       
</script>
<?php \richardfan\widget\JSRegister::end(); ?>