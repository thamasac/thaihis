<?php

namespace cpn\chanpan\widgets;
use yii\helpers\Url;
use yii\helpers\Html;

class CNZoom extends \yii\base\Widget{
    //put your code here
    /**
     *
     *  $options['type'] string  example type='inner' , lens<br>
     *  $options['lensSize'] integer  example type=500<br>
     *      You can use the lens zoom setting to "Magnify the image".
            The image to the lest has been constrained so it tucks underneath the image.
     *  
     * $options['scrollZoom'] type boolean true or false
     */
    public $img = '';
    public $options = [];
    public $src = '';
    public function init() {
        parent::init();
    }
    public function run() {
        parent::run();
        $this->registerScript();
        echo Html::img($this->src, [
            'data-zoom-image'=> $this->src, 
            'id'=>($this->options['id'] != '') ? $this->options['id'] : 'zoom_01',
            'width'=>($this->options['width'] != '') ? $this->options['width'] : '500',
            'height'=>($this->options['height'] != '') ? $this->options['height'] : '500',
            'class'=>($this->options['class'] != '') ? $this->options['class'] : 'img img-responsive'
        ]);
    }
    public function registerScript(){
        $view = $this->getView();
        $css = "
            .dads-children:hover {
                background-color: transparent;
            }  
        ";
        $view->registerCss($css);
        \cpn\chanpan\assets\zoom\CNZoomAsset::register($view);
        $js="
            var id = '".$this->options['id']."';
            var type = '".$this->options['type']."';
            var lensSize = '".$this->options['lensSize']."';
            var scrollZoom = '".$this->options['scrollZoom']."';
                
            if(id == ''){
                id = 'zoom_01';
            }
            if(type == ''){
                type = 'inner';
            }
            if(lensSize == ''){
                lensSize = 300;
            }
            if(scrollZoom == ''){
                scrollZoom = false;
            }
            
            $('#'+id).elevateZoom({
               zoomType: type,
               cursor: 'crosshair',
               zoomWindowFadeIn: 500,
               zoomWindowFadeOut: 1000,
               lensSize: lensSize,
               scrollZoom : scrollZoom
           });    
        ";
        $view->registerJs($js);
    }
}
