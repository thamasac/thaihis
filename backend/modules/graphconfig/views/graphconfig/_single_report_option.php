<?php
use yii\helpers\Html;
use yii\helpers\Url;
if($error == 1){
    //$text = Yii::t('graphconfig',  'SQL Not complete or error');
    $text = $errortext;
}else{
    $text = '';
    switch ($type){
        case 0 : $text = Yii::t('graphconfig', 'SQL Correct!');
            break;
        case 1 :
        case 2 :
        case 3 :
            $text .= '<div class="col-md-6">
                         '.Html::label(Yii::t('graphconfig', 'Group variable'), 'options[reporttypevariable]', ['class' => 'control-label']).'
                         '.Html::dropDownList('options[reporttypevariable]', $val, $list, ['class'=>'form-control']).'
                    </div>
                    <div class="col-md-6">
                        '.Html::label(Yii::t('graphconfig', 'Value variable'), 'options[reporttypeval]', ['class' => 'control-label']).'
                        '.Html::dropDownList('options[reporttypeval]', $val2, $list, ['class'=>'form-control']).'
                    </div>' ;
            break;
        default: $text = Yii::t('graphconfig', 'SQL Correct!');
            break;

    }

}
?>
<div class="col-md-12">
    <?= $text ?>
</div>
