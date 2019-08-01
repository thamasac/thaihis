<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformFields */
/* @var $form yii\bootstrap\ActiveForm */
\backend\modules\ezforms2\assets\EzfColorInputAsset::register($this);
yii\bootstrap\ActiveField::className();
?>

<div class="ezform-fields-form">

    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
    ]);
    ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezform', 'Notification') ?></h4>
    </div>

    <div class="modal-body">

        <!--        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Basic Settings') ?></a></li>
                    <li role="presentation"><a href="#setting" aria-controls="setting" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> <?php // echo  Yii::t('ezform', 'Advanced settings')    ?></a></li>
                </ul>-->

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div id="box-data" class="col-md-12">
                    <div class="sdloader "><i class="sdloader-icon"></i></div>
                </div>
                <div class="clearfix"></div>
                <div class="row" style="display: none;">
                    <?php
                    //init data
                    $specific = isset($model['ezf_field_options']['specific']) ? $model['ezf_field_options']['specific'] : [];
                    $icon = isset($specific['icon']) ? $specific['icon'] : '';
                    $color = isset($specific['color']) ? $specific['color'] : '';
                    ?>
                    <div class="col-md-8 ">
                        <?= $form->field($model, 'ezf_field_label')->textInput(['maxlength' => true,])->label('1. Specify question statement.') ?>
                    </div>
                    <div class="clearfix"></div>

                </div>

                <div class="row" style="display: none;">
                    <div class="col-md-8 ">
                        <?= $form->field($model, 'ezf_field_name')->textInput(['maxlength' => true])->label('2. Specify variable name (field name) AND specify lenght of the question area.') ?>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="row">
                    <div class="col-md-12 ">
                        <?php
//                        echo Html::label(Yii::t('ezform', '3. Select question type.'), 'EzformFields[ezf_field_type]');
                        ?>
                            <?= $form->field($model, 'ezf_field_type')->hiddenInput()->label(FALSE) ?>
                        <div id="input-type-info" style="display: none;">
                            <div class="modal-header" >
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
                                <h4 class="modal-title" ><?= Yii::t('ezform', 'Help') ?></h4>
                            </div>
                            <div id="input-info-content" class="modal-body" ></div>
                        </div>
                    </div>
                    <div class="col-md-2 sdbox-col">

                    </div>
                    <div class="col-md-2 sdbox-col">

                    </div>
                </div>

<!--                <label style="margin-top: 15px;">--><?php //echo Yii::t('ezform', 'Notification setting.') ?><!--</label>-->
<!--                <div class="well" >-->



                    <?php //echo $form->field($model, 'table_field_length')->textInput(['maxlength' => true])   ?>
                    <?php echo $form->field($model, 'table_field_length')->hiddenInput()->label(FALSE) ?>
                    <?php //echo $form->field($model, 'ezf_field_order')->textInput(['type' => 'number'])  ?>
                    <?php echo $form->field($model, 'ezf_field_order')->hiddenInput()->label(FALSE) ?>

<!--                    <div id="box-data">-->
<!--                        ...-->
<!--                    </div>-->

<!--                </div>-->



            </div>  


            <!--	<div class="row">
                        <div class="col-lg-12">
                            <p style="cursor:pointer" id="btn-setting"><i class="fa fa-cog"></i> <?= Yii::t('ezform', 'Advanced settings') ?></p>
                        </div>
                    </div>-->



            <?= $form->field($model, 'ezf_field_id')->hiddenInput()->label(FALSE) ?>

            <?= $form->field($model, 'ezf_id')->hiddenInput()->label(FALSE) ?>
            <?= $form->field($model, 'ezf_version')->hiddenInput()->label(FALSE) ?>

            <?= $form->field($model, 'ezf_field_group')->hiddenInput()->label(FALSE) ?>


            <?= $form->field($model, 'ezf_field_ref')->hiddenInput()->label(FALSE) ?>



            <?= $form->field($model, 'table_field_type')->hiddenInput()->label(FALSE) ?>

        </div>
        <div role="tabpanel" class="tab-pane" id="setting">
            <div id="box-config">
                <div id="box-options">

                </div>

                <div id="box-validations">

                </div>
            </div>
        </div>
    </div>




</div>
<div class="modal-footer">
    <?= Html::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => ($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary') . ' btn-submit', 'id' => 'btnNotify']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true" ><?= Yii::t('app', 'Close') ?></button>
</div>

<?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("

var color_options = {
    showInput: true,
    showInitial:true,
    allowEmpty:true,
    showPalette:true,
    showSelectionPalette:true,
    hideAfterPaletteSelect:true,
    showAlpha:false,
    preferredFormat:'hex',
    palette: [
        ['#000','#444','#666','#999','#ccc','#eee','#f3f3f3','#fff'],
        ['#f00','#f90','#ff0','#0f0','#0ff','#00f','#90f','#f0f'],
        ['#f4cccc','#fce5cd','#fff2cc','#d9ead3','#d0e0e3','#cfe2f3','#d9d2e9','#ead1dc'],
        ['#ea9999','#f9cb9c','#ffe599','#b6d7a8','#a2c4c9','#9fc5e8','#b4a7d6','#d5a6bd'],
        ['#e06666','#f6b26b','#ffd966','#93c47d','#76a5af','#6fa8dc','#8e7cc3','#c27ba0'],
        ['#c00','#e69138','#f1c232','#6aa84f','#45818e','#3d85c6','#674ea7','#a64d79'],
        ['#900','#b45f06','#bf9000','#38761d','#134f5c','#0b5394','#351c75','#741b47'],
        ['#600','#783f04','#7f6000','#274e13','#0c343d','#073763','#20124d','#4c1130']
    ]
};





$('#box-data').on('blur', '.check_varname', function() {
    var value = $(this).val();
    
    if(!value.match(/^[a-z0-9_]+$/i)){
        $(this).parent().find('.help-block').remove();
        $(this).parent().append('<div class=\"help-block help-block-error\"><code>" . Yii::t('ezform', 'Variable name must be in English or numbers only and do not contain spaces.') . "</code></div>');
        $(this).focus();
    } else {
        $(this).parent().find('.help-block').remove();
    }

});

$('#btn-type-help').on('click', function() {
    $('#modal-gridtype .modal-content').html($('#input-type-info').html());
    $('#modal-gridtype').modal('show');
});

$('#" . $model->formName() . "').on('focus', 'input[type=text]',function() {
   $(this).select();
});

$('#specific-color').spectrum(color_options);
$('#ezformfields-ezf_field_color').spectrum(color_options);

$('#btnNotify').on('click', function(e) {
   
    $('.btn-submit').attr('disabled', true);
    var \$form = $('#{$model->formName()}');
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
		$(document).find('#$reloadDiv-add-notify').modal('hide');
                $('#$reloadDiv-modal').modal('show')
                .find('.modal-content')
                .load('/notify/default/detail?ezf_id=$dataEzf->ezf_id&v=$dataEzf->ezf_version&modal=$reloadDiv-modal&reloadDiv=$reloadDiv');
	    } else if(result.action == 'update') {
		$(document).find('#$reloadDiv-add-notify').modal('hide');
                $('#$reloadDiv-modal').modal('show')
                .find('.modal-content')
                .load('/notify/default/detail?ezf_id=$dataEzf->ezf_id&v=$dataEzf->ezf_version&modal=$reloadDiv-modal&reloadDiv=$reloadDiv');
	    }
	    if(result.alterTable == false) {
		" . SDNoty::show('"' . Yii::t('ezform', 'Column creation failed.') . '"', '"error"') . "
                    $('.btn-submit').attr('disabled', false);
	    }
	} else {
	    " . SDNoty::show('result.message', 'result.status') . "
                $('.btn-submit').attr('disabled', false);
	} 
    }).fail(function() {
        $('.btn-submit').attr('disabled', false);
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    return false;
});

var field_type_tmp = $('#ezformfields-ezf_field_type').val();
$('#ezformfields-ezf_field_type').change(function() {
    var type_tmp = 1;
    if(field_type_tmp==$(this).val()){
        type_tmp = 0;
    }
    getViewEditor($(this).val(), type_tmp);
    
    getInputInfo($(this).val());
    
    
    
});

$('#btn-setting').click(function(){
    $('#box-config').toggle();
});

getViewEditor($('#ezformfields-ezf_field_type').val(), 0);
getInputInfo($('#ezformfields-ezf_field_type').val());

function getViewEditor(id, newitem){
    $.ajax({
		method: 'POST',
		url:'" . yii\helpers\Url::to(['/ezbuilder/ezform-fields/view-input']) . "',
		data: {id:id, newitem:newitem, ezf_field_id:'" . $model->ezf_field_id . "', ezf_id:'" . $model->ezf_id . "', label:$('#ezformfields-ezf_field_label').val(), name:$('#ezformfields-ezf_field_name').val()},
		dataType: 'JSON',
		success: function(result, textStatus) {
		    if(result.status == 'success') {
			$('#box-data').html(result.html);
//			$('#box-options').html(result.options);
			$('#box-validations').html(result.validations);
                        $('#ezformfields-ezf_field_lenght').val(result.size);
		    } else {
			$('#box-data').html('');
//			$('#box-options').html('');
			$('#box-validations').html('');
                        $('#ezformfields-ezf_field_lenght').val(3);
			" . SDNoty::show('result.message', 'result.status') . "
		    }
		}
    });
}

function getInputInfo(id){
    $.ajax({
        method: 'POST',
        url:'" . yii\helpers\Url::to(['/ezbuilder/ezform-fields/input-info']) . "',
        data: {id:id},
        dataType: 'JSON',
        success: function(result, textStatus) {
            if(result.status == 'success') {
                $('#input-info-content').html(result.html);
            } else {
                $('#input-info-content').html(result.html);
            }
        }
    });
   
}
");
?>