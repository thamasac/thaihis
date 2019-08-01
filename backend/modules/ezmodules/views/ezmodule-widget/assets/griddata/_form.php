<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$attrname_fields = [];
$value_fields = [];
$operatorItem = ['=' => '=', '<>' => '<>', '<' => '<', '<=' => '<=', '>' => '>', '>=' => '>='];

?>
<?php
if (is_array($conditions) && $act != 'addNew') :
    $val = isset($conditions) ? $conditions[$key_index] : 0;
    ?>
    <div class="form-group row" id="content-condition<?= $key_index ?>">
        <div class="col-md-5">
            <?php
            $attrname_fields[$key_index] = 'options[conditions][' . $key_index . '][field]';
            $value_fields[$key_index] = isset($val['field']) ? $val['field'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Field'), $attrname_fields[$key_index], ['class' => 'control-label']) ?>
            <div id="ref_field<?= $key_index ?>_box">

            </div>
        </div>
        <div class="col-md-1 sdbox-col">
            <?php
            $attrname_operator[$key_index] = 'options[conditions][' . $key_index . '][operator]';
            $value_operator[$key_index] = isset($val['operator']) ? $val['operator'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Operators'), $attrname_operator[$key_index], ['class' => 'control-label']) ?>
            <?= Html::dropDownList($attrname_operator[$key_index], $value_operator[$key_index], $operatorItem, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-5 sdbox-col">
            <?php
            $attrname_compare[$key_index] = 'options[conditions][' . $key_index . '][compare]';
            $value_compare[$key_index] = isset($val['compare']) ? $val['compare'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Value'), $attrname_compare[$key_index], ['class' => 'control-label']) ?>
            <?= Html::textInput($attrname_compare[$key_index], $value_compare[$key_index], ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-1 sdbox-col" style="margin-top:25px;">
            <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger', 'id' => 'btn-remove-condition' . $key_index, 'data-key_index' => $key_index]) ?>
        </div>
    </div>
    <?php
else:
    ?>
    <div class="form-group row" id="content-condition<?= $key_index ?>">
        <div class="col-md-5">
            <?php
            $attrname_fields[$key_index] = 'options[conditions][' . $key_index . '][field]';
            $value_fields[$key_index] = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Field'), $attrname_fields[$key_index], ['class' => 'control-label']) ?>
            <div id="ref_field<?= $key_index ?>_box">

            </div>
        </div>
        <div class="col-md-1 sdbox-col">
            <?php
            $attrname_operator[$key_index] = 'options[conditions][' . $key_index . '][operator]';
            $value_operator[$key_index] = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Operators'), $attrname_operator[$key_index], ['class' => 'control-label']) ?>
            <?= Html::dropDownList($attrname_operator[$key_index], $value_operator[$key_index], $operatorItem, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-5 sdbox-col">
            <?php
            $attrname_compare[$key_index] = 'options[conditions][' . $key_index . '][compare]';
            $value_compare[$key_index] = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Field'), $attrname_compare[$key_index], ['class' => 'control-label']) ?>
            <?= Html::textInput($attrname_compare[$key_index], $value_compare[$key_index], ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-1 sdbox-col" style="margin-top:25px;">
            <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger', 'id' => 'btn-remove-condition' . $key_index, 'data-key_index' => $key_index]) ?>
        </div>
    </div>
<?php
endif;
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $(function () {
        var key_index = '<?= $key_index ?>';
        field(key_index);
    });

    function field(key_index) {
        var ezf_id = <?= json_encode($ezf_id) ?>;
        var main_ezf_id = '<?= $main_ezf_id ?>';
        var value = <?= json_encode($value_fields) ?>;

        var name = <?= json_encode($attrname_fields) ?>;

        $.post('<?= Url::to(['/thaihis/patient-visit/get-fields-forms2']) ?>', {ezf_id: ezf_id, main_ezf_id: main_ezf_id, multiple: 0, name: name[key_index], value: value[key_index], id: 'config_field' + key_index}
        ).done(function (result) {
            $('#ref_field' + key_index + '_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
            console.log('server error');
        });
    }

    $(document).on('click', '[id^=btn-remove-condition]', function () {
        var key_index = $(this).attr('data-key_index');
        var div_condition = $('#content-condition' + key_index);
        div_condition.remove();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>