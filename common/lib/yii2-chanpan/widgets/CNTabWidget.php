<?php

namespace cpn\chanpan\widgets;
use yii\helpers\Html;


class CNTabWidget extends \yii\base\Widget{

    public $options = [];
    public $id = '';
    public $script = "";

    public function run(){
        $html = "";
        $html .= Html::beginTag('DIV');
        $html .= Html::beginTag('UL', ['class'=>'nav nav-tabs tabs-up','id'=> $this->id]);
        foreach ($this->options as $key => $o) {
            if (isset($o['active']) ? $o['active'] : '' == true) {
                $html .= Html::beginTag('LI', ['class'=>'active','id' => isset($o['id']) ? $o['id'] : $key]);
                $icon = Html::tag('I','',['class'=>$o['icon']]);
                $html .= Html::a($icon.' '.$o['title'],$o['url'],['class'=>'media_node active span','data-target'=>'#contacts','id'=>'contacts_tab','data-toggle'=>'tabajax', 'rel'=>'tooltip']);
                $html .= Html::endTag('LI');
            } else {
                $html .= Html::beginTag('LI',['id' => isset($o['id']) ? $o['id'] : $key]);
                $icon = Html::tag('I','',['class'=>$o['icon']]);
                $html .= Html::a($icon.' '.$o['title'],$o['url'],['class'=>'media_node active span','data-target'=>'#contacts','id'=>'contacts_tab','data-toggle'=>'tabajax', 'rel'=>'tooltip']);
                $html .= Html::endTag('LI');
            }
        }
        $html .= Html::endTag('UL');
        $html .= Html::beginTag('DIV',['class'=>'tab-content', 'style'=>'margin-top:10px;']);
        $html .= Html::tag("DIV", '', ['class'=>'tab-pane active', 'id'=>'contacts']);
        $html .= Html::tag("DIV", '', ['class'=>'tab-pane', 'id'=>'friends_list']);
        $html .= Html::tag("DIV", '', ['class'=>'tab-pane  urlbox span8', 'id'=>'awaiting_request']);
        $html .= Html::endTag("DIV");
        $html .= Html::endTag("DIV");
        $this->registerClientScript();
        echo $html;
    }
    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
            if($('li').hasClass('active')){
                   let \$this =   $('li.active [data-toggle=\'tabajax\']');
                   let loadurl = \$this.attr('href');
                   let targ = \$this.attr('data-target');                   
                   //alert(loadurl);
                   getData(loadurl,targ);             
                }
                $('li [data-toggle=\'tabajax\']').click(function(e) {
                    let \$this = $(this);  
                    let loadurl = \$this.attr('href');
                    let targ = \$this.attr('data-target');
                    getData(loadurl,targ);             
                    \$this.tab('show');
                    return false;
                });

                function getData(loadurl,targ){
                    $(targ).html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $.get(loadurl, function(data) {
                        $(targ).html(data);
                       //".$this->script."
                    });
                    
                }
        ");

    }

//run
}