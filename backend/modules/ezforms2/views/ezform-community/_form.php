<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\web\JsExpression;
use appxq\sdii\utils\SDUtility;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunity */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-community-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName().'-'.$model->object_id.'-'.$model->query_tool,
    ]); ?>
  
        <?php
            echo $form->field($model, 'send_to')->widget(Select2::className(), [
                'options' => ['placeholder' => Yii::t('ezform', 'Send To'), 'multiple' => true, 'class' => 'form-control', 'id'=>"send_to-{$model->object_id}-{$model->query_tool}"],
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform/get-user']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ]);
            ?>
  
        <?php 
                        $settings = [
                                'minHeight' => 30,
                                'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                                'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
                                'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                                'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
                                'plugins' => [
                                    'fontcolor',
                                    //'fontfamily',
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
                                    //'fullscreen',
                                ],
                                'paragraphize'=>false,
                                'replaceDivs'=>false,
                        ];
                        $lang = Yii::$app->language;
                        if($lang!='en-US'){
                            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
                        }

                        echo $form->field($model, 'content')->widget(vova07\imperavi\Widget::className(), [
                        'settings' => $settings,
                            'options' => ['id'=>"content-{$model->object_id}-{$model->query_tool}"]
                    ]);?>
      
	<?= $form->field($model, 'id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'parent_id')->hiddenInput()->label(FALSE) ?>

	

	<?= $form->field($model, 'type')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'object_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'dataid')->hiddenInput()->label(FALSE) ?>


	<?= $form->field($model, 'query_tool')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'field')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'value_old')->hiddenInput(['id'=>"value_old-{$model->object_id}-{$model->query_tool}"])->label(FALSE) ?>

	<?= $form->field($model, 'value_new')->hiddenInput(['id'=>"value_new-{$model->object_id}-{$model->query_tool}"])->label(FALSE) ?>

	<?= $form->field($model, 'approv_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'approv_date')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'approv_status')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'status')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'created_at')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_by')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'updated_at')->hiddenInput()->label(FALSE) ?>

    <div class="text-right">
	<?= Html::submitButton('<i class="glyphicon glyphicon-comment"></i> '. Yii::t('app', 'Post'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
$('form#{$model->formName()}-{$model->object_id}-{$model->query_tool}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
	    //เพิ่มรายการ
            //load form ใหม่
            
            var count = Number($('#count-$object_id-$query_tool').html())+1;
            $('#count-$object_id-$query_tool').html(count);
            $('#comment-list-$object_id-$query_tool').append(result.html);
            getComment_{$object_id}_{$query_tool}();
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

function getComment_{$object_id}_{$query_tool}() {
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezforms2/ezform-community/comment', 
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                ]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#comment-box-$object_id-$query_tool').html(result);
                }
            });
        }
");?>