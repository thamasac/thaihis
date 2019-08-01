<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\patient\models\ConstOrder */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="const-order-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Const Order</h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'order_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'order_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'group_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'group_type')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'fin_item_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'sks_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'full_price')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'order_status')->textInput(['maxlength' => true]) ?>

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
                $(document).find('#modal-const-order').modal('hide');
		$.pjax.reload({container:'#const-order-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-const-order').modal('hide');
		$.pjax.reload({container:'#const-order-grid-pjax'});
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