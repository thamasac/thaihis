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
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Form</h4></div>
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

    <div class="col-md-6">
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

    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_ref = 'options[refform]';
        $value_ref = isset($options['refform']) ? $options['refform'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Form Visit Transection'), $attrname_ref, ['class' => 'control-label']) ?>
        <div id="ref_form_box">

        </div>
    </div>
</div>

<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    form_ref($('#config_ezf_id').val());
    
    $('#config_ezf_id').change(function(){
        form_ref($(this).val(),0);
    });
    function form_ref(ezf_id, main_ezf_id) {
        var value = '<?=$value_ref ?>';
        $.post('<?= Url::to(['/thaihis/patient-visit/get-form-ref']) ?>', {ezf_id: ezf_id, multiple: 0, name: '<?= $attrname_ref ?>', value_ref: value, id: 'config_ref_form'}
        ).done(function (result) {
            $('#ref_form_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"'); ?>
        });
    }
</script>
<?php richardfan\widget\JSRegister::end(); ?>