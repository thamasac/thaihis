<?php 
use yii\helpers\Html;
?>
<?php 
$newKey = appxq\sdii\utils\SDUtility::getMillisecTime();
foreach ($options['procedure_name'] as $key => $val){?>
<div class="form-group row">
    <div class="col-md-6 ">
        <?= Html::textInput('options[procedure_name]['.$key.']', $val ,['class'=>'form-control','placeholder'=>'Procedure Name'])?>
    </div>
    <div class="clearfix"></div>
</div>
<?php } ?>

<div class="form-group row">
    <div class="col-md-6 ">
        <?= Html::textInput('options[procedure_name]['.$newKey.']', '' ,['class'=>'form-control','placeholder'=>'Procedure Name'])?>
    </div>
    <div class="clearfix"></div>
</div>