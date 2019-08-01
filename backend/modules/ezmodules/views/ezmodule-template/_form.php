<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTemplate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-template-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Template')?></h4>
    </div>

    <div class="modal-body">
	<?php
        $settings = [
            'minHeight' => 500,
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
//        $lang = Yii::$app->language;
//        if ($lang != 'en-US') {
//            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
//        }
        ?>
        
	<?= $form->field($model, 'template_name')->textInput(['maxlength' => true]) ?>
        
        <div class="form-group pull-right">
            <a id="modal-addbtn-ezmodule-widget" style="cursor: pointer;" class="btn btn-danger btn-xs" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/list'])?>"><i class="fa fa-puzzle-piece"></i> <?= Yii::t('ezmodule', 'Widget')?></a>
        
        </div>
        
        <?php
        echo $form->field($model, 'template_html')->widget(appxq\sdii\widgets\FroalaEditorWidget::className(), [
            'clientOptions' => [
                'zIndex' => 1000,
            ]
        ]);

        ?>
        <?=
            $form->field($model, 'template_js')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'javascript', // programing language mode. Default "html"
                'id' => 'template_js'
            ]);
            ?>
        <?=
            $form->field($model, 'template_css')->widget('appxq\sdii\widgets\AceEditor', [
                'mode' => 'css', // programing language mode. Default "html"
                'id' => 'template_css'
            ]);
            ?>
	<?= $form->field($model, 'public')->checkbox() ?>
        
        <?php
        if (Yii::$app->user->can('administrator')) {
            echo $form->field($model, 'template_system')->checkbox();
        } else {
            echo $form->field($model, 'template_system')->hiddenInput()->label(FALSE);
        }
        ?>
        
	<?= $form->field($model, 'sitecode')->hiddenInput()->label(FALSE) ?>

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

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-widget',
    'size'=>'modal-lg',
]);
?>
<?php  
$this->registerJs("
    

$('#modal-ezmodule-widget').on('click', '.btn-widget', function() {
    $('#ezmoduletemplate-template_html').froalaEditor('html.insert', $(this).attr('data-widget'), false);
});

$('#modal-addbtn-ezmodule-widget').on('click', function() {
    modalEzmoduleWidget($(this).attr('data-url'));
    return false;
});

function modalEzmoduleWidget(url) {
    $('#modal-ezmodule-widget .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-widget').modal('show')
    .find('.modal-content')
    .load(url);
}


");?>