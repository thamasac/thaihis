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
$ezf_id = isset($ezf_id) ? $ezf_id : 0;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByModule($ezm_id);
$itemSize = [
    'col-md-3'=>'25%',
    'col-md-6'=>'50%',
    'col-md-9'=>'75%',
    'col-md-12'=>'100%',
];

?>

<div class="panel panel-primary" id="content-widget<?= $key_index ?>">
    <div class="panel-body">
        <div class="form-group row">
            <?= Html::button("<i class='fa fa-trash'></i>", ['class' => 'btn btn-danger btn-sm pull-right', 'id' => 'btn-remove-widget'.$key_index, 'style' => 'margin-right:20px;']) ?>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <?= Html::label(Yii::t('ezform', 'Widget'), 'options[contents][' . $key_index . '][widget_id]') ?>
                <?php
                
                $attrname_widget_id = 'options[contents][' . $key_index . '][widget_id]';
                $value_widget_id = isset($val['widget_id']) ? $val['widget_id'] : '';
                
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_widget_id,
                    'value' => $value_widget_id,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Widgets'), 'id' => 'config_widget_id_' . $key_index, 'key_index' => $key_index],
                    'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
             <div class="col-md-3 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Content size'), 'options[contents][' . $key_index . '][widget_size]') ?>
                <?php
                $attrname_size = 'options[contents][' . $key_index . '][widget_size]';
                $value_size = isset($val['widget_size']) ? $val['widget_size'] : '';
                echo Html::dropDownList($attrname_size, $value_size, $itemSize,['class'=>'form-control']);
                ?>
            </div>
            
            <div class="col-md-3 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Widget order'), 'options[contents][' . $key_index . '][widget_order]') ?>
                <?php
                $attrname_widget_order = 'options[contents][' . $key_index . '][widget_order]';
                $value_widget_order = isset($val['widget_order']) ? $val['widget_order'] : '';
                echo Html::input('number',$attrname_widget_order, $value_widget_order,['class'=>'form-control']);
                ?>
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
    // JS script
    $(function () {

    });

    $('#btn-remove-widget<?= $key_index ?>').click(function () {
        $('#content-widget<?= $key_index ?>').remove();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>