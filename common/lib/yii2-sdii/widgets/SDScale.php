<?php
namespace appxq\sdii\widgets;
/**
 * SDScale class file UTF-8
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
use yii\helpers\Html;

class SDScale extends InputWidget {

    public $fields;
    public $enable_label;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
        
	$fields;
        $data;
        $style = 'transform: scale(2, 2);-moz-transform: scale(2, 2);-ms-transform: scale(2, 2);-webkit-transform: scale(2, 2);-o-transform: scale(2, 2);margin-right: 8px !important;margin-left: 0px !important;';
        if(isset($this->fields)){
            $thead = true;
            
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped" style="margin-bottom: 5px;background-color: #fff;">';
            foreach ($this->fields as $key => $value) {
                //$row = explode('_', $key);
                if($thead){
                    echo '<thead><tr><th style="min-width: 350px"></th>';
                    if(isset($value['data']) && is_array($value['data'])){
                        $data = $value['data'];
                        foreach ($value['data'] as $key_item => $value_item){
                            echo '<th style="text-align: center">';
                            echo $value_item['label'];
                            
                            if(isset($this->options['annotated']) && $this->options['annotated']==1){
                                echo "<code>{$value_item['value']}</code>";
                            }
                            
                            echo '</th>';
                        }
                    }
                    echo '</tr></thead>';
                    echo '<tbody><tr>';
                    $thead = false;
                }
                
                if(!$thead){
                    echo '<tr>';
                }
                $annotated = '';
                if(isset($this->options['annotated']) && $this->options['annotated']==1){
                    $annotated = "<code>{$value['attribute']}</code>";
                }
                echo '<td style="vertical-align: middle;">'.$value['label'].' '.$annotated.'</td>';
                if(isset($data) && !empty($data)){
                    $findex = 1;
                    foreach ($data as $key_item => $value_item){
                        $html_input = '';
                        
                        $itemLabel = '';
                        if(isset($this->enable_label) && $this->enable_label==1){
                            $itemLabel = $value_item['label'];
                        }
                        if ($this->hasModel()) {
                            if($findex==1){
                                $name = EzformWidget::getInputName($this->model, $value['attribute']);
                                $html_input .= EzformWidget::hiddenInput($name, 0);
                            }
                            $input_id = \yii\helpers\Html::getInputId($this->model, $value['attribute']);
                            
                            $html_input .= Html::activeRadio($this->model, $value['attribute'], ['id'=>"{$input_id}_{$value_item['value']}", 'value'=>$value_item['value'], 'label'=>$itemLabel, 'style'=>$style]);
                        } else {
                            $html_input = Html::radio($value['attribute'], false, ['value'=>$value_item['value'], 'label'=>$itemLabel, 'style'=>$style]);
                        }
                        
                        echo '<td style="text-align: center"><div class="radio">'.$html_input.'</div></td>';
                        $findex++;
                    }
                }
                
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        } else {
            return 'Fields not set.';
        }
    }
    
    public function registerClientScript() {
	$view = $this->getView();
        
    }
    
}

