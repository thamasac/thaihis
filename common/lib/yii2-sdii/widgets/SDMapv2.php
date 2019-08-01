<?php
namespace appxq\sdii\widgets;
/**
 * SDProvince class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class SDMapv2 extends InputWidget {

    public $fields;
    public $key;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
	$inputLatID;
	$inputLngID;
	$inputLatValue;
	$inputLngValue;
	
        $fields;
        
        if(isset($this->fields)){
            foreach ($this->fields as $key => $value) {
                $fields[$value['label']] = $value['attribute'];
            }
        } else {
            return 'Fields not set.';
        }
        
	if ($this->hasModel()) {
            $inputLatID = Html::getInputId($this->model, $fields['lat']);
	    $inputLngID = Html::getInputId($this->model, $fields['lng']);
	    $inputLatValue = Html::getAttributeValue($this->model, $fields['lat']);
	    $inputLngValue = Html::getAttributeValue($this->model, $fields['lng']);
        }
        
        $annotated_lat = '';
        $annotated_lng = '';
        if(isset($this->options['annotated']) && $this->options['annotated']){
            $annotated_lat = "<code>{$fields['lat']}</code>";
            $annotated_lng = "<code>{$fields['lng']}</code>";
        }
                
	echo MapInput::widget([
            'key'=> $this->key,
	    'lat'=>$inputLatID,
	    'lng'=>$inputLngID,
	    'latValue'=>$inputLatValue,
	    'lngValue'=>$inputLngValue,
            'options'=>['id'=>$fields['lat'].'_'.$fields['lng'], 'annotated_lat'=>$annotated_lat, 'annotated_lng'=>$annotated_lng]
	]);
	
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $fields['lat']);
	    echo Html::activeHiddenInput($this->model, $fields['lng']);
        }
	
    }
    
}
