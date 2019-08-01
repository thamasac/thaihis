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
$main_ezf_id = isset($main_ezf_id)?$main_ezf_id:null;
$ezf_id = isset($ezf_id)?$ezf_id:null;
$ezf_id2 = isset($ezf_id2)?$ezf_id2:null;
$operatorItem = ['=' => '=', '<>' => '<>', '<' => '<', '<=' => '<=', '>' => '>', '>=' => '>='];
$sec_name = 'options[conditions][' . $key_index . ']';
if (isset($key_index) && $key_index != '' && isset($sub_index) && $sub_index != '') {
    $sec_name = 'options[configs][' . $key_index . '][conditions][' . $sub_index . ']';
}

if(!isset($sub_index) || $sub_index=='')$sub_index=$key_index;
?>
<?php
//$val = isset($conditions) ? $conditions[$sub_index] : 0;
?>
<div class="form-group row" id="content-condition<?= $sub_index ?>">
    <div class="col-md-5">
        <?php
        $attrname_fields = $sec_name . '[field]';
        $value_fields = isset($val['field']) ? $val['field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field<?= $sub_index ?>_box" data-name="<?= $attrname_fields ?>" data-value="<?= $value_fields ?>">

        </div>
    </div>
    <div class="col-md-1 sdbox-col">
        <?php
        $attrname_operator = $sec_name . '[operator]';
        $value_operator = isset($val['operator']) ? $val['operator'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Operators'), $attrname_operator, ['class' => 'control-label']) ?>
        <?= Html::dropDownList($attrname_operator, $value_operator, $operatorItem, ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-5 sdbox-col">
        <?php
        $attrname_compare = $sec_name . '[compare]';
        $value_compare = isset($val['compare']) ? $val['compare'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Value'), $attrname_compare, ['class' => 'control-label']) ?>
        <?= Html::textInput($attrname_compare, $value_compare, ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-1 sdbox-col" style="margin-top:25px;">
        <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger', 'id' => 'btn-remove-condition' . $sub_index, 'data-key_index' => $sub_index]) ?>
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
        field<?= $sub_index ?>(key_index);
    });

    function field<?= $sub_index ?>(key_index) {
        var renderDiv = $('#ref_field' + key_index + '_box');
        var ezf_id = <?= json_encode($ezf_id) ?>;
        var ezf_id2 = <?= json_encode($ezf_id2) ?>;
        var main_ezf_id = '<?= $main_ezf_id ?>';
        var value = renderDiv.attr('data-value');
        var name = renderDiv.attr('data-name');
        
        var value_merge = ezf_id;
        if ($.isArray(ezf_id2) && $.isArray(ezf_id))
            value_merge = $.merge(ezf_id, ezf_id2);

        $.post('<?= Url::to(['/thaihis/patient-visit/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 0, name: name, value: value, id: 'config_field' + key_index}
        ).done(function (result) {
            renderDiv.html(result);
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