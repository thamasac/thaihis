<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$ezf_id = isset($ezf_id) ? $ezf_id : 0;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

if (!isset($key_index))
    $key_index = \appxq\sdii\utils\SDUtility::getMillisecTime();


?>

<div class="panel panel-primary" id="content-column<?= $key_index ?>">
    <div class="panel-body">

        <div class="form-group row">
            <div class="col-md-3 ">
                <?= Html::label(Yii::t('ezform', 'Header Label'), 'header_label') ?>
                <?= Html::textInput('options[columns][' . $key_index . '][header_label]', isset($val['header_label']) ? $val['header_label'] : null, ['class' => 'form-control', 'id' => 'header_label' . $key_index]) ?>
            </div>
            <div class="col-md-6  sdbox-col">
                <?php
                $attrname_fields = 'options[columns][' . $key_index . '][fields]';
                $value_fields = isset($val['fields']) ? $val['fields'] : '';
                ?>
                <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
                <div id="field_box<?= $key_index ?>">

                </div>
            </div>
            <div class="col-md-2 sdbox-col">
                <?php
                $attrname_getvalue = 'options[columns][' . $key_index . '][get_value]';
                $value_getvalue = isset($val['get_value']) ? $val['get_value'] : null;
                ?>
                <?= Html::label(Yii::t('ezform', 'Get value'), $attrname_getvalue, ['class' => 'control-label']) ?><br/>
                <?= Html::checkbox($attrname_getvalue, $value_getvalue)?>
            </div>
            <div class="col-md-1 sdbox-col">
                <?= Html::label(Yii::t('ezform', '#'), 'header_label') ?>
                <?= Html::button("<i class='fa fa-trash'></i>", ['class' => 'btn btn-danger btn-sm pull-right', 'id' => 'btn-remove-column'.$key_index, 'style' => 'margin-right:20px;']) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-2 ">
                <?= Html::label(Yii::t('ezform', 'Width (px)'), 'width') ?>
                <?= Html::input('number','options[columns][' . $key_index . '][width]', isset($val['width']) ? $val['width'] : null, ['class' => 'form-control', 'id' => 'header_width' . $key_index]) ?>
            </div>
            <div class="col-md-2 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Align'), 'align') ?>
                <?= Html::dropDownList('options[columns][' . $key_index . '][align]', isset($val['align']) ? $val['align'] : null,['left'=>'Left','Right'=>'Right','center'=>'Center'], ['class' => 'form-control', 'id' => 'content_align' . $key_index]) ?>
            </div>
            <div class="col-md-7 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Template'), 'template') ?>
                <?= Html::textInput('options[columns][' . $key_index . '][template]', isset($val['template']) ? $val['template'] : null, ['class' => 'form-control', 'id' => 'header_label' . $key_index]) ?>
            </div>
            <div class="col-md-1 sdbox-col">
               
            </div>
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
    $(function () {
        fields<?= $key_index ?>($('#config_ref_form').val(), $('#config_ezf_id').val());
    });

    function fields<?= $key_index ?>(ezf_id, main_ezf_id) {
        var value = <?= json_encode($value_fields) ?>;
        var name = '<?= $attrname_fields ?>';
        var value_ref = <?= json_encode($value_ref) ?>;
        if (ezf_id) {
            value_ref = ezf_id;
        }

        $.post('<?= Url::to(['/thaihis/configs/get-fields-forms']) ?>', {ezf_id: value_ref, main_ezf_id: main_ezf_id, multiple: 1, name: name, value: value, id: 'config_field<?= $key_index ?>'}
        ).done(function (result) {
            $('#field_box<?= $key_index ?>').html(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
            console.log('server error');
        });
    }

    $('#config_ezf_id').change(function () {
        fields<?= $key_index ?>($('#config_ref_form').val(), $(this).val());
    });

    $('#ref_form_box').on('change', '#config_ref_form', function () {
        fields<?= $key_index ?>($(this).val(), $('#config_ezf_id').val());
    });
    
    $('#btn-remove-column<?=$key_index?>').click(function(){
        $('#content-column<?= $key_index ?>').remove();
    });
</script>
<?php
\richardfan\widget\JSRegister::end();
