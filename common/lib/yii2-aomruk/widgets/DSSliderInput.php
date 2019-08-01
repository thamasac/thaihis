<?php

namespace dms\aomruk\widgets;

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
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class DSSliderInput extends InputWidget {

//    public $max = 100;
//    public $min = 0;

    public function init() {
        parent::init();

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();

        if (isset($this->options['class'])) {
            $this->options['class'] .= ' slider';
        } else {
            $this->options['class'] = 'slider';
        }
//        if (isset($this->options['style'])) {
//            $this->options['style'] .= "width:20px;" . $this->options['style'];
//        } else {
//            $this->options['style'] = "width:20px;";
//        }
//        if ($this->label != '') {
//            $this->options['label'] = $this->label;
//        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id . "-slider";
        }
        if (!isset($this->options['max'])) {
            $this->options['id'] = 100;
        }
        if (!isset($this->options['min'])) {
            $this->options['id'] = 0;
        }
        $this->options['text'] = isset($this->options['text']) ? ' ' . $this->options['text'] : '';
//        $this->options['readonly']= true;
//        $this->options['data-ezf_id'] = $this->ezf_id;
//        $this->options['data-ezf_field_id'] = $this->ezf_field_id;
//        \appxq\sdii\utils\VarDumper::dump($this->options);
    }

    public function run() {

        if ($this->hasModel()) {
            if ($this->model[$this->attribute] == '') {
                $this->model[$this->attribute] = 0;
                $this->value = 0;
            } else {
                $this->value = $this->model[$this->attribute];
            }

            echo Html::activeInput('range', $this->model, $this->attribute, $this->options) . 
                    Html::tag('div', Html::tag('div', $this->options['min'], ['class' => 'pull-left label label-info']) .
                    Html::tag('span', $this->value . $this->options['text'], ['id' => $this->id . '_div']) .
                    Html::tag('div', $this->options['max'],['class' => 'pull-right label label-info']),['class' => 'text-center']);
        } else {
            if($this->value == ''){
                $this->value = 0;
            }
            echo Html::input('range', $this->name, $this->value, $this->options) . Html::tag('div', Html::tag('div', $this->options['min'], ['class' => 'pull-left label label-info']) .
                    Html::tag('span', $this->value . $this->options['text'], ['id' => $this->id . '_div']) .
                    Html::tag('div', $this->options['max'],['class' => 'pull-right label label-info']),['class' => 'text-center']);
        }
        $this->registerClientScript();
        $this->registerClientCss();
    }

    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
                $('#{$this->options['id']}').on('input',function(){
                    $('#{$this->id}_div').html($(this).val()+'{$this->options['text']}');
                });
            ");
    }
    
    public function registerClientCss(){
        $view = $this->getView();
        $view->registerCss("
                .slider:hover {
                    cursor: pointer;
                    
                }
            ");
    }

}
