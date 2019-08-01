<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\QueueLog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="queue-log-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Queue Log</h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'id')->textInput() ?>

	<?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'ezf_id')->textInput() ?>

	<?= $form->field($model, 'dataid')->textInput() ?>

	<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'enable')->textInput() ?>

	<?= $form->field($model, 'setting_id')->textInput() ?>

	<?= $form->field($model, 'module_id')->textInput() ?>

	<?= $form->field($model, 'current_unit')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'user_receive')->textInput() ?>

	<?= $form->field($model, 'time_receive')->textInput() ?>

	<?= $form->field($model, 'options')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'updated_by')->textInput() ?>

	<?= $form->field($model, 'updated_at')->textInput() ?>

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
                $(document).find('#modal-queue-log').modal('hide');
		$.pjax.reload({container:'#queue-log-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-queue-log').modal('hide');
		$.pjax.reload({container:'#queue-log-grid-pjax'});
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