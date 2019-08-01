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
$id = appxq\sdii\utils\SDUtility::getMillisecTime();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="row">
    <div class='col-md-3'>
        <label>Button name</label>
        <?php 
            echo Html::textInput('options[btn_name]', isset($options['btn_name']) ? $options['btn_name'] : '', ['class'=>'form-control']);
        ?>
    </div>
    <div class="col-md-5">
        <label>Button type</label>
        <?php 
            echo Html::radioList('options[btn_type]', isset($options['btn_type']) ? $options['btn_type'] : '', [
                'primary' => 'Blue',
                'success' => 'Green',
                'info' => 'Sky blue',
                'warning' => 'Orange',
                'danger' => 'Red'
                ]);
            ?>
    </div>
    <div class="col-md-4">
        <label>Position</label>
        <?php 
            echo Html::radioList('options[position_type]', isset($options['position_type']) ? $options['position_type'] : '', [
                    'pull-left' => 'Left',
                    'pull-right' => 'Right',
                    '' => 'Customized', 
                ]);
            ?>
    </div>
    <div class="clearfix"></div><hr/>
    <div class="col-md-4">
        <label>Button size</label>
        <?php 
            echo Html::radioList('options[position_size]', isset($options['position_size']) ? $options['position_size'] : 'btn-md', [
                    'btn-lg' => 'Large',
                    'btn-md' => 'Medium',
                    'btn-sm' => 'Small',
                    'btn-xs' => 'XSmall', 
                ]);
            ?>
    </div>
    <div class="col-md-3">
        <label>Block Level Buttons</label>
        <?php 
            echo Html::radioList('options[btn_block]', isset($options['btn_block']) ? $options['btn_block'] : '', [
                    'btn-block' => 'TRUE',
                    '' => 'FALSE',
                ]);
            ?>
    </div>
    <div class="col-md-3">
        <label><?= Yii::t('chanpan', 'Icon') ?></label>
        <?=
        dominus77\iconpicker\IconPicker::widget([
            'name' => "options[icon]",
            'value' => isset($options['icon']) ? $options['icon'] : 'fa-check',
            'options' => ['class' => 'sicon-input form-control', 'id' => "iconpicker_" . $id],
            'clientOptions' => [
                'hideOnSelect' => true,
            ]
        ])
        ?>
    </div>
    
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