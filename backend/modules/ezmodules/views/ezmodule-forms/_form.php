<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\classes\ModuleQuery;
use yii\web\JsExpression;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleForms */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-forms-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Forms') ?></h4>
    </div>

    <div class="modal-body">
        
        <div class="row">
            <div class="col-md-6 ">
            <?php
            $options = \appxq\sdii\utils\SDUtility::string2Array($model->options);
            $modelForms = ModuleQuery::getEzformList($ezf_id);
            $dataForm= [];
            if(isset($modelForms)){
                $dataForm = \yii\helpers\ArrayHelper::map($modelForms, 'ezf_id', 'ezf_name');
            }
            echo $form->field($model, 'ezf_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $dataForm,
                'options' => ['prompt' => Yii::t('ezmodule', 'Form')],
                'pluginOptions' => [
                    'allowClear' => false,
                ],
                'pluginEvents' => [
                    "change"=>"function(e) { $('#subforms-options').html(''); $('#show_options').html(''); $('#field_options').html(''); }",
                    "select2:select" => "function(e) { $('#ezmoduleforms-form_name').val(e.params.data.text); $('#action_subforms .add-subform').attr('data-ezf_id', e.params.data.id); $('#show_subforms a').attr('data-ezf_id', e.params.data.id); $('#field_subforms a').attr('data-ezf_id', e.params.data.id); }",
                    "select2:unselect" => "function(e) { $('#ezmoduleforms-form_name').val(''); $('#action_subforms .add-subform').attr('data-ezf_id', 0); $('#show_subforms a').attr('data-ezf_id', 0); $('#field_subforms a').attr('data-ezf_id', 0);}"
                ]
            ]);
            ?>
            </div>
            <div class="col-md-3 sdbox-col">
            <?= $form->field($model, 'form_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 sdbox-col">
            <?= $form->field($model, 'form_order')->textInput(['type'=>'number']) ?>
            </div>
        </div>
        <div id="forms-options-box">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('ezmodule', 'Fields') ?></h3>
                </div>
                <div class="panel-body">
                    <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_field_header')?>
                    <div id="field_options">
                        <?php
                        $dataFields = [];
                        if(isset($model->ezf_id) && $model->ezf_id>0){
                            $modelFields = ModuleQuery::getFieldsOptionList($model->ezf_id);
                            if(isset($modelFields)){
                                $dataFields = \yii\helpers\ArrayHelper::map($modelFields, 'id', 'name');
                            }
                            if(isset($options['fields']) && !empty($options['fields'])){
                                foreach ($options['fields'] as $key => $value) {
                                    echo $this->render('_field', [
                                        'id'=>$key,
                                        'ezf_id'=>$model->ezf_id,
                                        'dataFields'=>$dataFields,
                                        'value'=>$value,
                                        'prefix'=>'',
                                    ]);
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer" id="field_subforms">
                        <a style="cursor: pointer;" data-div="field_options"  data-ezf_id="<?=isset($model->ezf_id)?$model->ezf_id:0?>" data-prefix="" class="btn btn-primary btn-sub-field"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Field')?></a>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('ezmodule', 'Condition')?></h3>
                </div>


                <div class="panel-body" id="condition-options">
                    <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_condition_header')?>

                    <div id="condition-items">
                        <?php
                        
                        $modelFormsCond;
                        $dataFormCond;
                        if(isset($options['conditions']) && !empty($options['conditions'])){
                            $modelFormsCond = \backend\modules\ezforms2\classes\EzfQuery::getEzformList($ezf_id);
                            $dataFormCond = [];
                            if(isset($modelFormsCond)){
                                $dataFormCond = \yii\helpers\ArrayHelper::map($modelFormsCond, 'ezf_id', 'ezf_name');
                            }

                            foreach ($options['conditions'] as $key => $value) {
                                echo $this->render('_condition', [
                                    'id'=>$key,
                                    'ezf_id'=>$ezf_id,
                                    'dataForm'=>$dataFormCond,
                                    'value'=>$value,
                                    'prefix'=>'',
                                ]);
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer" id="condition_subforms">
                        <a id="add-condition" data-div="condition-items" data-ezf_id="<?=$ezf_id?>" data-prefix="" style="cursor: pointer;" class="btn btn-warning btn-sub-condition"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Condition')?></a>
                    </div>
                </div>
            </div>

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('ezmodule', 'Show Items') ?></h3>
                </div>
                <div class="panel-body">
                    <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_show_header')?>
                    <div id="show_options">
                        <?php
                        if(isset($model->ezf_id) && $model->ezf_id>0){
                           
                            if(isset($options['show']) && !empty($options['show'])){
                                foreach ($options['show'] as $key => $value) {
                                    echo $this->render('_show', [
                                        'id'=>$key,
                                        'ezf_id'=>$model->ezf_id,
                                        'dataFields'=>$dataFields,
                                        'value'=>$value,
                                        'prefix'=>'',
                                    ]);
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer" id="show_subforms">
                        <a style="cursor: pointer;" data-div="show_options"  data-ezf_id="<?=isset($model->ezf_id)?$model->ezf_id:0?>" data-prefix="" class="btn btn-success btn-sub-show"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Show Items')?></a>
                    </div>
                </div>
            </div>
        
<!--        Forms Box         -->
        
            <div class="modal-header" style="margin-bottom: 10px;">
                <h4 class="modal-title"><?= Yii::t('ezmodule', 'Hierarchical Forms') ?> lvl 1</h4>
            </div>

            <div id="subforms-options">
            <?php
                if(isset($model->ezf_id) && $model->ezf_id>0){
                    
                    if(isset($options['forms']) && !empty($options['forms'])){
                        $modelSubForms = backend\modules\ezmodules\classes\ModuleQuery::getEzformList($model->ezf_id);
                        $data_subforms = [];
                        if(isset($modelSubForms)){
                            $data_subforms = \yii\helpers\ArrayHelper::map($modelSubForms, 'ezf_id', 'ezf_name');
                        }
                        
                        foreach ($options['forms'] as $key => $value) {
                            echo $this->render('_subform', [
                                'id'=>$key,
                                'ezf_id'=>$model->ezf_id,
                                'parent_ezf_id'=>$ezf_id,
                                'lvl'=>1,
                                'color'=>'info',
                                'prefix'=>'',
                                'margin'=>0,
                                'dataForm'=>$data_subforms,
                                'dataFormCond'=>$dataFormCond,
                                'value'=>$value,
                            ]);
                        }
                    }
                }
                
                
            ?>
            </div>

            <div id="action_subforms" class="text-right">
                <a style="cursor: pointer;" data-margin="0" data-div="subforms-options" data-color="info" data-prefix="" data-lvl="1" data-parent_ezf_id="<?=$ezf_id?>" data-ezf_id="<?=isset($model->ezf_id)?$model->ezf_id:0?>" class="btn btn-info add-subform"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Form')?> lvl 1</a>
            </div>
        </div>
        
        
        <?= $form->field($model, 'ezm_id')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'form_default')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'created_by')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'updated_by')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>

    </div>
        
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
$('#forms-options-box').on('focus', 'input[type=text]',function() {
   $(this).select();
});

$('#forms-options-box').on('click', '.btn-sub-condition', function(){
    var ezf_id = $(this).attr('data-ezf_id');
    if(ezf_id>0){
        getCondition(ezf_id, $(this).attr('data-div'), $(this).attr('data-prefix'));
    } else {
        ". SDNoty::show("'".Yii::t('ezmodule', 'Please select a form.')."'", "'error'") ."
    }
});

$('#forms-options-box').on('change', '.ccond-input', function(){
    if($(this).val()=='BETWEEN'){
	$(this).parent().parent().find('.cvalue2-input').removeAttr('readonly');
    } else {
	$(this).parent().parent().find('.cvalue2-input').attr('readonly','readonly');
	$(this).parent().parent().find('.cvalue2-input').val('');
    }
});

function getCondition(ezf_id, div, prefix) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-forms/get-condition']) . "',
        data:{ezf_id:ezf_id, prefix:prefix},      
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+div).append(result);
        }
    });
}

$('#forms-options-box').on('click', '.btn-sub-field', function(){
    var ezf_id = $(this).attr('data-ezf_id');
    if(ezf_id>0){
        getField(ezf_id, $(this).attr('data-div'), $(this).attr('data-prefix'));
    } else {
        ". SDNoty::show("'".Yii::t('ezmodule', 'Please select a form.')."'", "'error'") ."
    }
});

$('#forms-options-box').on('click', '.btn-sub-show', function(){
    var ezf_id = $(this).attr('data-ezf_id');
    if(ezf_id>0){
        getShow(ezf_id, $(this).attr('data-div'), $(this).attr('data-prefix'));
    } else {
        ". SDNoty::show("'".Yii::t('ezmodule', 'Please select a form.')."'", "'error'") ."
    }
});

$('#forms-options-box').on('change', '.scond-input', function(){
    if($(this).val()=='BETWEEN'){
	$(this).parent().parent().find('.svalue2-input').removeAttr('readonly');
    } else {
	$(this).parent().parent().find('.svalue2-input').attr('readonly','readonly');
	$(this).parent().parent().find('.svalue2-input').val('');
    }
});

function getField(ezf_id, div, prefix) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-forms/get-field']) . "',
        data:{ezf_id:ezf_id, prefix:prefix},      
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+div).append(result);
        }
    });
}

function getShow(ezf_id, div, prefix) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-forms/get-show']) . "',
        data:{ezf_id:ezf_id, prefix:prefix},      
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+div).append(result);
        }
    });
}

$('#forms-options-box').on('click', '.add-subform', function(){
    let ezf_id = $(this).attr('data-ezf_id');
    let parent_ezf_id = $(this).attr('data-parent_ezf_id');
    if(ezf_id>0){
        getSubForm(parent_ezf_id, ezf_id, $(this).attr('data-div'), $(this).attr('data-lvl'), $(this).attr('data-color'), $(this).attr('data-prefix'), $(this).attr('data-margin'));
    } else {
        ". SDNoty::show("'".Yii::t('ezmodule', 'Please select a form.')."'", "'error'") ."
    }
});

$('#forms-options-box').on('click', '.btn-del',function(){
    $(this).parent().parent().remove();
});

function getSubForm(parent_ezf_id, ezf_id, div, lvl, color, prefix, margin) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-forms/get-subform']) . "',
        data:{parent_ezf_id:parent_ezf_id, ezf_id:ezf_id, lvl:lvl, color:color, prefix:prefix, margin:margin},    
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+div).append(result);
        }
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
	    if(result.action == 'create') {

            } else if(result.action == 'update') {
		
	    }
            $(document).find('#modal-ezmodule-forms').modal('hide');
            reloadGridAjax();
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
	} 
    }).fail(function() {
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

function reloadGridAjax() {
    var url = $('#$reloadDiv').attr('data-url');
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#$reloadDiv').html(result);
        }
    });
}
");?>