<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\SystemError */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="system-error-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">System Error</h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'id')->textInput() ?>

	<?= $form->field($model, 'code')->textInput() ?>

	<?= $form->field($model, 'file')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'line')->textInput() ?>

	<?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'trace_string')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'created_by')->textInput() ?>

	<?= $form->field($model, 'created_at')->textInput() ?>

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
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
	    if(result.action == 'create') {
		//$(\$form).trigger('reset');
                $(document).find('#modal-system-error').modal('hide');
		$.pjax.reload({container:'#system-error-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-system-error').modal('hide');
		$.pjax.reload({container:'#system-error-grid-pjax'});
	    }
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