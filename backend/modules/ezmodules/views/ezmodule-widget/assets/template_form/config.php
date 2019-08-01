<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$key_index = isset($key_index) ? $key_index : '';
$conditions = isset($options['conditions']) ? $options['conditions'] : null;
$selects = isset($options['selects']) ? $options['selects'] : null;
$summarys = isset($options['summarys']) ? $options['summarys'] : null;

?>
<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_ezf_id = 'options[ezf_id]';
        $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Form <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id' . $key_index],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_visit_date = 'options[visit_date]';
        $value_visit_date = isset($options['visit_date']) ? $options['visit_date'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field visit date'), $attrname_visit_date, ['class' => 'control-label']) ?>
        <div id="visit_date_box<?= $key_index ?>">

        </div>
    </div>
</div>
<div class="form-group row">

    <div class="col-md-12">
        <?php
        $attrname_ref = 'options[refform]';
        $value_ref = isset($options['refform']) && is_array($options['refform']) ? $options['refform'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form '), '', ['class' => 'control-label']) ?>
        <div id="ref_form_box<?= $key_index ?>"  >

        </div>
    </div>
</div>
<div class="form-group row">

    <div class="col-md-12">
        <?php
        $attrname_left_ref = 'options[left_refform]';
        $value_left_ref = isset($options['left_refform']) && is_array($options['left_refform']) ? $options['left_refform'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form (Left join only)'), '', ['class' => 'control-label']) ?>
        <div id="left_ref_form_box<?= $key_index ?>"  >

        </div>
    </div>
</div>
<div class="form-group row" >
    <div class="col-md-12">
        <?php
        $attrname_fields = 'options[fields]';
        $value_fields = isset($options['fields']) && is_array($options['fields']) ? $options['fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields <code>*</code>'), 'options[fields]', ['class' => 'control-label']) ?>
        <div id="ref_field_box<?= $key_index ?>">

        </div>
    </div>
</div>
<div class="form-group row" >
    <div class="col-md-6">
        <?php
        $attrname_group_concat = 'options[group_concat]';
        $value_group_concat = isset($options['group_concat']) && is_array($options['group_concat']) ? $options['group_concat'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Group concat fields '), 'options[group_concat]', ['class' => 'control-label']) ?>
        <div id="ref_group_concat_box<?= $key_index ?>">

        </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_group_fields = 'options[group_fields]';
        $value_group_fields = isset($options['group_fields']) && is_array($options['group_fields']) ? $options['group_fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Group Fields '), 'options[group_fields]', ['class' => 'control-label']) ?>
        <div id="ref_group_field_box<?= $key_index ?>">

        </div>
    </div>

</div>
<div class="form-group row" >
    <div class="col-md-6 ">
        <?php
        $attrname_order_fields = 'options[order_fields]';
        $value_order_fields = isset($options['order_fields']) && is_array($options['order_fields']) ? $options['order_fields'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Order Fields '), 'options[order_fields]', ['class' => 'control-label']) ?>
        <div id="ref_order_fields_box<?= $key_index ?>">

        </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Order type'), 'options[order_fields]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[order_type]', isset($options['order_type'])?$options['order_type']:'', ['ASC' => 'ASC', 'DESC' => 'DESC'], ['class' => 'form-control']) ?>
    </div>
</div>

<?php

echo $this->renderAjax('_condition_panel', [
    'conditions' => $conditions,
    'value_ref'=>$value_ref,
    'value_left_ref'=>$value_left_ref,
    'value_ezf_id'=>$value_ezf_id,
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $(function () {
        form_ref<?= $key_index ?>($('#config_ezf_id').val(), $('#config_ref_form').val());
        form_left_ref($('#config_ezf_id').val(), $('#config_ref_form').val(), $('#config_left_ref_form').val());
        fields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        visitDateField<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        groupFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        orderFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        groupConcatFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
    });

    function form_ref<?= $key_index ?>(ezf_id, value_ref) {
        var value = <?= json_encode($value_ref) ?>;
        var name = <?= json_encode($attrname_ref) ?>;
        if (value_ref) {
            value = value_ref;
        }
        $.post('<?= Url::to(['/thaihis/configs/get-form-ref2']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value_ref: value, id: 'config_ref_form<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_form_box<?= $key_index ?>').html(result);
            //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
            //fields($('#ezf_target_id').val());
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }
    function form_left_ref<?= $key_index ?>(ezf_id, value_ref, value_ref2) {
        var value = <?= json_encode($value_left_ref) ?>;
        var name = <?= json_encode($attrname_left_ref) ?>;
        if (value_ref2) {
            value = value_ref2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
        $.post('<?= Url::to(['/thaihis/configs/get-form-ref2']) ?>', {ezf_id: ezf_id, multiple: 1, name: name, value_ref: value, value_merge: value_merge, id: 'config_left_ref_form<?= $key_index ?>'}
        ).done(function (result) {
            $('#left_ref_form_box<?= $key_index ?>').html(result);
            //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
            //fields($('#ezf_target_id').val());
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    function fields<?= $key_index ?>(ezf_id,ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_fields) ?>;
        var name = '<?= $attrname_fields ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2 = <?= json_encode($value_left_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_field_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    $('#config_ezf_id<?= $key_index ?>').change(function () {
        visitDateField<?= $key_index ?>($(this).val());
        form_ref<?= $key_index ?>($(this).val());
        fields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form<?= $key_index ?>').val(), $(this).val());
        visitDateField<?= $key_index ?>($('#config_ref_form').val(), $(this).val());
        groupFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form<?= $key_index ?>').val(), $(this).val());
        orderFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form<?= $key_index ?>').val(), $(this).val());
        groupConcatFields<?= $key_index ?>($('#config_ref_form').val(), $('#config_left_ref_form<?= $key_index ?>').val(), $(this).val());
    });

    $('#ref_form_box<?= $key_index ?>').on('change', '#config_ref_form<?= $key_index ?>', function () {
        visitDateField<?= $key_index ?>($('#config_ezf_id').val());
        fields<?= $key_index ?>($(this).val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        visitDateField<?= $key_index ?>($(this).val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        groupFields<?= $key_index ?>($(this).val(), $('#config_left_ref_form<?= $key_index ?>').val(), $('#config_ezf_id<?= $key_index ?>').val());
        orderFields<?= $key_index ?>($(this).val(), $('#config_left_ref_form<?= $key_index ?>').val(), $('#config_ezf_id<?= $key_index ?>').val());
        groupConcatFields<?= $key_index ?>($(this).val(), $('#config_left_ref_form<?= $key_index ?>').val(), $('#config_ezf_id<?= $key_index ?>').val());
    });

    $('#left_ref_form_box<?= $key_index ?>').on('change', '#config_left_ref_form<?= $key_index ?>', function () {
        fields<?= $key_index ?>($('#config_ref_form<?= $key_index ?>').val(), $(this).val(), $('#config_ezf_id<?= $key_index ?>').val());
        groupFields<?= $key_index ?>($('#config_ref_form<?= $key_index ?>').val(), $(this).val(), $('#config_ezf_id<?= $key_index ?>').val());
        groupConcatFields<?= $key_index ?>($('#config_ref_form<?= $key_index ?>').val(), $(this).val(), $('#config_ezf_id<?= $key_index ?>').val());
        form_left_ref<?= $key_index ?>($('#config_ezf_id<?= $key_index ?>').val(), $('#config_ref_form<?= $key_index ?>').val(), $(this).val());
    });

    function visitDateField<?= $key_index ?>(ezf_id,ezf_id2, main_ezf_id) {
        var value = '<?= $value_visit_date ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2= <?= json_encode($value_left_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 0, name: '<?= $attrname_visit_date ?>', value: value, id: 'config_field_bdate<?= $key_index ?>'}
        ).done(function (result) {
            $('#visit_date_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });

    }

    function groupFields<?= $key_index ?>(ezf_id, ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_group_fields) ?>;
        var name = '<?= $attrname_group_fields ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2 = <?= json_encode($value_left_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_group_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_group_field_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }
    function orderFields<?= $key_index ?>(ezf_id, ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_order_fields) ?>;
        var name = '<?= $attrname_order_fields ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2 = <?= json_encode($value_left_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_order_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#ref_order_fields_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }
    function groupConcatFields(ezf_id, ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_group_concat) ?>;
        var name = '<?= $attrname_group_concat ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        var value_ref2 = <?= json_encode($value_left_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }

        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_group_concat'}
        ).done(function (result) {
            $('#ref_group_concat_box<?= $key_index ?>').html(result);
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