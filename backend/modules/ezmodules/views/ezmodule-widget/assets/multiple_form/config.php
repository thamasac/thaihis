<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\models\EzformTree;
use kartik\tree\TreeViewInput;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);


?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'Form Category'), 'options[target]', ['class' => 'control-label']) ?>
      <?=  TreeViewInput::widget([
          'name' => 'options[category_id]',
          'value'=>isset($options['category_id'])?$options['category_id']:0,
            'id' => 'category_id',
            'query' => EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft'),
            'headingOptions' => ['label' => 'Categories'],
            'asDropdown' => true,
            'multiple' => false,
            'fontAwesome' => true,
            'rootOptions' => [
                'label' => '<i class="fa fa-home"></i> ',
                'class' => 'text-success',
                'options' => ['disabled' => false]
            ],
      ])?>
       
    </div>
</div>

<div class="form-group row">
  <div class="col-md-6 " >
     <?= Html::label(Yii::t('ezform', 'Theme'), 'options[theme]', ['class' => 'control-label']) ?>
        <?= kartik\select2\Select2::widget([
            'id'=>'config_theme',
            'name' => 'options[theme]',
            'value'=>isset($options['theme'])?$options['theme']:'default',
            'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('theme'),
            'options' => ['placeholder' => Yii::t('ezform', 'Select Theme ...')],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ]);?>
  </div>
  
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Parent Name'), 'options[target]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[target]', (isset($options['target'])?$options['target']:'target'), ['class'=>'form-control'])?>
    </div>
</div>


<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>