<?php

use kartik\select2\Select2;
if(isset($name_data) && $name_data != ''){
    if(!$ezf_id == 0 && is_array($ezf_id)){
        $ezf_id = \appxq\sdii\utils\SDUtility::array2String($ezf_id);
    }
    echo \yii\helpers\Html::hiddenInput($name_data,$ezf_id);
}

if ($multiple) {
    echo Select2::widget([
        'id' => $id,
        'name' => $name,
        'value' => $value,
        'data' => $dataForm,
//        'maintainOrder' => true,
        'options' => ['placeholder' => Yii::t('ezform', 'Please select a form.'), 'multiple' => true, 'class' => 'form-ref'],
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
        'options' => ['placeholder' => Yii::t('ezform', 'Please select a form.'), 'class' => 'form-ref'],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
}