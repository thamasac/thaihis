<?php

namespace cpn\chanpan\widgets;

use yii;
use yii\base\Widget;
use yii\web\View;
use yii\helpers\Html;
use cpn\chanpan\assets\WizardWidgetAsset;

class CNWizards extends Widget {

    public $defaultStep='';
    public $config=[];
    public $step = '';
    public $urlStart="";
    public function init() {
        parent::init();
        $this->defaultStep = isset($this->defaultStep) ? $this->defaultStep : '0';
    }
    public function run() {
        $nStep= isset($_GET['step']) ? $_GET['step'] : 1;
        $this->step = $nStep;
        $urlNext = "";
        $urlPrevious="";
        
        parent::run(); 
        $wizard = Html::beginTag('DIV', ['id'=>'chanpan_progress']);
            foreach($this->config as $k=>$v){               
                $id = isset($v['id']) ? $v['id'] : $v['step'];
                
                if($this->defaultStep==$k){
                   if($k!=0){
                       $urlPrevious = $this->config[$k-1]['url'];
                   }
                   if($nStep < count($this->config)){
                       $urlNext = $this->config[$k+1]['url'];
                   } 

                   $wizard .= Html::beginTag("SPAN",['class'=>'progress_bar']);
                        $wizard .= Html::a("{$v['step']}<br>{$v['name']}", $v['url'], ['class'=>'active','id'=>$id]); 
                   $wizard .= Html::endTag("SPAN"); 
                    
                }else if($nStep <= count($this->config)){
                   $wizard .= Html::beginTag("SPAN",['class'=>'progress_bar']);
                        $wizard .= Html::a("{$v['step']}<br>{$v['name']}", $v['url'], ['id'=>$id]); 
                   $wizard .= Html::endTag("SPAN");
                }
                
            }
            
        $wizard .= Html::endTag('DIV'); 
        $wizard .= Html::beginTag('DIV',['class'=>'text-right','style'=>'margin-top:20px;    float: right;']);
        if($nStep != 1){
            $wizard .= Html::button("<i class='glyphicon glyphicon-chevron-left'></i> ".\Yii::t('chanpan', 'Previous'), [
                'id'=>'btnPrevious',
                'data-href'=>$urlPrevious,
                'class'=>'btn btn-default'
            ]);
            $wizard .= ' ';
        }
        if($nStep < count($this->config)){
            $wizard .= Html::button("<i class='glyphicon glyphicon-chevron-right'></i> ".\Yii::t('chanpan', 'Next'), [
                'id'=>'btnNext',
                'data-href'=>$urlNext,
                'class'=>'btn btn-success'
            ]);
            $wizard .= ' ';
        }else{
            $wizard .= Html::button("<i class='fa fa-hand-o-right'></i> ".\Yii::t('chanpan', 'Start')." <i class='fa fa-hand-o-left'></i>", [
                'id'=>'btnStart',
                'data-href'=>$this->urlStart,
                'class'=>'btn btn-primary'
            ]);
            $wizard .= ' ';
        }            
        $wizard .= Html::endTag("DIV");
        $this->registerClientScript();
        $this->registerCssScript();
        echo $wizard;
    }
    public function registerCssScript(){
        $view = $this->getView();
        $view->registerCss("       
                .progress_bar {
                    background-color: #eee;
                    box-sizing: border-box;
                    display: table-cell;
                    vertical-align: middle;

                }
                #chanpan_progress .progress_bar a {
                    width: 100em;
                    height: 55px;
                    line-height: normal;
                    padding: 0 10px;
                    text-align: center;
                    display: table-cell;
                    color: #999;
                    vertical-align: middle;
                    text-decoration: none;
                    border-left: 1px solid #fff;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                }
                #chanpan_progress .progress_bar a.active {
                    color: #fff;
                    background-color: #1ab7ea;
                    position: relative;
                    width: 100em;
                }
                #chanpan_progress .progress_bar a.active::after {
                    border-left: 15px solid transparent;
                    border-right: 15px solid transparent;
                    border-top: 15px solid #1ab7ea;
                    bottom: -15px;
                    content: '';
                    height: 0;
                    left: 50%;
                    margin: auto;
                    position: absolute;
                    -webkit-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                    transform: translateX(-50%);
                    width: 0;
                    z-index: 1;
                }
        ");
    }
    public function registerClientScript() {
	$view = $this->getView();
        //WizardWidgetAsset::register($view);        
        $view->registerJs("
            $('#btnStart').on('click',function(){
                let urlHref = $(this).attr('data-href');
                let url = '/manageproject/step/set-start';                    
                $.get(url,function(data){
                    console.log(urlHref);
                    location.href = urlHref;
                }); 
                return false;
            });
            $('#btnPrevious').on('click',function(){
                    let urlHref = $(this).attr('data-href');
                    location.href = urlHref;
                    return false;
                });
                $('#btnNext').on('click', function(){
                    let urlHref = $(this).attr('data-href');
                    let step = parseInt('".$this->step."')+1;
                    let url = '/manageproject/step/set-cookie?step='+step;                    
                    $.get(url,function(data){
                        console.log(urlHref);
                        location.href = urlHref;
                    }); 
                    return false;
                });
        ");
    }
     

     

}
