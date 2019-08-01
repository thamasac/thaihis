<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * จำเป็นต้องมี options[render] และถ้ามีการส่งค่า options[params]
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>
<?=\yii\helpers\Html::hiddenInput('options[render]', '/ezmodule-tab/communication/widget');?>

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title to be displayed before the Widget'), 'options[params][title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[params][title]', (isset($options['params']['title'])?$options['params']['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  
</div>
<div class="form-group row">
    <div class="col-md-6 " >
        <?= Html::label(Yii::t('ezform', 'Theme'), 'options[params][theme]', ['class' => 'control-label']) ?>
        <?= kartik\select2\Select2::widget([
        'id'=>'config_tab_theme',
        'name' => 'options[params][theme]',
        'value'=>isset($options['params']['theme'])?$options['params']['theme']:'default',
        'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('theme'),
        'options' => ['placeholder' => Yii::t('ezform', 'Select Theme ...')],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);?>
    </div>
</div>


