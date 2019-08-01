<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\ArrayHelper;
use backend\modules\core\models\CoreFields;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptions */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-options-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>
	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="itemModalLabel">Core Options</h4>
	</div>
  
	<div class="modal-body">
	    <div class="row">
			<div class="col-md-3" >
				<?= $form->field($model, 'option_name')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-3 sdbox-col">
				<?= $form->field($model, 'input_label')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-6 sdbox-col">
				<?= $form->field($model, 'option_value')->textInput() ?>
			</div>
		
	    </div>
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'input_field')->dropDownList(ArrayHelper::map(CoreFields::find()->all(), 'field_code', 'field_code'), ['prompt'=>'None']) ?>
			</div>
			<div class="col-md-3 sdbox-col">
				<?= $form->field($model, 'autoload')->dropDownList(CoreFunc::itemAlias('autoload')) ?>
			</div>
			<div class="col-md-3 sdbox-col" style="padding-top: 21px;">
				<?= $form->field($model, 'input_required')->checkbox() ?>
			</div>
	    </div>
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'input_hint')->textarea(['rows' => 3]) ?>
				<?= $form->field($model, 'input_validate')->textarea(['rows' => 3]) ?>
			</div>
			<div class="col-md-6 sdbox-col">
				<?= $form->field($model, 'input_data')->textarea(['rows' => 3, 'placeholder'=>'String array or function']) ?>
				<?= $form->field($model, 'input_meta')->textarea(['rows' => 3]) ?>
			</div>
		
	    </div>
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'input_specific')->textarea(['rows' => 3]) ?>
			</div>
			<div class="col-md-2 sdbox-col">
				<?= $form->field($model, 'input_order')->textInput(['type'=>'number']) ?>
			</div>
	    </div>
		

	</div>
	<div class="modal-footer">
	    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
			$(\$form).trigger('reset');
			$.pjax.reload({container:'#core-options-grid-pjax'});
	    } else if(result.action == 'update'){
			$(document).find('#modal-core-options').modal('hide');
			$.pjax.reload({container:'#core-options-grid-pjax'});
			console.log(result);
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