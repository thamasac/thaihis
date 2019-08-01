<?php
use kartik\select2\Select2;

$select = isset($placeholder) && $placeholder !=''?$placeholder:Yii::t('ezform', 'Select field ...');
if($multiple){
    echo Select2::widget([
        'id'=>$id,
        'name' => $name,
        'value'=>$value,
        'data' => $data,
        'maintainOrder'=>true,
        'options' => ['placeholder' => $select, 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'tokenSeparators' => [',', ' '],
        ]
    ]);
} else {
    echo Select2::widget([
        'id'=>$id,
        'name' => $name,
        'value'=>$value,
        'data' => $data,
        'options' => ['placeholder' => $select],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
}

