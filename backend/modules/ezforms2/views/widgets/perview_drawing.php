<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$bgsize = 'auto auto';
if($width > $height){
$bgsize = "{$width}px auto";
} else {
$bgsize = "auto {$height}px";
}

//\appxq\sdii\utils\VarDumper::dump($line);
?>
<?= yii\helpers\Html::img($line, [
        'width'=>$width,
        'height'=>$height,
        'style'=>"background-image: url('$bg');background-position: center center;background-repeat: no-repeat;background-size: $bgsize;"
    ])?>
