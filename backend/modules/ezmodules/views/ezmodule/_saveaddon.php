<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\inv\models\InvMenu */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-menu-form">
    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Module')?></h4>
    </div>
    
    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>
        <div class="modal-body">
        <?php
        echo $form->field($model, 'module_id')->widget(kartik\select2\Select2::className(), [
           'options' => ['placeholder' => Yii::t('ezmodule', 'Search Module')],
           'data' => ArrayHelper::map($modelModule,'ezm_id','ezm_name'),
           'pluginOptions' => [
           ],
        ]);
        ?>
        
	<?= Html::activeHiddenInput($model, 'addon_id') ?>
        <?= Html::activeHiddenInput($model, 'addon_default') ?>
        <?= Html::activeHiddenInput($model, 'ezm_id') ?>
        <?= Html::activeHiddenInput($model, 'user_id') ?>
	
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
	    getModuleMenuContent($('#ezmodule-module-menu').attr('data-url'));
            $(document).find('#modal-ezmodule-module-menu').modal('hide');
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});


function getModuleMenuContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-module-menu').html(result);
            }
        });
    }

");?>