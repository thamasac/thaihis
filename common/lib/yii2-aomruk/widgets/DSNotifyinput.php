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

class DSNotifyinput extends InputWidget {

    public $ezf_id;
    public $ezf_field_id;
//    public $rows = 5;
    public $label = '';
//    public $maxlength = 255;
    public $hide_input = false;
    public $widgetOption = [];

    public function init() {
        parent::init();

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();

//        if (isset($this->options['class'])) {
//            $this->options['class'] .= ' form-control';
//        } else {
//            $this->options['class'] = 'form-control';
//        }

        if (isset($this->options['style'])) {

            $this->options['style'] .= "width:20px;" . $this->options['style'];
        } else {
            $this->options['style'] = "width:20px;";
        }
        if ($this->label != '') {
            $this->options['label'] = $this->label;
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id . "-notify";
        }
//        $this->options['disabled'] = true;
//        \appxq\sdii\utils\VarDumper::dump($this->widgetOption);
        $this->options['data-ezf_id'] = $this->ezf_id;
        $this->options['data-ezf_field_id'] = $this->ezf_field_id;
    }

    public function run() {

        if ($this->hasModel()) {
            echo \backend\modules\ezforms2\classes\EzformWidget::activeCheckbox($this->model, $this->attribute, $this->options);
        } else {
            echo \backend\modules\ezforms2\classes\EzformWidget::checkbox($this->name, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
            ");
    }

}
