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
$val = isset($subquery[$sub_index]) ? $subquery[$sub_index] : [];
?>
<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_ezf_id = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][ezf_id]';
        $value_ezf_id = isset($subquery['ezf_id']) ? $subquery['ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Form <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id' . $sub_index],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>

<div class="form-group row" >
    <div class="col-md-12">
        <?php
        $attrname_fields = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][fields]';
        $value_fields = isset($subquery['fields']) && is_array($subquery['fields']) ? $tab[$sub_index]['fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields <code>*</code>'), 'options[fields]', ['class' => 'control-label']) ?>
        <div id="ref_field_box<?= $sub_index ?>">

        </div>
    </div>
</div>
<div class="form-group row" >
    <div class="col-md-6">
        <?php
        $attrname_con_fields = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][condition_field]';
        $value_con_fields = isset($subquery['condition_fields']) && is_array($subquery['condition_field']) ? $tab[$sub_index]['condition_field'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Condition field <code>*</code>'), 'options[condition_field]', ['class' => 'control-label']) ?>
        <div id="ref_con_field_box<?= $sub_index ?>">

        </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_con_val = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][condition_val]';
        $value_con_val = isset($subquery['condition_val']) && is_array($subquery['condition_val']) ? $tab[$sub_index]['condition_val'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Condition value '), 'options[condition_val]', ['class' => 'control-label']) ?>
        <?= Html::textInput($attrname_con_fields, $value_con_val,['class'=>'form-control'])?>
    </div>
</div>
<div class="form-group row" >
    <div class="col-md-4 ">
        <?php
        $attrname_group_fields = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][group_fields]';
        $value_group_fields = isset($subquery['group_fields']) && is_array($subquery['group_fields']) ? $tab[$sub_index]['group_fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Group Fields '), 'options[group_fields]', ['class' => 'control-label']) ?>
        <div id="ref_group_field_box<?= $sub_index ?>">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_order_fields = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][order_fields]';
        $value_order_fields = isset($subquery['order_fields']) && is_array($subquery['order_fields']) ? $subquery['order_fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Order Fields '), 'options[order_fields]', ['class' => 'control-label']) ?>
        <div id="ref_order_fields_box<?= $sub_index ?>">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php 
        $attrname_order_type = 'options[tabs][' . $key_index . '][subquery][' . $sub_index . '][order_type]';
        $value_order_type = isset($subquery['order_type']) && is_array($subquery['order_type']) ? $subquery['order_type'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Order type'), 'options[order_fields]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList($attrname_order_type, $value_order_type, ['ASC' => 'ASC', 'DESC' => 'DESC'], ['class' => 'form-control']) ?>
    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $(function () {
        fields<?= $sub_index ?>( $('#config_ezf_id').val());
        conditionFields<?= $sub_index ?>($('#config_ezf_id').val());
        groupFields<?= $sub_index ?>( $('#config_ezf_id').val());
        orderFields<?= $sub_index ?>($('#config_ezf_id').val());
    });

    function fields<?= $sub_index ?>(ezf_id) {
        var value = <?= json_encode($value_fields) ?>;
        var name = '<?= $attrname_fields ?>';
        
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value: value, id: 'config_fields<?= $sub_index ?>'}
        ).done(function (result) {
            $('#ref_field_box<?= $sub_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    $('#config_ezf_id<?= $sub_index ?>').change(function () {
        fields<?= $sub_index ?>($(this).val());
        conditionFields<?= $sub_index ?>($(this).val());
        groupFields<?= $sub_index ?>($(this).val());
        orderFields<?= $sub_index ?>($(this).val());
    });
    
    function conditionFields<?= $sub_index ?>(ezf_id) {
        var value = <?= json_encode($value_con_fields) ?>;
        var name = '<?= $attrname_con_fields ?>';

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value: value, id: 'config_con_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_con_field_box<?= $sub_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    function groupFields<?= $sub_index ?>(ezf_id) {
        var value = <?= json_encode($value_group_fields) ?>;
        var name = '<?= $attrname_group_fields ?>';

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value: value, id: 'config_group_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_group_field_box<?= $sub_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }
    function orderFields<?= $sub_index ?>(ezf_id) {
        var value = <?= json_encode($value_order_fields) ?>;
        var name = '<?= $attrname_order_fields ?>';


        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value: value, id: 'config_order_fields<?= $sub_index ?>'}
        ).done(function (result) {
            $('#ref_order_fields_box<?= $sub_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    function getMilisecTime() {
        var d = new Date();
        var key_index = d.getFullYear() + '' + d.getMonth() + '' + d.getDate() + '' + d.getHours() + '' + d.getMinutes() + '' + d.getSeconds() + '' + d.getMilliseconds();
        return key_index;
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>