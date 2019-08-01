<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreItemAlias */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-item-alias-form">

    <?php $form = ActiveForm::begin(['id'=>$model->formName()]); ?>
	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="itemModalLabel">Core Item Alias</h4>
	</div>

	<div class="modal-body">
		<div class="row">
		    <div class="col-md-6">
				<?= $form->field($model, 'item_code')->textInput(['maxlength' => true]) ?>
		    </div>
		    <div class="col-md-6 sdbox-col">
				<?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>
		    </div>
		</div>
	    
		<?= $form->field($model, 'item_data')->textarea(['rows' => 6]) ?>

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
				$.pjax.reload({container:'#core-item-alias-grid-pjax'});
			} else if(result.action == 'update'){
				$(document).find('#modal-core-item-alias').modal('hide');
				$.pjax.reload({container:'#core-item-alias-grid-pjax'});
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