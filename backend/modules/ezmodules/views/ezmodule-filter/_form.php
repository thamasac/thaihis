<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleFilter */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-filter-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Filter')?></h4>
    </div>

    <div class="modal-body">
	
        <div class="row">
                <div class="col-md-9 "><?= $form->field($model, 'filter_name')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3 sdbox-col"><?= $form->field($model, 'filter_order')->textInput(['type'=>'number']) ?></div>
        </div>
	<div class="row">
                <div class="col-md-3 "><?= $form->field($model, 'public')->checkbox() ?></div>
                <div class="col-md-3 sdbox-col">
                    <?php
                    if($user_module==$userId){
                        echo $form->field($model, 'ezm_default')->checkbox();
                    } else {
                        echo $form->field($model, 'ezm_default')->hiddenInput()->label(false);
                    }
                    ?>
                </div>
        </div>
        
        <?php $userlist = backend\modules\ezforms2\classes\EzfQuery::getIntUserAll();?>
        <?=
        $form->field($model, 'share')->widget(\kartik\select2\Select2::className(), [
            'options' => ['placeholder' => Yii::t('ezmodule', 'Share'), 'multiple' => true],
            'data' => \yii\helpers\ArrayHelper::map($userlist, 'id', 'text'),
            'pluginOptions' => [
                'tokenSeparators' => [',', ' '],
            ],
        ])
        ?>
        
        <?=$form->field($model, 'filter_type')->radioList([Yii::t('ezmodule', 'Manual Filter'), Yii::t('ezmodule', 'Conditional Filter')]);?>

	<?= $form->field($model, 'created_by')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'updated_by')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'filter_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'ezm_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'sitecode')->hiddenInput()->label(false) ?>
        
        <div id="condition-options" style="display: none;">
            <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_condition_header')?>
            
            <div id="condition-items">
                <?php
                $modelForms = \backend\modules\ezforms2\classes\EzfQuery::getEzformList($ezf_id);
                $dataForm= [];
                if(isset($modelForms)){
                    $dataForm = \yii\helpers\ArrayHelper::map($modelForms, 'ezf_id', 'ezf_name');
                }
                $options = \appxq\sdii\utils\SDUtility::string2Array($model->options);
                
                if(isset($options) && !empty($options)){
                    foreach ($options as $key => $value) {
                        echo $this->render('_condition', [
                            'id'=>$key,
                            'ezf_id'=>$ezf_id,
                            'dataForm'=>$dataForm,
                            'value'=>$value,
                        ]);
                    }
                }
                ?>
            </div>
            <div class="modal-footer">
                <a id="add-condition" style="cursor: pointer;" class="btn btn-success"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Condition')?></a>
            </div>
        </div>

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  
$jsLoadContent = "getFilterContent($('#ezmodule-filter').attr('data-url'));";
if($model->filter_type==1){
    $jsLoadContent = 'location.reload();';
}

$this->registerJs("
displayCondition($('input[name=\"EzmoduleFilter[filter_type]\"]:checked').val());

$('input[name=\"EzmoduleFilter[filter_type]\"]').change(function(){
    displayCondition($(this).val());
});

function displayCondition(filter_type) {
    if(filter_type==1){
        $('#condition-options').show();
    } else {
        $('#condition-options').hide();
    }
}

$('#add-condition').click(function(){
    getItem();
});

$('#condition-items').on('click', '.btn-del',function(){
    $(this).parent().parent().remove();
});

$('#condition-items').on('change', '.ccond-input', function(){
    if($(this).val()=='BETWEEN'){
	$(this).parent().parent().find('.cvalue2-input').removeAttr('readonly');
    } else {
	$(this).parent().parent().find('.cvalue2-input').attr('readonly','readonly');
	$(this).parent().parent().find('.cvalue2-input').val('');
    }
});

function getItem() {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-filter/get-condition', 'ezf_id'=>$ezf_id]) . "',
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#condition-items').append(result);
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
             $jsLoadContent  
	    if(result.action == 'create') {
		
                $(document).find('#modal-ezmodule-filter').modal('hide');
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezmodule-filter').modal('hide');
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

function getFilterContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-filter').html(result);
            }
        });
    }

");?>