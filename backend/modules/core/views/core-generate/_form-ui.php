<?php

use yii\helpers\Html;
use backend\modules\core\models\CoreFields;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerate */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="generate-fields-form">

    <?php $form = ActiveForm::begin(['id'=>$model->formName()]); ?>
	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="itemModalLabel">Generate Field</h4>
	</div>

	<div class="modal-body">
	    <div class="row">
			<div class="col-md-4">
				<?= $form->field($model, 'input_field')->dropDownList(ArrayHelper::map(CoreFields::find()->all(), 'field_code', 'field_code'), ['placeholder'=>'Field']) ?>
			</div>
			<div class="col-md-4 sdbox-col">
				<?= $form->field($model, 'option_name')->textInput(['placeholder'=>'Name']) ?>
			</div>
			<div class="col-md-4 sdbox-col">
				<?= $form->field($model, 'option_value')->textInput(['placeholder'=>'Value']) ?>
			</div>
	    </div>

	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'input_data')->hint('backend\modules\core\classes\CoreFunc::itemAlias(\'CODE\')')->textarea(['rows' => 3, 'placeholder'=>'Data']) ?>
				<?= $form->field($model, 'input_hint')->textarea(['rows' => 3, 'placeholder'=>'Hint']) ?>
			</div>
			<div class="col-md-6 sdbox-col">
				<?= $form->field($model, 'input_meta')->hint('HTML options array.')->textarea(['rows' => 3, 'placeholder'=>'Meta']) ?>
				<?= $form->field($model, 'input_validate')->textarea(['rows' => 3, 'placeholder'=>'Validate']) ?>
			</div>
	    </div>
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'input_specific')->textarea(['rows' => 3, 'placeholder'=>'Specific']) ?>
			</div>
			<div class="col-md-2 sdbox-col">
				<?= $form->field($model, 'input_order')->textInput(['type'=>'number', 'step'=>0.1, 'class'=>'form-control']) ?>
			</div>
			<div class="col-md-3 sdbox-col" style="padding-top: 21px;">
				<?= $form->field($model, 'input_required')->checkbox() ?>
			</div>
		
	    </div>
	    <?= Html::activeHiddenInput($model, 'input_label');?>
	</div>
	<div class="modal-footer">
	    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
	    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("

$('form#{$model->formName()}').on('beforeSubmit', function(e){
    var \$form = $(this);
    $.post(
		\$form.attr('action'), //serialize Yii2 form
		\$form.serialize()
    ).done(function(result){
		if(result.status == 'success'){
			". SDNoty::show('result.message', 'result.status') ."
			if(result.action == 'create'){
				$(document).find('#modal-generate-fields').modal('hide');
				$.pjax.reload({container:'#generate-fields-grid-pjax'});
			} else if(result.action == 'update'){
				$(document).find('#modal-generate-fields').modal('hide');
				$.pjax.reload({container:'#generate-fields-grid-pjax'});
			}
		} else{
			". SDNoty::show('result.message', 'result.status') ."
		} 
    }).fail(function(){
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
    });
    return false;
});

");?>