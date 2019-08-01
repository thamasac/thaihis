<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);


?>


<!--config start-->



<!--config end-->

<?php
$this->registerJS("
    
");
?>