<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$key_search = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<div id="<?=$key_search?>" class="row" style="margin-bottom: 10px;">
<div class="col-md-3"><input type="text" class="form-control varname-input" name="options[search_column][<?=$key_search?>][varname]" ></div>
<div class="col-md-3 sdbox-col"><input type="text" class="form-control value-input" name="options[search_column][<?=$key_search?>][value]" ></div>
<div class="col-md-2 sdbox-col"><a href="#" class="search-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>
