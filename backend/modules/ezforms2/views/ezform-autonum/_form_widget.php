<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformAutonum */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-autonum-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-body">
        
	<div class="row">
                <div class="col-md-6 "><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-2 sdbox-col" ><?= $form->field($model, 'per_time')->textInput(['type'=>'number']) ?></div>
                <div class="col-md-2 sdbox-col" style="margin-top: 22px;"><?= $form->field($model, 'per_day')->checkbox() ?></div>
                <div class="col-md-2 sdbox-col" style="margin-top: 22px;"><?= $form->field($model, 'bysite')->checkbox() ?></div>
        </div>
        <div class="row">
                <div class="col-md-3 "><?= $form->field($model, 'prefix')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3 sdbox-col"><?= $form->field($model, 'digit')->textInput(['type'=>'number']) ?></div>
                <div class="col-md-3 sdbox-col"><?= $form->field($model, 'count')->textInput(['type'=>'number', 'style'=>'background-color: #d9edf7;']) ?></div>
                <div class="col-md-3 sdbox-col"><?= $form->field($model, 'suffix')->textInput(['maxlength' => true]) ?></div>
        </div>
        
        <?= Html::activeHiddenInput($model, 'id') ?>
        <?= Html::activeHiddenInput($model, 'ezf_id') ?>
        <?= Html::activeHiddenInput($model, 'ezf_field_id') ?>
        <?= Html::activeHiddenInput($model, 'status') ?>
	<?= Html::activeHiddenInput($model, 'updated_at') ?>
        <?= Html::activeHiddenInput($model, 'updated_by') ?>
        <?= Html::activeHiddenInput($model, 'created_at') ?>
        <?= Html::activeHiddenInput($model, 'created_by') ?>

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
            
            $('#'+$('#$reloadDiv').attr('data-field')).val(result.data.id);
            $('#$reloadDiv').load($('#$reloadDiv').attr('data-url')+result.data.id);
            
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

");?>