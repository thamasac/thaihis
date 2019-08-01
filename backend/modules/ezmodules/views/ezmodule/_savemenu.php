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
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Menu')?></h4>
    </div>
    
    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>
    <?php
        $settings = [
            'minHeight' => 300,
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
                
            ]
        ];
//        $lang = Yii::$app->language;
//        if ($lang != 'en-US') {
//            $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
//        }
        ?>
    <?php
    if ($model->menu_order == "") $model->menu_order=10000;

    $orderitems = ArrayHelper::merge(ArrayHelper::map($modelOrderMenu, 'menu_order', 'menu_name'), [10000=> Yii::t('ezmodule', 'Last')]);
    
    foreach ($modelOrderMenu as $key => $menu) {
        if ($menu->menu_parent != 0) {
            $orderitems[$menu->menu_order]="before '-----".$menu->menu_name."'";
        } else {
            $orderitems[$menu->menu_order]= "before '".$menu->menu_name."'";
        }
        
    }

    $submenuitems = ArrayHelper::merge([0=>Yii::t('ezmodule', 'Main Menu')],ArrayHelper::map($modelMenu, 'menu_id', 'menu_name'));
    ?>
    <div class="modal-body">
	<div class="row">
		<div class="col-md-4 "><?= $form->field($model, 'menu_name')->textInput(['maxlength' => true]) ?></div>
		<div class="col-md-4 sdbox-col"><?= $form->field($model, 'menu_parent')->dropDownList($submenuitems) ?></div>
		<div class="col-md-4 sdbox-col"><?= $form->field($model, 'menu_order')->dropDownList($orderitems) ?></div>
	</div>
        
        
        <?php
        echo $form->field($model, 'menu_content')->widget(vova07\imperavi\Widget::className(), [
            
            'settings' => $settings
        ]);

        ?>

	<?= $form->field($model, 'menu_active')->hiddenInput()->label(FALSE) ?>
	
	<?= Html::activeHiddenInput($model, 'updated_at') ?>
        <?= Html::activeHiddenInput($model, 'updated_by') ?>
        <?= Html::activeHiddenInput($model, 'created_at') ?>
        <?= Html::activeHiddenInput($model, 'created_by') ?>
	
	<?= $form->field($model, 'ezm_id')->hiddenInput()->label(FALSE) ?>

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
	    getMenuContent($('#ezmodule-menu').attr('data-url'));
            $(document).find('#modal-ezmodule-menu').modal('hide');
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});


function getMenuContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-menu').html(result);
            }
        });
    }

");?>