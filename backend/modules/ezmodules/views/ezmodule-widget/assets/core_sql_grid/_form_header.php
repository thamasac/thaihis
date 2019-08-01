<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$key_header = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<div id="<?=$key_header?>" class="row" style="margin-bottom: 10px;">
<div class="col-md-3"><input type="text" class="form-control varname-input" name="options[header][<?=$key_header?>][varname]" ></div>
<div class="col-md-3 sdbox-col"><input type="text" class="form-control label-input" name="options[header][<?=$key_header?>][label]" ></div>
<div class="col-md-2 sdbox-col"> <?= Html::dropDownList("options[header][$key_header][align]", '', ['left'=>'Left', 'right'=>'Right', 'center'=>'Center'], ['class'=>'form-control align-input'])?></div>
<div class="col-md-2 sdbox-col"><input type="number" class="form-control width-input" name="options[header][<?=$key_header?>][width]" ></div>
<div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>
