<?php
namespace appxq\sdii\widgets;
/**
 * SDEzGrid class file UTF-8
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

class SDEzGrid extends InputWidget {

    public $fields;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
	$fields;
        $header;
        $row_now = 1;
        
        if(isset($this->fields)){
            $thead = true;
            //echo '<div class="table-responsive">';
            echo '<table class="table table-striped eztable" style="margin-bottom: 5px;background-color: #fff;">';
            foreach ($this->fields as $key => $value) {
                $row = explode('_', $key);
                
                if($thead){
                    echo '<thead><tr>';
                    if(isset($value['header']) && is_array($value['header'])){
                        $header = $value['header'];
                        foreach ($value['header'] as $key_item => $value_item){
                            echo '<th>';
                            echo $value_item['label'];
                            echo '</th>';
                        }
                    }
                    echo '</tr></thead>';
                    echo '<tbody><tr>';
                    $thead = false;
                }
                
                if($row[0]>$row_now){
                    echo "<tr>";
                    $row_now=$row[0];
                }
                
                $type = 'textinput';
                foreach ($header as $keyCol => $valueCol) {
                    if($valueCol['col']==$row[1]){
                        $type = $valueCol['type'];
                    }
                }
                
                if ($this->hasModel()) {
                    if($type=='textinput'){
                        $html_input = EzformWidget::activeTextInput($this->model, $value['attribute'], ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    } elseif ($type=='textarea') {
                        $html_input = EzformWidget::activeTextarea($this->model, $value['attribute'], ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    } elseif ($type=='datetime') {
                        $inputId = \yii\helpers\Html::getInputId($this->model, $value['attribute']);
                        $inputName = \yii\helpers\Html::getInputName($this->model, $value['attribute']);
                        $inputValue = \yii\helpers\Html::getAttributeValue($this->model, $value['attribute']);
                        
                        $html_input = \trntv\yii\datetimepicker\DatetimepickerWidget::widget([
                            //'model' => $this->model,
                            //'attribute' => $value['attribute'],
                            'id'=>$inputId,
                            'name'=>$inputName,
                            'value'=>$inputValue!=''?$inputValue:NULL,
                            'clientOptions' => [
                                'format' => 'DD-MM-YYYY',
                                'sideBySide' => true,
                            ],
                            'options' => [
                                'placeholder'=>$value['label']
                            ]
                        ]);
                    } elseif ($type=='checkbox') {
                        $html_input = '<div class="checkbox">'.EzformWidget::activeCheckbox($this->model, $value['attribute'], ['label'=>$value['label']]).'</div>';
                    } elseif ($type=='label') {
                        $html_input = \yii\helpers\Html::label($value['label']);
                    } else {
                        $html_input = EzformWidget::activeTextInput($this->model, $value['attribute'], ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    }
                } else {
                    $setValue = isset($value['value'])?$value['value']:'';
                    
                    $html_input = EzformWidget::radio($value['attribute'], false, ['value'=>$setValue, 'label'=>'']);
                    if($type=='textinput'){
                        $html_input = EzformWidget::textInput($value['attribute'], $setValue, ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    } elseif ($type=='textarea') {
                        $html_input = EzformWidget::textarea($value['attribute'], $setValue, ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    } elseif ($type=='datetime') {
                        $html_input = \trntv\yii\datetimepicker\DatetimepickerWidget::widget([
                            'name' => $value['attribute'],
                            'value' => $setValue,
                            'clientOptions' => [
                                'format' => 'DD-MM-YYYY',
                                'sideBySide' => true,
                            ],
                            'options' => [
                                'placeholder'=>$value['label']
                            ]
                        ]);
                    } elseif ($type=='checkbox') {
                        $html_input = '<div class="checkbox">'.EzformWidget::checkbox($value['attribute'], $setValue, ['label'=>$value['label']]).'</div>';
                    } elseif ($type=='label') {
                        $html_input = \yii\helpers\Html::label($value['label']);
                    } else {
                        $html_input = EzformWidget::textInput($value['attribute'], $setValue, ['placeholder'=>$value['label'], 'class'=>'form-control']);
                    }
                }
                $annotated = '';
                if(isset($this->options['annotated']) && $this->options['annotated']){
                    $annotated = "<code>{$value['attribute']}</code>";
                }
                echo '<td >'.$html_input.$annotated.'</td>';
                
                if($row[0]!=$row_now){
                    echo "</tr>";
                }
                
                
            }
            echo '</tr>';
            
            echo '</table>';
            //echo '</div>';
        } else {
            return 'Fields not set.';
        }
    }
    
    public function registerClientScript() {
	$view = $this->getView();
        
    }
    
}
