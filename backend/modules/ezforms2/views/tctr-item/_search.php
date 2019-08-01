<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="ezf-emr-search" style="padding: 5px;">
<?php
$form = ActiveForm::begin([
            'id' => 'search-emr-'.$model->formName(),
            'action' => Yii::$app->request->url,
            'method' => 'get',
            'layout' => 'inline',
]);
?>
    <?= Html::label(Yii::t('ezform', 'Search By'))?>
    <?= $form->field($model, 'ezf_id')->dropDownList(yii\helpers\ArrayHelper ::map($ezformAll, 'ezf_id', 'ezf_name'), ['class'=>'form-control', 'prompt'=>Yii::t('ezform', 'All').' '.Yii::t('ezform', 'Form')])?>
    <?= $form->field($model, 'xsourcex')->textInput(['class'=>'form-control', 'placeholder'=>Yii::t('ezform', 'Site Code')])?>
    <?= $form->field($model, 'rstat')->dropDownList(backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class'=>'form-control', 'prompt'=>Yii::t('ezform', 'All').' '. Yii::t('ezform', 'Status')])?>
    
<?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('form#search-emr-{$model->formName()}').on('change', function(e) {
    $(this).submit();   
});


");
?>
