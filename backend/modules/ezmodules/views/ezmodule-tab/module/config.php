<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * จำเป็นต้องมี options[render] และถ้ามีการส่งค่า options[params]
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];
?>
<?=\yii\helpers\Html::hiddenInput('options[render]', '/ezmodule/module_widget');?>


<div class="form-group row">
    <div class="col-md-6">
        <?= \yii\helpers\Html::label(Yii::t('ezmodule', 'Module'), 'options[params][id]', ['class' => 'control-label']) ?>
        <?php 
        $value = isset($options['params']['id'])?$options['params']['id']:'';
        $items = \backend\modules\ezmodules\classes\ModuleQuery::getModuleMyAllAddon(Yii::$app->user->id);
        
        echo kartik\select2\Select2::widget([
            'name' => 'options[params][id]',
            'value'=> $value,
            'options' => ['placeholder' => Yii::t('ezform', 'Select Module'), 'id'=>'config_tab_id'],
            'data' => \yii\helpers\ArrayHelper::map($items,'ezm_id','ezm_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>