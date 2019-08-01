<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreFields */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-fields-form">

    <?php $form = ActiveForm::begin([
		'id'=>$model->formName(),
    ]); ?>
	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="itemModalLabel">Core Fields</h4>
	</div>

	<div class="modal-body">
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'field_code')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-6 sdbox-col" style="padding-top: 21px;">
				<?= $form->field($model, 'field_internal')->checkbox() ?>
			</div>
		
	    </div>
	    <div class="row">
			<div class="col-md-6">
				<?= $form->field($model, 'field_class')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-6 sdbox-col">
				<?= $form->field($model, 'field_name')->textInput(['maxlength' => true]) ?>
			</div>
		
	    </div>

		<?= $form->field($model, 'field_meta')->textarea(['rows' => 3]) ?>
	    
		<?= $form->field($model, 'field_description')->textarea(['rows' => 3]) ?>
	    
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
				$.pjax.reload({container:'#core-fields-grid-pjax'});
			} else if(result.action == 'update'){
				$(document).find('#modal-core-fields').modal('hide');
				$.pjax.reload({container:'#core-fields-grid-pjax'});
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