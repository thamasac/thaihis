<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\Json;

class SDRatingStar extends InputWidget
{

    public $theme = 'bootstrap-stars';
    
    public $allowEmpty = true;
    
    public $emptyValue = 0;
    
    public $items = [0, 1, 2, 3, 4, 5];
    
    public $pluginOptions = [];
    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!isset($this->options['id'])){
            $this->options['id'] = $this->hasModel()?Html::getInputId($this->model, $this->attribute):$this->name;
        }
        parent::init();
        if(!isset($this->pluginOptions['theme'])){
            $this->pluginOptions['theme'] = $this->theme;
        }
        
//        if(isset($this->pluginOptions['theme']) && $this->pluginOptions['theme']=='bars-1to10'){
//            $this->items = [0,1,2,3,4,5,6,7,8,9,10];
//        }
        
        $this->pluginOptions['allowEmpty'] = $this->allowEmpty;
        $this->pluginOptions['emptyValue'] = $this->emptyValue;
        $this->pluginOptions['readonly'] = false;
        
        if(isset($this->options['readonly']) && $this->options['readonly']){
            $this->pluginOptions['readonly'] = TRUE;
        }
        
        if(isset($this->options['maxNummber'])){
            $this->items = \appxq\sdii\utils\SDUtility::num2array($this->options['maxNummber']);
        }
        
    }

    /**
     * @inheritdoc
     */
    public function run()
    {

        $input = $this->hasModel()
            ? Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options)
            : Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        
        echo $input;

        $this->registerClientScript();
    }

    /**
     * Registers required script for the plugin to work as DatePicker
     */
    public function registerClientScript()
    {
        $js = '';
        $view = $this->getView();
        
        \appxq\sdii\assets\BarRatingAsset::register($view)->css = ['themes/' . $this->pluginOptions['theme'] . '.css'];
        
        $id = $this->options['id'];
        $selector = "$('#$id')";
        
        $options = !empty($this->pluginOptions) ? Json::encode($this->pluginOptions) : '';

        $js = "$selector.barrating($options);";
        $js .="
            $selector.removeClass('dads-children');
            $selector.parent().removeClass('dads-children');
            $selector.parent().find('.br-widget').removeClass('dads-children');
            ";
        $view->registerJs($js);
    }

}
