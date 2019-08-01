<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezmodules\classes\ModuleFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleWidget */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-widget-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Widget')?></h4>
    </div>

    <div class="modal-body">
        <div class="row">
                <div class="col-md-6 "><?= $form->field($model, 'widget_name')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6 sdbox-col"><?= $form->field($model, 'widget_varname')->textInput(['maxlength' => true, 'disabled'=>(!Yii::$app->user->can('administrator'))]) ?></div>
        </div>
	<?php if(Yii::$app->user->can('administrator')):?>
            <div class="row">
                    <div class="col-md-8 "><?= $form->field($model, 'widget_render')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-md-4 sdbox-col" style="margin-top: 22px;"><?= $form->field($model, 'widget_attribute')->checkbox() ?></div>
            </div>
            
        <?php else:?>
            <?= $form->field($model, 'widget_render')->hiddenInput()->label(FALSE) ?>
            <?= $form->field($model, 'widget_attribute')->hiddenInput()->label(FALSE) ?>
        <?php endif;?>

	<?= $form->field($model, 'widget_detail')->textarea(['rows' => 3]) ?>
	<?= $form->field($model, 'widget_example')->textarea(['rows' => 3]) ?>
        <?= $form->field($model, 'enable')->checkbox() ?>
	<?= $form->field($model, 'options')->hiddenInput()->label(false) ?>
        <?php
        
        $items_widget = ModuleFunc::itemAlias('widget');
        $items_widget_db = \backend\modules\core\classes\CoreFunc::itemAlias('widget');
        $items_widget = yii\helpers\ArrayHelper::merge($items_widget, $items_widget_db);
        
        if(Yii::$app->user->can('administrator')){
            $items_widget['core'] = 'Core item';
        }
        
        $model->widget_type = (isset($model->widget_type) && !empty($model->widget_type))?$model->widget_type:'core_content';
        ?>
      
        <?php
        echo $form->field($model, 'widget_type')->dropDownList($items_widget, ['placeholder' => 'Select Widget']);
        
//        echo $form->field($model, 'widget_type')->textInput(['placeholder' => 'Select Widget', 'list'=>'widget-list']);
//       
//       echo '<datalist id="widget-list">';
//       foreach ($items_widget as $keyw => $valuew) {
//          echo '<option value="'.$keyw.'">'.$valuew.'</option>';
//       }
//       echo '</datalist>';

//           echo $form->field($model, 'widget_type')->widget(\kartik\select2\Select2::className(),[
//            'data' => $items_widget,
//            'options' => ['placeholder' => 'Select Widget'],
//            'pluginOptions' => [
//                'allowClear' => FALSE,
//            ]
//        ]);
           ?>
      
      <div id="widget-config">
          
      </div>

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  
$js = "$.pjax.reload({container:'#ezmodule-widget-grid-pjax'});";
if($modal==1){
    $js = "
        $('#modal-add-widget').modal('show')
    .find('.modal-content')
    .load('".Url::to(['/ezmodules/ezmodule-widget/list-module', 'ezm_id'=>$ezm_id])."');
        ";
} 
$this->registerJs("

genFormItem('{$model->widget_type}', '{$model->widget_id}', '{$model->ezf_id}');

$('#ezmodulewidget-widget_type').on('change', function() {
    genFormItem($(this).val(), '{$model->widget_id}', '{$model->ezf_id}', '{$model->ezm_id}');
});

function genFormItem(widget_type, id, ezf_id, ezm_id) {
    $('#widget-config').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $.ajax({
	    method: 'POST',
	    url:'".Url::to(['/ezmodules/ezmodule-widget/get-form'])."',
            data: {widget:widget_type, id:id, ezf_id:ezf_id, ezm_id:ezm_id},    
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    $('#widget-config').html(result.html);
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
    });
}

$('form#{$model->formName()}').on('afterValidate', function (e) {
    let scroll = $('form#{$model->formName()} .form-group.has-error').offset();
    if(scroll){
        $('#modal-ezmodule-widget').animate({ scrollTop: scroll.top }, 300);
    }
});

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
                
	    } else if(result.action == 'update') {
                
	    }
            $js
            $(document).find('#modal-ezmodule-widget').modal('hide');
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