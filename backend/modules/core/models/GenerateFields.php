<?php

namespace backend\modules\core\models;

use Yii;
use yii\data\ArrayDataProvider;
use yii\base\ModelEvent;
use appxq\sdii\utils\SDUtility;

/**
 *
 * @property string $option_name
 * @property string $option_value
 * @property string $input_field
 * @property string $input_label
 * @property string $input_data
 * @property string $input_meta
 * @property string $input_hint
 * @property integer $serialize
 * @property integer $input_required
 * @property string $input_validate
 * @property string $input_order
 */
class GenerateFields extends \yii\base\Model {

	public $option_name;
	public $option_value;
	public $input_field;
	public $input_label;
	public $input_data;
	public $input_meta;
	public $input_hint;
	public $serialize;
	public $input_required;
	public $input_validate;
	public $input_specific;
	public $input_order;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['input_field', 'option_name'], 'required'],
			[['option_value', 'input_data', 'input_specific', 'input_meta', 'input_hint', 'input_validate'], 'string'],
			[['serialize', 'input_required'], 'integer'],
			[['input_order'], 'number'],
			[['input_field'], 'string', 'max' => 20],
			[['option_name'], 'string', 'max' => 80],
			[['option_name'], 'unique']
		];
	}

	public function attributeLabels() {
		return [
			'option_name' => Yii::t('core', 'Name'),
			'option_value' => Yii::t('core', 'Value'),
			'serialize' => Yii::t('core', 'Serialize'),
			'input_label' => Yii::t('core', 'Label'),
			'input_hint' => Yii::t('core', 'Hint'),
			'input_field' => Yii::t('core', 'Field'),
			'input_specific' => Yii::t('core', 'Specific'),
			'input_data' => Yii::t('core', 'Data'),
			'input_required' => Yii::t('core', 'Required'),
			'input_validate' => Yii::t('core', 'Validate'),
			'input_meta' => Yii::t('core', 'Option'),
			'input_order' => Yii::t('core', 'Order'),
		];
	}

	public function loadDataAll($obj){
		$session = Yii::$app->session;
		$arr = [];

		if (!isset($session['field_tmp'])) {
			$session['field_tmp'] = $obj;
		} else {
			return FALSE;
		}

		return TRUE;
	}
	
	public function loadDataOne($id){
		$session = Yii::$app->session;
		
		if (isset($session['field_tmp'])) {
			$arr = $session['field_tmp'];
			
			foreach ($arr as $key => $value) {
				if ($value['option_name'] == $id) {
					$this->attributes = $value;
				}
			}
		}

		$this->input_validate = SDUtility::string2strArray($this->input_validate);
		$this->input_meta = SDUtility::string2strArray($this->input_meta);
		$this->input_specific = SDUtility::string2strArray($this->input_specific);

		return $arr;
	}
	
	public function defaultAttributes(){
		$session = Yii::$app->session;
		//$this->input_validate = "['string']";
		$this->input_order = 1;
			
		if (isset($session['field_tmp'])) {
			$this->input_order = count($session['field_tmp']) + 1;
		} 
	}

	public function saveData($id = NULL){
		$session = Yii::$app->session;
		
		$this->input_validate = SDUtility::strArray2String($this->input_validate);
		$this->input_meta = SDUtility::strArray2String($this->input_meta);
		$this->input_specific = SDUtility::strArray2String($this->input_specific);

		$arr = [];

		if (isset($session['field_tmp'])) {
			$arr = $session['field_tmp'];
			
			if(isset($id)){
				foreach ($arr as $key => $value) {
					if ($value['option_name'] == $id) {
						$arr[$key] = $this->attributes;
						break;
					}
				}
			} else {
				$arr[] = $this->attributes;
			}
		} else {
			$arr[] = $this->attributes;
		}
				
		usort($arr, function($a, $b) {
			return strcmp($a['input_order'], $b['input_order']);
		});

		$session['field_tmp'] = $arr;
		
		return $arr;
	}
	
	public function deleteData($id){
		$session = Yii::$app->session;
		$arr = [];
		
		if (isset($session['field_tmp'])) {
			$arr = $session['field_tmp'];
			foreach ($arr as $key => $value) {
				if ($value['option_name'] == $id) {
					unset($arr[$key]);
					break;
				}
			}
		} else {
			return FALSE;
		}

		$session['field_tmp'] = $arr;
		
		return $arr;
	}

	public function resetData(){
		unset(Yii::$app->session['field_tmp']);
	}
	
	public function fieldTmp() {
		$arr = [];
		$session = Yii::$app->session;
		if (isset($session['field_tmp'])) {
			$arr = $session['field_tmp'];
		}

		$dataProvider = new ArrayDataProvider([
			'allModels' => $arr,
			'key' => 'option_name',
			'sort' => [
				'attributes' => ['option_name', 'input_order'],
			],
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		return $dataProvider;
	}

}
