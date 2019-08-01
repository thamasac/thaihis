<?php
namespace appxq\sdii\widgets;

use yii\base\Widget;
use yii\web\View;

/**
 * Description of CSSRegister
 *
 * @author appxq
 */
class CSSRegister extends Widget {
    //variables to be passed to \yii\base\View::registerCss()
    public $key = null;
    public $position = [];
        
    /**
        * Start widget by calling ob_start(), caching all output to output buffer
        * @see \yii\base\Widget::begin()
        */
       public static function begin($config = []){
               $widget = parent::begin($config);

               ob_start();

               return $widget;
       }


       /**
        * Get script from output buffer, and register by \yii\web\View::registerCss()
        * @see \yii\base\Widget::end()
        */
       public static function end(){
               $script = ob_get_clean();
               $widget = parent::end();

               if(preg_match("/^\\s*\\<style\\>(.*)\\<\\/style\\>\\s*$/s", $script, $matches)){
                       $script = $matches[1];
               }

               $widget->view->registerCss($script, $widget->position, $widget->key);

       }
}
