<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformInput */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-input-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel">Ezform Input</h4>
    </div>

    <div class="modal-body">
	
	<div class="row">
		<div class="col-md-6 ">
		    <?= $form->field($model, 'input_name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-6 sdbox-col">
		    <?= $form->field($model, 'system_class')->textInput(['maxlength' => true]) ?>
		    
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 ">
		    <?= $form->field($model, 'input_class')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-6 sdbox-col">
		    <?= $form->field($model, 'input_behavior')->textInput(['maxlength' => true]) ?>
		</div>
	</div>
	
	<div class="row">
                <div class="col-md-4 ">
		    <?= $form->field($model, 'input_function')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 sdbox-col">
		    <?= $form->field($model, 'table_field_type')->dropDownList([
			'none'=>'(NOT SET)',
                        'field'=>'(FIELD SET)',
			'VARCHAR'=>'VARCHAR',
			'INT'=>'INT',
			'TEXT'=>'TEXT',
			'DATE'=>'DATE',
			'DATETIME'=>'DATETIME',
			'DOUBLE'=>'DOUBLE',
			'TINYINT'=>'TINYINT',
			'BIGINT'=>'BIGINT',
			'LONGTEXT'=>'LONGTEXT',
		    ]) ?>
		</div>
		<div class="col-md-4 sdbox-col">
		    <?= $form->field($model, 'table_field_length')->textInput(['type'=>'number']) ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6 ">
		    <?= $form->field($model, 'input_data')->textarea(['rows' => 3]) ?>
		</div>
		<div class="col-md-6 sdbox-col">
		    <?= $form->field($model, 'input_validate')->textarea(['rows' => 3]) ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6 ">
		    <?= $form->field($model, 'input_specific')->textarea(['rows' => 3]) ?>
		</div>
		<div class="col-md-6 sdbox-col">
		    <?= $form->field($model, 'input_option')->textarea(['rows' => 3]) ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3 ">
		    <?= $form->field($model, 'input_order')->textInput(['type'=>'number']) ?>
		</div>
            <div class="col-md-3 sdbox-col">
		    <?= $form->field($model, 'input_size')->dropDownList([1=>'8.32% (1)',2=>'16.64% (2)',3=>'25% (3)',4=>'33.33% (4)',5=>'41.67% (5)',6=>'50%  (6)',7=>'58.36%  (7)',8=>'66.66%  (8)',9=>'75%  (9)',10=>'83.34%  (10)',11=>'91.66%  (11)',12=>'100%  (12)']) ?>
		</div>
            <div class="col-md-3 sdbox-col">
              <?php
               $categoryList = \backend\modules\core\classes\CoreFunc::itemAlias('input_category');
              echo $form->field($model, 'input_category')->dropDownList($categoryList, ['prompt'=> Yii::t('ezform', 'None')]);
              ?>
          </div>
            <div class="col-md-3 sdbox-col" style="margin-top: 22px;">
                <?= $form->field($model, 'input_active')->checkbox() ?>
            </div>
	</div>
	
	<?= $form->field($model, 'input_version')->hiddenInput()->label(false) ?>
        <?php
        echo $form->field($model, 'icon')->widget(\trntv\filekit\widget\Upload::classname(), [
            'id' => 'widget_input_icon',
            'url' => ['/core/file-storage/input-upload']
        ]);
        ?>
        <?= $form->field($model, 'input_link')->textInput() ?>
        <?php 
               echo '<label>Help</label>';
               echo appxq\sdii\widgets\FroalaEditorWidget::widget([
                'name' => "content",
                'value' => $content,
                   'toolbar_size'=>'sm',
                'options' => ['id'=>"content-{$model->input_id}"]
            ]);
                ?>
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
		$(document).find('#modal-ezform-input').modal('hide');
		$.pjax.reload({container:'#ezform-input-grid-pjax'});
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezform-input').modal('hide');
		$.pjax.reload({container:'#ezform-input-grid-pjax'});
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