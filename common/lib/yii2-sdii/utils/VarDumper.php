<?php

namespace appxq\sdii\utils;
use Yii;

class VarDumper {
	/**
	* Displays a variable.
	* This method achieves the similar functionality as var_dump and print_r
	* but is more robust when handling complex objects such as Yii controllers.
	* @param mixed $var variable to be dumped
	* @param boolean $end Enable Yii::$app->end();
	* @param boolean $highlight whether the result should be syntax-highlighted
	*/
	public static function dump($var, $end=true, $highlight = true) {
            \yii\helpers\VarDumper::dump($var, 10, $highlight);
            if($end){
                Yii::$app->end();
            }
	}
}
