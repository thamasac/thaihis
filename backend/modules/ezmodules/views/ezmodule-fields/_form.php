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
/* @var $model backend\modules\ezmodules\models\EzmoduleFields */
/* @var $form yii\bootstrap\ActiveForm */


?>

<div class="ezmodule-fields-form">

    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
    ]);
    ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Field') ?></h4>
    </div>

    <div class="modal-body">
        <?php
        $fieldDesc = isset($model->ezf_field_id)?\backend\modules\ezforms2\classes\EzfQuery::getFieldById($model->ezf_field_id):NULL;
        $initValueText = null;
        if(isset($fieldDesc)){
            $initValueText = (isset($fieldDesc->ezf_field_label) && !empty($fieldDesc->ezf_field_label))?$fieldDesc->ezf_field_label:$fieldDesc->ezf_field_name;
        }
        echo $form->field($model, 'ezf_field_id')->widget(\kartik\widgets\Select2::classname(), [
            'initValueText' => $initValueText,
            //'options' => ['id' => $model->formName().'_ezf_field_id'],
            'pluginOptions' => [
                'allowClear' => false,
                'ajax' => [
                    'url' => Url::to(['/ezmodules/ezmodule-fields/get-fields', 'inform'=>$inform]),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(result) { return result.text; }'),
                'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
            ],
            'pluginEvents' => [
                "select2:select" => "function(e) { $('#ezmodulefields-ezf_id').val(e.params.data.ezf_id); $('#ezmodulefields-field_name').val(e.params.data.text); }",
                "select2:unselect" => "function(e) { $('#ezmodulefields-ezf_id').val(''); $('#ezmodulefields-field_name').val('');}"
            ]
        ]);
        ?>
        
        <?= $form->field($model, 'field_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'field_order')->textInput(['type'=>'number']) ?>
<?= $form->field($model, 'order_by')->checkbox() ?>

        <div class="row">
            <div class="col-md-6 ">
            <div class="form-group">
            <label class="control-label"><?= Yii::t('ezmodule', 'Width') ?></label>
<?= Html::textInput('EzmoduleFields[options][width]', isset($model->options['width']) ? $model->options['width'] : 110, ['class' => 'form-control', 'type' => 'number']) ?>
        </div>
            </div>
            <div class="col-md-6 sdbox-col">
            <div class="form-group">
            <label class="control-label"><?= Yii::t('ezmodule', 'Align') ?></label>
<?= Html::dropDownList('EzmoduleFields[options][align]', isset($model->options['align']) ? $model->options['align'] : '', ModuleFunc::itemAlias('align'), ['class' => 'form-control']) ?>
        </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 ">
            <div class="form-group">
            <label class="control-label"><?= Yii::t('ezmodule', 'Show in PDF') ?></label>
<?= Html::dropDownList('EzmoduleFields[options][pdf]', isset($model->options['pdf']) ? $model->options['pdf'] : 1, [1 => 'Yes', 0 => 'No'], ['class' => 'form-control']) ?>
        </div>
            </div>
            <div class="col-md-6 sdbox-col">
            <div class="form-group">
            <label class="control-label"><?= Yii::t('ezmodule', 'Width PDF') ?></label>
<?= Html::textInput('EzmoduleFields[options][width_pdf]', isset($model->options['width_pdf']) ? $model->options['width_pdf'] : 25, ['class' => 'form-control', 'type' => 'number']) ?>
        </div>
            </div>
        </div>
        
        <?= $form->field($model, 'ezm_id')->hiddenInput()->label(FALSE) ?>
        <?= $form->field($model, 'ezf_id')->hiddenInput()->label(FALSE) ?>

        
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

<?php $this->registerJs("
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
		
	    } else if(result.action == 'update') {
		
	    }
            $(document).find('#modal-ezmodule-fields').modal('hide');
            reloadGridAjax();
	} else {
	    " . SDNoty::show('result.message', 'result.status') . "
	} 
    }).fail(function() {
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
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

"); ?>