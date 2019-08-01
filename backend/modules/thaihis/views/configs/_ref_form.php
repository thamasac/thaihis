<?php

use kartik\select2\Select2;

if ($multiple) {
    echo Select2::widget([
        'id' => $id,
        'name' => $name,
        'value' => $value,
        'data' => $dataForm,
//        'maintainOrder' => true,
        'options' => ['placeholder' => Yii::t('ezform', 'Please select a form.'), 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'tokenSeparators' => [',', ' '],
        ]
    ]);
} else {
    echo Select2::widget([
        'id' => $id,
        'name' => $name,
        'value' => $value,
        'data' => $dataForm,
        'options' => ['placeholder' => Yii::t('ezform', 'Please select a form.')],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
}