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

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?php echo CoreFunc::t('Tag');?></h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint(CoreFunc::t('The name is how it appears on your site.')) ?>

	<?= $form->field($model, 'slug')->textInput(['maxlength' => true])->hint(CoreFunc::t('The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.')) ?>

	<?php
	
	if (!in_array($model->taxonomy, Yii::$app->controller->module->noParentTag)) {
	    $taxonomy = CoreFunc::getTaxonomyDropDownList(0, $model->taxonomy);
	    echo $form->field($model, 'parent')->dropDownList($taxonomy, ['encode' => false, 'prompt' => 'none'])->hint(CoreFunc::t('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'));
	} else {
	    echo Html::activeHiddenInput($model, 'parent');
	}
	?>
	
	<?php echo $form->field($model, 'description')->textarea(['rows' => 4, 'cols' => 50])->hint(CoreFunc::t('The description is not prominent by default; however, some themes may show it.')); ?>

	<?= Html::activeHiddenInput($model, 'term_group')?>
	<?= Html::activeHiddenInput($model, 'taxonomy')?>
	<?= Html::activeHiddenInput($model, 'count')?>
	<?= Html::activeHiddenInput($model, 'term_taxonomy_id')?>
	<?= Html::activeHiddenInput($model, 'term_id')?>
	
    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->term_id>0 ?Yii::t('app', 'Update'):Yii::t('app', 'Create'), ['class' => $model->term_id>0 ?'btn btn-primary':'btn btn-success']) ?>
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
		$(\$form).trigger('reset');
		getTerms();
		$.pjax.reload({container:'#tags-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-tags').modal('hide');
		$.pjax.reload({container:'#tags-grid-pjax'});
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

function getTerms(){
	var getTerms = " . (!in_array($model->taxonomy, Yii::$app->controller->module->noParentTag) ? 'true' : 'false') . ";
	var tagsParent = $('#tagsform-parent').val();
	
	if(getTerms){
	    $.getJSON('" . yii\helpers\Url::to(['//core/tags/terms', 'taxonomy' => $model->taxonomy]) . "', function(result) {
		if(result.status == 'success') {
		    $('#tagsform-parent').html(result.content);
		    $('#tagsform-parent').val(tagsParent);
		} else {
		    " . SDNoty::show('result.message', 'error') . "
		    return ;
		}
	    });
	}
}
	
");?>