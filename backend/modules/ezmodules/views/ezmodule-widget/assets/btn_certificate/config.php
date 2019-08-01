<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzformWidget;

$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
?>


<div class="form-group row">
    <?= Html::hiddenInput('options[reloadDiv]', isset($options['reloadDiv']) ? $options['reloadDiv'] : 'visit-' . SDUtility::getMillisecTime()); ?>
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Button</h4></div>
    <div class="col-md-3">
        <?= Html::label(Yii::t('ezform', 'Button Icon'), 'options[btn_icon]', ['class' => 'control-label']) ?>
        <?=
        dominus77\iconpicker\IconPicker::widget([
            'name' => 'options[btn_icon]',
            'value' => isset($options['btn_icon']) ? $options['btn_icon'] : '',
            'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon'],
            'clientOptions' => [
                'hideOnSelect' => true,
            ]
        ])
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Text'), 'options[btn_text]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[btn_text]', (isset($options['btn_text']) ? $options['btn_text'] : Yii::t('ezform', 'Button Text')), ['class' => 'form-control']) ?>
    </div>

    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Color'), 'options[btn_color]', ['class' => 'control-label']) ?>
        <?=
        Html::dropDownList('options[btn_color]', (isset($options['btn_color']) ? $options['btn_color'] : 'btn-default'), [
            'btn-default' => 'Default',
            'btn-primary' => 'Primary',
            'btn-success' => 'Success',
            'btn-info' => 'Info',
            'btn-warning' => 'Warning',
            'btn-danger' => 'Danger'
                ], ['class' => 'form-control'])
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Style'), 'options[btn_style]', ['class' => 'control-label']) ?>
        <?=
        Html::dropDownList('options[btn_style]', (isset($options['btn_style']) ? $options['btn_style'] : 'btn-block'), [
            'btn-block' => 'Block',
            'btn-lg' => 'Large',
            'btn-md' => 'Medium',
            'btn-sm' => 'Small',
            'btn-xs' => 'XSmall',
                ], ['class' => 'form-control'])
        ?>
    </div>

</div>
<div class="form-group row">

    <div class="col-md-12">
        <?php
        $attrname_ezf_id = 'options[ezf_id]';
        $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Form Visit'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>


</div>
<!--<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Print</h4></div>
    <div class="col-md-12">
<?php
//        $attrname_ezf_print = 'options[ezf_print]';
//        $value_ezf_print = isset($options['ezf_print']) ? $options['ezf_print'] : '""';
//echo Html::label(Yii::t('thaihis', 'Form'), $attrname_ezf_id, ['class' => 'control-label']);
//echo kartik\select2\Select2::widget([
//    'name' => $attrname_ezf_print,
//    'value' => $value_ezf_print,
//    'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_print', 'multiple' => true],
//    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
//    'pluginOptions' => [
//        'allowClear' => true,
//    ],
//]);
?>
    </div>
</div>-->

<div class="form-group row">
    <div class="col-md-12">
        <?php
        $attrname_template = 'options[template]';
        $value_template = isset($options['template']) ? $options['template'] : '';
        ?>
        <?= Html::label(Yii::t('thaihis', 'Template print'), $attrname_ezf_id, ['class' => 'control-label', 'style' => 'margin-right:2%']) . 
                Html::button('<i class="glyphicon glyphicon-plus"></i> Constant', ['class' => 'btn btn-success', 'id' => 'btn-add-constant' ,'style' => 'margin-right:2%']).
                Html::button('<i class="glyphicon glyphicon-eye-open"></i> Preview', ['class' => 'btn btn-info pull-right', 'id' => 'btn-preview']) ?>
        <div style="margin-top:2%;">
        <?=
        \appxq\sdii\widgets\FroalaEditorWidget::widget([
            'name' => $attrname_template,
            'value' => $value_template,
            //'id'=>'froala-editor',
            'options' => ['id' => 'custom_template'],
            'clientOptions' => [
                'zIndex' => 1,
                'height' => '300',
                //'theme' => 'gray', //optional: dark, red, gray, royal
                'language' => 'th',
            ]
        ]);
        ?>
        </div>
    </div>
    <!--    <div class="col-md-3">
    <?php // echo Html::label(Yii::t('thaihis', 'Constant'), $attrname_ezf_id, ['class' => 'control-label'])   ?>
            <div id="field_print_box">
    
            </div>
        </div>-->
    <div id="divPreviewPrint"></div>

</div>

<?php
$modalID = 'modal-sub';
$submodal = '<div id="' . $modalID . '" class="fade modal" role="dialog"><div class="modal-dialog modal-md"><div class="modal-content"></div></div></div>';
richardfan\widget\JSRegister::begin();
?>
<script>
    var hasMyModal = $('body').has('#<?= $modalID ?>').length;
    if (!hasMyModal) {
        $('.page-column').append('<?= $submodal ?>');
    }
    $('#<?= $modalID ?>').on('hidden.bs.modal', function (e) {
        $('#<?= $modalID ?> .modal-content').html('');
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });
    
    $('#btn-preview').click(function(){
        $.post('/patient/emr/preview-print-ce',{
            options:{
                template: $('#custom_template').val()
            }
        },function(data){
            $('#divPreviewPrint').html(data);
            // window.open('/patient/restful/print-report-cer?options='+data, '_blank');
        });
        
    });

    $('#btn-add-constant').click(function () {
        ezf_print($('#config_ezf_id').val());
    });

    $('#<?= $modalID ?>').on('click', '.attrname_field_print', function () {
        const val = $(this).attr('data-constant');
        $('#custom_template').froalaEditor('html.insert', val, true);
        $('#<?= $modalID ?>').modal('hide');
    });

    function ezf_print(ezf_id) {
        $('#<?= $modalID ?>').modal('show');
        $('#<?= $modalID ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $.post('<?= Url::to(['/queue/default/get-constant']) ?>', {ezf_id: ezf_id, name: 'attrname_field_print', id: 'config_constant', ezf_all_field: 1}
        ).done(function (result) {
            $('#<?= $modalID ?> .modal-content').html(result);
//            $('#field_print_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
        });
    }
</script>
<?php richardfan\widget\JSRegister::end(); ?>