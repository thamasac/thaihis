<?php

namespace appxq\sdii\models;

use yii\base\DynamicModel as BaseDynamicModel;

/**
 * DynamicModel class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 6 ต.ค. 2558 15:13:53
 * @link http://www.appxq.com/
 * @example 
 */

class DynamicModel extends BaseDynamicModel {

	private $_attributeLabels = [];

	public function attributeLabels() {
		return $this->_attributeLabels;
	}

	public function addLabel($attributeLabels) {
		$this->_attributeLabels = $attributeLabels;

		return $this;
	}

	public function getAttributeLabel($attribute) {
		$labels = $this->attributeLabels();
		return (isset($labels[$attribute]) && !empty($labels[$attribute])) ? $labels[$attribute] : $this->generateAttributeLabel($attribute);
	}

}
