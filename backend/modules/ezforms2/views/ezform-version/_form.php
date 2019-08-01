<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformVersion */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-version-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Ezform Version</h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'ver_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'ver_for')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ver_approved')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ver_active')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'approved_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'approved_date')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ver_options')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'field_detail')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_sql')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_js')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_error')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_options')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

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
                $(document).find('#modal-ezform-version').modal('hide');
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezform-version').modal('hide');
	    }
            
            window.location.href = '".\yii\helpers\Url::to(['/ezbuilder/ezform-builder/update', 'id'=>$model->ezf_id, 'v'=>''])."'+$('#ezformversion-ver_code').val();
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