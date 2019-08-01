<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_pms_widget_id = 'options[pms_widget_id]';
            $value_pms_widget_id = isset($options['pms_widget_id']) ? $options['pms_widget_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'PMS widget'), $attrname_pms_widget_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_pms_widget_id,
                'value' => $value_pms_widget_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_pms_widget_id'],
                'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        
        <div class="clearfix"></div>
    </div>

<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>