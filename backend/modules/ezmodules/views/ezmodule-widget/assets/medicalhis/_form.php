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
$ezf_id = isset($value_ezf_id) ? $value_ezf_id : 0;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
$conditions = isset($val['conditions']) ? $val['conditions'] : null;
if (!isset($key_index))
    $key_index = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>

<div class="panel panel-primary" id="content-medical<?= $key_index ?>">
    <div class="panel-body">
        <div class="form-group row">
            <?= Html::button("<i class='fa fa-trash'></i>", ['class' => 'btn btn-danger btn-sm pull-right', 'id' => 'btn-remove-medical' . $key_index, 'style' => 'margin-right:20px;']) ?>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <?php
                $attrname_con_ezf_id = 'options[contents][' . $key_index . '][ezf_id]';
                $value_con_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : 0;
                ?>
                <?= Html::label(Yii::t('thaihis', 'Form'), $attrname_con_ezf_id, ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_con_ezf_id,
                    'value' => $value_con_ezf_id,
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
                $attrname_con_ref = 'options[contents][' . $key_index . '][refform]';
                $value_con_ref = isset($val['refform']) && is_array($val['refform']) ? $val['refform'] : null;
                ?>
                <?= Html::label(Yii::t('ezform', 'Reference Form <code>*</code>'), '', ['class' => 'control-label']) ?>
                <div id="ref_form_box<?= $key_index ?>"  >

                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-12 ">
                <?php
                $attrname_fields = 'options[contents][' . $key_index . '][fields]';
                $value_fields = isset($val['fields']) ? $val['fields'] : '';
                ?>
                <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
                <div id="field_box<?= $key_index ?>">

                </div>
            </div>
        </div>
        <?php
        echo $this->renderAjax('../template_form/_condition_panel', [
            'conditions' => $conditions,
            'key_index' => $key_index,
            'value_ref' => $value_con_ref,
            'value_ezf_id' => $value_con_ezf_id,
        ]);
        ?>

        <div class="form-group">
            <?= Html::label(Yii::t('ezform', 'Template Box'), 'options[contents][' . $key_index . '][template_box]', ['class' => 'control-label']) ?>
            <?= Html::textarea('options[contents][' . $key_index . '][template_box]', isset($val['template_box']) ? $val['template_box'] : '', ['class' => 'form-control', 'row' => 3]) ?>
        </div>
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
        fields<?= $key_index ?>(<?= json_encode($value_con_ref) ?>, '<?= $value_con_ezf_id ?>');
        form_ref<?= $key_index ?>('<?= $value_con_ezf_id ?>', <?= json_encode($value_con_ref) ?>);
    });

    $('#config_ezf_id<?= $key_index ?>').change(function () {
        fields<?= $key_index ?>($('#config_ref_form<?= $key_index ?>').val(), $(this).val());
        form_ref<?= $key_index ?>($(this).val(), $('#config_ref_form<?= $key_index ?>').val());
    });

    $('#ref_form_box<?= $key_index ?>').on('change', '#config_ref_form<?= $key_index ?>', function () {
        fields<?= $key_index ?>($(this).val(), $('#config_ezf_id<?= $key_index ?>').val());
        form_ref<?= $key_index ?>($('#config_ezf_id<?= $key_index ?>').val(), $(this).val());
    });

    $('#btn-remove-content<?= $key_index ?>').click(function () {
        $('#content-medical<?= $key_index ?>').remove();
    });

    function form_ref<?= $key_index ?>(ezf_id, value_ref) {
        var value = <?= json_encode($value_con_ref) ?>;
        var name = <?= json_encode($attrname_con_ref) ?>;
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

    function fields<?= $key_index ?>(ezf_id, main_ezf_id) {
        var value = <?= json_encode($value_fields) ?>;
        var name = '<?= $attrname_fields ?>';
        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms']) ?>', {ezf_id: ezf_id, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_fields<?= $key_index ?>'}
        ).done(function (result) {
            $('#field_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    $('#btn-remove-medical<?= $key_index ?>').click(function () {
        $('#content-medical<?= $key_index ?>').remove();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>