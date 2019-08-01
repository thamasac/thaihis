<?php

namespace cpn\chanpan\widgets;

use yii\helpers\Url;
use yii\helpers\Html;

class JDrag extends \yii\base\Widget{
    public $url = '';
    public $options = [
        'header'=>'',
        'body'=>''
    ];
    public $data = [];
    public $mode = '';
    public function init() {
        parent::init();
        $this->options['header']=($this->options['header'] != "") ? $this->options['header'] : 'col-md-2 col-sm-3 col-xs-4 text-center';
        $this->options['body']=($this->options['body'] != "") ? $this->options['body'] : 'col-md-10 col-sm-10 col-xs-10';
    }
    public function run() {
        $html = "";
        $grid = '';
        $html .= Html::beginTag("DIV", ['class' => 'dad col-container', 'id' => 'dropBox','style'=>"max-width:1920px;"]);
        foreach ($this->data as $key => $v) {
            $dataId = $v["data-id"];
            $collaboration = $v["collaboration"];
            //$grid .= Html::beginTag("DIV",isset($v['link-options']) ? $v['link-options'] : []);
            $html .= Html::beginTag("DIV", ['class' => "{$this->options['header']} col-3", 'data-id' => "{$v['data-id']}"]);
            /*button*/
            $html .= Html::beginTag("DIV", ['data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>"{$v['title']}",'class' => "{$this->options['body']} draggable {$v['link-options']['class']}", 'style' => 'padding:0', 'data-url' => $v['link-options']['data-url']]);
            $html .= Html::beginTag("DIV", ['class' => 'xxx children', 'data-id' => "{$v['data-id']}"]);
            $html .= Html::img($v['img'], $v['options']);
            $html .= Html::tag("DIV", "<b>" . $v['content'] . "</b>", []);
            $html .= Html::endTag("DIV");
            //
            $html .= Html::endTag("DIV");

            /*content and img*/
            $html .= "
                        <div class='col-md-1 draggable btnEdit' style='padding:0; display:none;'>
                            <i class='fa btnDrag' style='line-height:55px;height:10px;margin-top: 25px;    margin-left: 4px;'></i>
                            <i title='Setting' class='fa fa-cog btnEdits' data-id='$dataId'  style='    margin-left: 2px;margin-top:5px;'></i>
                        </div>";
            //$grid .= Html::endTag("DIV");
            $userIsCreate = \Yii::$app->user->id ==  $v["user_create"];
            if($this->mode !='co-creator'){
                $html .= "<div class='col-md-12'>";
                if(($collaboration == 1 || $collaboration == 2) && !$userIsCreate)
                    $html .= "<button type='button' class='btn-sm btn-primary btnJoin' data-collaboration ='$collaboration' data-id='$dataId' style='margin-top: 5px;'>Join</button>";
                $html .= "</div>";

            }
            $html .= Html::endTag("DIV");

            //div2

        }

        $html .= Html::endTag("DIV");

        $this->registerScript();
        echo $html;
    }

    public function renderImage()
    {

    }

    public function registerScript()
    {
        $view = $this->getView();
        \cpn\chanpan\assets\jdrag\JDragAssets::register($view);
        $css = "
            .dads-children:hover {
                background-color: transparent;
                border-radius: 5px;
            }
            .btnEdits{cursor:pointer}
            .xxx.children {
                margin-top: 15px;
            }
            .dad-noSelect,.dad-noSelect *{
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                cursor: -webkit-grabbing !important;
                cursor: -moz-grabbing !important;
            }

            .dad-container{
                position: relative;
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            .dad-container::after{
                content: '';
                clear: both !important;
                display: block;
            }
            .dad-active .dad-draggable-area{
                cursor: -webkit-grab;
                cursor: -moz-grab;
            }
            .dad-draggable-area>*,.dad-draggable-area img{
                pointer-events: none;
            }
            .dads-children.active{
                pointer-events: none;
            }
            .dads-children-clone{
                opacity: 1;
                z-index: 9999;
                pointer-events: none;
            }
            .dads-children-placeholder{
                pointer-events: none;
                overflow: hidden;
                position: absolute !important;
                box-sizing: border-box;
               /*border:2px dashed #639BF6;*/

                border-radius:5px;
                margin:5px;
                text-align: center;
                color: #639BF6;
                font-weight: bold;
                border:3px dashed #639BF6;
            }
            .drag{
                background:blue;
            }

            .btnDrag{
                background-image: url(" . Url::to('@web/img/handle.svg') . ");
                background-repeat: no-repeat;
                opacity: 0.55;
                width:1em;
            }
        ";
        $view->registerCss($css);
        $js = "
            /*show and hide button edit and drag*/
            $('.col-3').hover(function(){
                let id = $(this).attr('data-id');
                $('.col-3[data-id='+id+'] .btnEdit').fadeIn('slow');
            },
                function(){                    
                    $('.btnEdit').hide();
                } 
            );
            
            /*drag and drop*/
            var options = {
                draggable: '.btnDrag',
                 
                callback: function (e) {
                    var positionArray = [];
                    $('.draggable').find('.children').each(function () {
                        positionArray.push($(this).attr('data-id'));
                    });
                    //delete positionArray[positionArray.length-1];
                    positionArray.splice(positionArray.length - 1, positionArray.length);
                    $.get('" . $this->url . "', {data:positionArray.toString()}, function(data){
                        console.log(data);
                    });
                }
            };
            $('#dropBox').dad(options);
            $(function () {
                $('[data-toggle=\"tooltip\"]').tooltip();
              });
        ";
        $view->registerJs($js);
    }
}
