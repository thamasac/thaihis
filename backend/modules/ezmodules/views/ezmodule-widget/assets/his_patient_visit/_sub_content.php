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
$val = isset($subcontent[$sub_index]) ? $subcontent[$sub_index] : [];
?>

<div class="panel panel-default" id="content-subcontent<?= $sub_index ?>" >

    <div class="panel-body">
        <div class="form-group row">
            <div class="col-md-12 ">
                <?= Html::button("<i class='fa fa-trash'></i>", ['id' => 'btn-remove-subcontent' . $sub_index, 'class' => 'btn btn-danger pull-right', 'data-key_index' => $sub_index]) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 ">
                <?= Html::label(Yii::t('ezform', 'Content name <code>*</code>'), '') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][title]', isset($val['title']) ? $val['title'] : null, ['class' => 'form-control', 'id' => 'var_name_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
            <div class="col-md-4 sdbox-col">
                <?php
                $attrname_ezf_id = 'options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][ezf_id]';
                $value_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : null;
                ?>
                <?= Html::label(Yii::t('ezmodule', 'Forms <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_ezf_id,
                    'value' => $value_ezf_id,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id'. $sub_index, 'key_index' => $sub_index],
                    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>

            </div>
            <div class="col-md-4 sdbox-col">
                <?php
                $attrname_field = 'options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][field]';
                $value_field = isset($val['field']) ? $val['field'] : null;
                ?>
                <?= Html::label(Yii::t('ezform', 'Field Display <code>*</code>'), '', ['class' => 'control-label']) ?>
                <div id="ref_field_box<?= $sub_index ?>" key_index="<?= $sub_index ?>">

                </div>
            </div>
            <div class="col-md-1 sdbox-col ">
                <?= Html::label(Yii::t('ezform', 'Order'), '') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][suborder]', isset($val['suborder']) ? $val['suborder'] : null, ['class' => 'form-control', 'id' => 'suborder_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <?= Html::label(Yii::t('ezform', 'Target name'), '') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][target_name]', isset($val['target_name']) ? $val['target_name'] : null, ['class' => 'form-control', 'id' => 'target_name_input' . $sub_index, 'data-key_index' => $sub_index]) ?>
            </div>
            <div class="col-md-6 sdbox-col">
                <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][subcontent][' . $sub_index . '][not_require_data]', isset($val['not_require_data']) ? $val['not_require_data'] : 0, ['label' => 'Not require data of main box']) ?>
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
        var key_index = '<?= $sub_index ?>';
        field<?= $sub_index ?>($('#config_ezf_id'+key_index).val(),key_index);
    });
    
    $('#config_ezf_id<?=$sub_index?>').on('change',function(){
        var key_index = '<?= $sub_index ?>';
        field<?= $sub_index ?>($(this).val(),key_index);
    });

    function field<?= $sub_index ?>(ezf_id, key_index) {
        var renderDiv = $('#ref_field_box' + key_index);
        var ezf_id = ezf_id;
        var name = '<?=$attrname_field?>';
        var value = '<?=$value_field?>';

        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>', {ezf_id: ezf_id, multiple: 0, name: name, value: value, id: 'config_field' + key_index}
        ).done(function (result) {
            renderDiv.html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
            console.log('server error');
        });
    }

    $(document).on('click', '[id^=btn-remove-subcontent]', function () {
        var key_index = $(this).attr('data-key_index');
        var div_select = $('#content-subcontent' + key_index);
        div_select.remove();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>