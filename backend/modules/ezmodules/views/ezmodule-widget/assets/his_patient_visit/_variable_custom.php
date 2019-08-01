<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzformWidget;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
$sub_index = isset($sub_index) ? $sub_index : appxq\sdii\utils\SDUtility::getMillisecTime();
$val = isset($variables[$sub_index]) ? $variables[$sub_index] : [];
?>

<div class="panel panel-default" id="content-variable<?= $sub_index ?>" >

    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-12 ">
                <?= Html::button("<i class='fa fa-trash'></i>", ['id' => 'btn-remove-variable' . $sub_index, 'class' => 'btn btn-danger pull-right', 'data-key_index' => $sub_index]) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 ">
                <?= Html::label(Yii::t('ezform', 'Variable name <code>*</code>'), '') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][variables][' . $sub_index . '][var_name]', isset($val['var_name'])?$val['var_name']:null, ['class' => 'form-control', 'id' => 'var_name_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
            <div class="col-md-3 sdbox-col">
                <?php
                $attrname_icon = 'options[tabs][' . $key_index . '][variables][' . $sub_index . '][icon]';
                $value_icon = isset($val['icon']) ? $val['icon'] : null;
                ?>
                <?= Html::label(Yii::t('ezform', 'Icon'), $attrname_icon, ['class' => 'control-label']) ?>
                <?=
                dominus77\iconpicker\IconPicker::widget([
                    'name' => $attrname_icon,
                    'value' => $value_icon,
                    'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon' . $sub_index],
                    'clientOptions' => [
                        'hideOnSelect' => true,
                    ]
                ])
                ?>

            </div>
            <div class="col-md-3 sdbox-col">
                <?php
                $themes = ['btn-default' => 'btn-default', 'btn-primary' => 'btn-primary', 'btn-success' => 'btn-success', 'btn-info' => 'btn-info', 'btn-danger' => 'btn-danger', 'btn-warning' => 'btn-warning'];
                ?>
                <?= Html::label(Yii::t('ezform', 'Themes'), '') ?>
                <?= Html::dropDownList('options[tabs][' . $key_index . '][variables][' . $sub_index . '][themes]', isset($val['themes']) ? $val['themes'] : 'btn-default', $themes, ['class' => 'form-control', 'id' => 'themes_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
            <div class="col-md-3 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Label'), '') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][variables][' . $sub_index . '][label]', isset($val['label']) ? $val['label'] : '', ['class' => 'form-control', 'id' => 'label_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4 ">
                <?php
                $attrname_ezf_id = 'options[tabs][' . $key_index . '][variables][' . $sub_index . '][ezf_id]';
                $value_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : null;
                ?>
                <?= Html::label(Yii::t('ezmodule', 'Forms <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_ezf_id,
                    'value' => $value_ezf_id,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id' . $sub_index, 'key_index' => $sub_index],
                    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-4 sdbox-col">
                <?php
                $require = ['none_target'=>'None target','visit' => 'Visit', 'patient' => 'Patient'];
                ?>
                <?= Html::label(Yii::t('ezform', 'Target require'), '') ?>
                <?= Html::dropDownList('options[tabs][' . $key_index . '][variables][' . $sub_index . '][require]', isset($val['require']) ? $val['require'] : 'btn-default', $require, ['class' => 'form-control', 'id' => 'require_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
            <div class="col-md-4 sdbox-col" >
                <?php
                $attrname_action = 'options[tabs][' . $key_index . '][variables][' . $sub_index . '][action]';
                $value_action = isset($val['action']) ? $val['action'] : ['create', 'update', 'delete', 'view', 'search'];
                ?>
                <?= Html::label(Yii::t('ezform', 'Action'), 'options[tabs][' . $key_index . '][variables][' . $sub_index . '][action]', ['class' => 'control-label']) ?>
                <?=
                kartik\select2\Select2::widget([
                    'id' => 'config_action' . $sub_index,
                    'name' => $attrname_action,
                    'value' => $value_action,
                    'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('action'),
//                    'maintainOrder' => true,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select action ...'), 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                    ]
                ]);
                ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6 ">
                <?= Html::label(Yii::t('ezform', 'Link to other action'), 'options[tabs][' . $key_index . '][variables][' . $sub_index . '][url_target_link]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][variables][' . $sub_index . '][url_target_link]',isset($val['url_target_link']) ? $val['url_target_link'] : '',['class'=>'form-control'])?>
            </div>
            <div class="col-md-3 sdbox-col ">
                
                <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][variables][' . $sub_index . '][popup]',isset($val['popup']) ? $val['popup'] : 0,['label'=>Yii::t('ezform', 'Popup enabled')])?>
            </div>
            <div class="col-md-3 sdbox-col">
                <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][variables][' . $sub_index . '][readonly]', isset($val['readonly']) ? $val['readonly'] : 0, ['label' => 'Readonly']) ?>
            </div>
        </div>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $(function () {
        var key_index  = '<?= $sub_index ?>';
        //field<?= $sub_index ?>($('#config_ezf_id'+key_index).val(),key_index);
    });

    function field<?= $sub_index ?>(ezf_id,key_index) {
        var renderDiv = $('#ref_field_init_box_' + key_index);
        var main_ezf_id = ezf_id;
        var value = renderDiv.attr('data-value');
        var name = renderDiv.attr('data-name');

        $.post('<?= Url::to(['/thaihis/patient-visit/get-fields-forms2']) ?>', {main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_header_select_field' + key_index}
        ).done(function (result) {
            renderDiv.html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
            console.log('server error');
        });
    }

    $(document).on('click', '#btn-remove-variable<?=$sub_index?>', function () {s
        var key_index = $(this).attr('data-key_index');
        var div_select = $('#content-variable' + key_index);
        div_select.remove();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>