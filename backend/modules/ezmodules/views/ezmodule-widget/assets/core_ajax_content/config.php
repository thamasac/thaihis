<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

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
<div class="alert alert-info" role="alert"> 
    <strong>Variable & Query Params(GET) : </strong> {module} {reloadDiv} {target} {fields} {options}
</div>

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Ajax URL'), 'options[url]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[url]', (isset($options['url'])?$options['url']:''), ['class'=>'form-control'])?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Fields (JSON array = ["id"], object = {"title":"title"})'), 'options[fields]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[fields]', (isset($options['fields'])?$options['fields']:''), ['class'=>'form-control'])?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Options URL (JSON array = ["id"], object = {"title":"title"})'), 'options[options_url]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[options_url]', (isset($options['options_url'])?$options['options_url']:''), ['class'=>'form-control'])?>
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