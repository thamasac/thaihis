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
<div class="col-md-3"><input type="text" class="form-control varname-input" name="options[custom_label][<?=$key_header?>][varname]" ></div>
<div class="col-md-6 sdbox-col"><input type="text" class="form-control label-input" name="options[custom_label][<?=$key_header?>][label]" ></div>
<div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>

