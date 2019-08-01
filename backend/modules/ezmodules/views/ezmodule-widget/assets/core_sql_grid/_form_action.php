<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$key_action = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<div id="<?=$key_action?>" class="row" style="margin-bottom: 10px;">
<div class="col-md-6"><input type="text" class="form-control action-input" name="options[actions][<?=$key_action?>][action]" ></div>
<div class="col-md-4 sdbox-col"><input type="text" class="form-control cond-input" name="options[actions][<?=$key_action?>][cond]" ></div>
<div class="col-md-2 sdbox-col"><a href="#" class="action-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>
