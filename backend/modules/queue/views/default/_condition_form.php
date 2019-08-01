<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use appxq\sdii\utils\SDUtility;

$id = SDUtility::getMillisecTime();
$itemCondition = [
    '>' => 'มากกว่า',
    '<' => 'น้อยกว่า',
    '>=' => 'มากกว่าหรือเท่ากับ',
    '<=' => 'น้อยกว่าหรือเท่ากับ',
    '=' => 'เท่ากับ',
    '!=' => 'ไม่เท่ากับ'
];
?>

<div class="col-md-12 divMainCondition" style="margin-top:2%">

    <div class="col-md-2 ">
        <?php
        echo Select2::widget([
            'id' => 'select-value-con-' . SDUtility::getMillisecTime(),
            'name' => 'options[condition][' . $id . '][condition]',
            'value' => $condition_con,
            'data' => ['and' => 'AND' , 'or' => 'OR'],
            'hideSearch' => true,
//            'options' => ['placeholder' => Yii::t('ezform', 'Select condition ...')],
//            'pluginOptions' => [
////                'allowClear' => true,
//            ]
        ]);
        ?>
    </div>

    <div class="col-md-4 sdbox-col">
        <?php
        echo Select2::widget([
            'id' => 'select-value-field-' . SDUtility::getMillisecTime(),
            'name' => 'options[condition][' . $id . '][field]',
            'value' => $condition_field,
            'data' => $dataForm,
            'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ]);
        ?>
    </div>
    <div class="col-md-2 sdbox-col">
        <?php
        echo Select2::widget([
            'id' => 'select-value-ope-' . SDUtility::getMillisecTime(),
            'name' => 'options[condition][' . $id . '][operator]',
            'value' => $condition_con,
            'data' => $itemCondition,
            'hideSearch' => true,
            'options' => ['placeholder' => Yii::t('ezform', 'Select condition ...')],
            'pluginOptions' => [
                'allowClear' => true,

            ]
        ]);
 ?>
    </div>

    <div class="col-md-3 sdbox-col">
        <?php
        if(empty($type)){
            ?>
            <div class="input-group">
                <?=Html::textInput('options[condition][' . $id . '][value]', $condition_value, ['class' => 'form-control','id'=>'val-con-'.$id])?>
                <span class="input-group-btn">
                    <?=Html::button(Yii::t('queue','Constant'), ['class'=>'btn btn-success btn-condition-constant','data-input-id'=>'val-con-'.$id])?>
                </span>
            </div>
        <?php
//            echo Html::textInput('options[condition][' . $id . '][value]', $condition_value, ['class' => 'form-control']);
        }else{
            echo Html::hiddenInput('options[condition][' . $id . '][value]', 'NOW()');
            echo Html::textInput('', 'วันนี้', ['class' => 'form-control','disabled' => true]);
        }

        ?>
    </div>

    <div class="col-md-1 sdbox-col">
        <?php echo Html::tag('div', '<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove-condition']) ?>
    </div>

</div>

