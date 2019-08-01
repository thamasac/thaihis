<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

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

if (!isset($ezm_id) || $ezm_id == '')
    $ezm_id = $model['ezm_id'];
$widget_id = $model['widget_id'];

$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByModule($ezm_id, $widget_id);

$columns = isset($options['columns']) ? $options['columns'] : null;

$contents = isset($options['contents']) ? $options['contents'] : [];
$conditions = isset($options['conditions']) ? $options['conditions'] : [];
$selects = isset($options['selects']) ? $options['selects'] : [];

$value_ref = isset($options['refform']) && is_array($options['refform']) ? $options['refform'] : null;
$value_left_ref = isset($options['left_refform']) && is_array($options['left_refform']) ? $options['left_refform'] : null;
$value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : null;
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->

<?php 
    echo $this->renderAjax('../template_form/config',[
        'itemsEzform'=>$itemsEzform,
        'options'=>$options,
    ]);
?>
<?php
echo $this->renderAjax('../template_form/_select_panel', [
    'selects' => $selects,
    'value_ref' => $value_ref,
    'value_ezf_id' => $value_ezf_id,
    'value_ref2'=>$value_left_ref,
]);
?>


<div class="form-group row" >
    <div class="col-md-6">
        <?php
        $attrname_date_field = 'options[date_field]';
        $value_date_field = isset($options['date_field']) ? $options['date_field'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Date field for search'), 'options[date_field]', ['class' => 'control-label']) ?>
        <div id="ref_date_field_box">

        </div>
    </div>
    <div class="col-md-6">
        <?php
        $attrname_search_field = 'options[search_field]';
        $value_search_field = isset($options['search_field']) && is_array($options['search_field']) ? $options['search_field'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields for search '), 'options[search_field]', ['class' => 'control-label']) ?>
        <div id="ref_search_field_box">

        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-12 " >
        <?php
        $attrname_serial = 'options[serial_column]';
        $value_serial = isset($options['serial_column']) ? $options['serial_column'] : null;

        echo Html::checkbox($attrname_serial, $value_serial, []) . ' ' . Html::label(Yii::t('thaihis', 'Serial number of column'));
        ?>
    </div>
</div>
<div class="form-group row">

    <div class="col-md-6 ">
        <?= Html::label(Yii::t('ezform', 'Widget for action'), 'options[widget_id]') ?>
        <?php
        $attrname_widget_id = 'options[widget_id]';
        $value_widget_id = isset($options['widget_id']) ? $options['widget_id'] : '';
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widgets'), 'id' => 'config_widget_id'],
            'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>
<div class="form-group row">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <div class="sdbox-col">
                <?= Html::label(Yii::t('thaihis', 'Custom column of grid')) ?>
                <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-column']) ?>
            </div>
        </div>
        <div class="panel-body">

            <div class="sdbox-col" id="display-column">
                <?php
                $value_merge = $value_ref;
                if (isset($columns) && is_array($columns)):
                    foreach ($columns as $key => $val):
                        $key_index = $key;
                        if (isset($value_left_ref) && is_array($value_left_ref)) {
                            $value_merge = array_merge($value_ref, $value_left_ref);
                        }
                        echo $this->renderAjax('_form', [
                            'key_index' => $key_index,
                            'value_ref' => $value_merge,
                            'value_ezf_id' => $value_ezf_id,
                            'val' => $val,
                        ]);
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>

<!--config end-->

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function () {
        searchFields($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        dateSearchFields($('#config_ref_form').val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
    });

    function searchFields(ezf_id, ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_search_field) ?>;
        var name = '<?= $attrname_search_field ?>';
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
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_search_field'}
        ).done(function (result) {
            $('#ref_search_field_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    function dateSearchFields(ezf_id, ezf_id2, main_ezf_id) {
        var value = <?= json_encode($value_date_field) ?>;
        var name = '<?= $attrname_date_field ?>';
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
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms2']) ?>', {ezf_id: value_merge, main_ezf_id: main_ezf_id, multiple: 0, name: name, type: '63', value: value, id: 'config_date_field'}
        ).done(function (result) {
            $('#ref_date_field_box').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }


    $('#config_ezf_id').change(function () {
        searchFields($('#config_ref_form').val(), $('#config_left_ref_form').val(), $(this).val());
        dateSearchFields($('#config_ref_form').val(), $('#config_left_ref_form').val(), $(this).val());
    });

    $('#ref_form_box').on('change', '#config_ref_form', function () {
        searchFields($(this).val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
        dateSearchFields($(this).val(), $('#config_left_ref_form').val(), $('#config_ezf_id').val());
    });

    $('#left_ref_form_box').on('change', '#config_left_ref_form', function () {
        searchFields($('#config_ref_form').val(), $(this).val(), $('#config_ezf_id').val());
        dateSearchFields($('#config_ref_form').val(), $(this).val(), $('#config_ezf_id').val());
    });


    $('#btn-add-column').on('click', function () {
        onLoadColumn('addNew');
    });

    function onLoadColumn(act) {
        var ezf_id = $('#config_ref_form').val();
        var main_ezf_id = $('#config_ezf_id').val();
        var value_ref = $('#config_ref_form').val();
        var value_ref2 = $('#config_left_ref_form').val();
        var div_content = $('#display-column');
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);

        var url = '<?= Url::to(['/thaihis/configs/add-newcolumn-grid']) ?>';
        $.get(url, {act: act, main_ezf_id: main_ezf_id, value_ref: value_merge}, function (result) {
            div_content.append(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>