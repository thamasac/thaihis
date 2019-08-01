<?php
use yii\helpers\Html;
use yii\helpers\Url;
if($error == 1){
    $text = Yii::t('graphconfig',  'SQL Not complete or error');
}else{
    $text = '';
    switch ($type){
    case 0 : $text = Yii::t('graphconfig', 'SQL Correct!');
        break;
    case 1 : 
    case 2 : 
    case 3 : 
            $text .= '<div class="col-md-6">
                        <div class="col-md-12"> '.Yii::t('graphconfig', 'Group variable').'</div>
                        <div class="col-md-12"> '.Html::dropDownList('forms-reporttypevariable', $val, $list, ['class'=>'form-control']).' </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12"> '.Yii::t('graphconfig', 'Value variable').'</div>
                        <div class="col-md-12"> '.Html::dropDownList('forms-reporttypeval', $val2, $list, ['class'=>'form-control']).'</div>
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
