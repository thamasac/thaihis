<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformChangeLog */
/* @var $form yii\bootstrap\ActiveForm */

$modelFields = \backend\modules\ezforms2\classes\EzfQuery::getFieldsList($model->ezf_id);
$modelVersion = \backend\modules\ezforms2\classes\EzfQuery::getEzformVersionList($model->ezf_id);
?>

<div class="ezform-change-log-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Ezform Change Log</h4>
    </div>

    <div class="modal-body">
	<?= $form->field($model, 'log_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_field_id')->dropDownList(yii\helpers\ArrayHelper::map($modelFields, 'id', 'name')) ?>

	<?= $form->field($model, 'ezf_version')->dropDownList(yii\helpers\ArrayHelper::map($modelVersion, 'ver_code', 'ver_code')) ?>

	<?= $form->field($model, 'log_type')->textInput() ?>

	<?= $form->field($model, 'log_event')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'log_count')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'log_ref_id')->hiddenInput()->label(FALSE) ?>

      <?php 
                    $settings = [
			    'minHeight' => 30,
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
                            'paragraphize'=>false,
                            'replaceDivs'=>false,
                    ];
                    $lang = Yii::$app->language;
                    if($lang!='en-US'){
                        $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
                    }
                    
                    echo $form->field($model, 'log_detail')->widget(vova07\imperavi\Widget::className(), [
		    'settings' => $settings
		]);?>

	<?= $form->field($model, 'log_ref_table')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'log_ref_version')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'log_ref_varname')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

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
                $(document).find('#modal-ezform-change-log').modal('hide');
		$.pjax.reload({container:'#ezform-change-log-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezform-change-log').modal('hide');
		$.pjax.reload({container:'#ezform-change-log-grid-pjax'});
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