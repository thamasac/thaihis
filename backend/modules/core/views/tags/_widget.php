<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\core\classes\CoreFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreTerms */
/* @var $form yii\bootstrap\ActiveForm */

?>

<div class="tags-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="row">
	<div class="col-md-10" style="padding-right: 20px;">
	    <?= $form->field($model, 'name')
		    ->textInput(['maxlength' => true])
		    ->hint(CoreFunc::t('Separate tags with commas.')) 
		    ->label(false)
	    ?>
	</div>
	<div class="col-md-2 text-left" style="padding-left: 0; margin-left: -15px;">
	    <?= Html::submitButton('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success'])?>
	</div>
    </div>
    
    <?= Html::activeHiddenInput($model, 'slug')?>
    <?= Html::activeHiddenInput($model, 'parent')?>
    <?= Html::activeHiddenInput($model, 'description')?>
    <?= Html::activeHiddenInput($model, 'term_group')?>
    <?= Html::activeHiddenInput($model, 'taxonomy')?>
    <?= Html::activeHiddenInput($model, 'count')?>
    <?= Html::activeHiddenInput($model, 'term_taxonomy_id')?>
    <?= Html::activeHiddenInput($model, 'term_id')?>

    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default', 'style' => 'display: none; ']) ?>

    <?php ActiveForm::end(); ?>
    
</div>

<?php  $this->registerJs("
function resetTagsFormVal(){
    $('#{$model->formName()}').each (function(){
	    this.reset();
    });
}

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
	    resetTagsFormVal();
				
	    if(typeof updateTags == 'function'){
		updateTags(result.data); 
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