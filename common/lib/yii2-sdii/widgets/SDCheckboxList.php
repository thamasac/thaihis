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
use yii\widgets\InputWidget;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzformWidget;

class SDCheckboxList extends InputWidget {

    public $fields;
    public $inline;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
	$fields;
        if(isset($this->fields)){
            echo '<div data-type="checkbox" inline="'.$this->inline.'">';
            foreach ($this->fields as $key => $value) {
                $options = isset($this->options['itemOptions'])?$this->options['itemOptions']:[];
                
                $annotated = 0;
                if(isset($this->options['annotated']) && $this->options['annotated']){
                    $options['annotated'] = 1;
                    $annotated = 1;
                }
                
                $options = ArrayHelper::merge($options, [
                    'label'=>$value['label'] . ' '.($annotated?"<code>{$value['attribute']}</code>":''),
                    'inline'=> $this->inline,
                ]);

                if(isset($value['other'])){
                    $options = ArrayHelper::merge($options, [
                        'other'=> $value['other'],
                    ]);
                }
                
                
                
                if ($this->hasModel()) {
                    echo EzformWidget::activeCheckbox($this->model, $value['attribute'], $options);
                } else {
                    echo EzformWidget::checkbox($value['attribute'], false, $options);
                }
            }
            echo '</div>';
        } else {
            return 'Fields not set.';
        }
    }
    
    public function registerClientScript() {
	$view = $this->getView();
        
    }
    
}
