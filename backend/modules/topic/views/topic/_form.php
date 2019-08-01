<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use vova07\imperavi\Widget;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Topic */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="topic-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"></h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?=
        $form->field($model, 'detail')->widget(Widget::className(), [
            'settings' => [
                'minHeight' => 50,
                'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
                'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
                'plugins' => [
                    'fontcolor',
                    'fontfamily',
                    'fontsize',
                    'textdirection',
                    'textexpander',
                    'counter',
                    'table',
                    'definedlinks',
                    'video',
                    'imagemanager',
                    'filemanager',
                    'limiter',
                    'fullscreen',
                ],
                'paragraphize' => false,
                'replaceDivs' => false,
            ],
        ]);
        ?>
	<?= $form->field($model, 'module_id')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'widget_id')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'create_by')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'create_at')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'update_by')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'update_at')->hiddenInput()->label(false) ?>

	<?= $form->field($model, 'rstat')->hiddenInput()->label(false) ?>

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
		 
                $(document).find('#modal-topic').modal('hide');
                $('#modal-addbtn-topic').hide();
		showTopicAllForm();
	    } else if(result.action == 'update') {
		$(document).find('#modal-topic').modal('hide');
		showTopicAllForm();
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

function showTopicAllForm(){
    let url = '".Url::to(['/topic/topic/get-topic-all','options'=>$options])."'
    $.get(url, function(data){
         $('#showTopic').html(data); 
        $('#single-main-".$options['widget_id']."').html(data);
    });
}    

");?>