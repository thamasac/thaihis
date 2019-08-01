<?php

namespace appxq\sdii\helpers;

/**
 * SDHtml class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 6 ต.ค. 2558 15:13:53
 * @link http://www.appxq.com/
 * @example 
 */
class SDHtml {

	public static function getMsgSuccess() {
		return '<strong><i class="glyphicon glyphicon-ok-sign"></i> Success!</strong> ';
	}

	public static function getMsgError() {
		return '<strong><i class="glyphicon glyphicon-warning-sign"></i> Error!</strong> ';
	}
        
        public static function getMsgWarning() {
		return '<strong><i class="glyphicon glyphicon-warning-sign"></i> Warning!</strong> ';
	}

	public static function getBtnAdd() {
		return '<span class="glyphicon glyphicon-plus"></span>';
	}

	public static function getBtnDelete() {
		return '<span class="glyphicon glyphicon-minus"></span>';
	}

	public static function getBtnRepeat() {
		return '<span class="glyphicon glyphicon-repeat"></span>';
	}
	
        public static function getBtnSearch() {
		return '<span class="glyphicon glyphicon-search"></span>';
	}
}
