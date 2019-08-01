<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

/**
 * Description of AlertNotify
 *
 * @author AR Soft
 */
class AlertNotify extends \yii\base\Component {

    public $icon = '<i class="fa fa-envelope"></i> ';
    public $position = 'right';
    public $time = 30;

    public function icon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function time($time) {
        $this->time = $time;
        return $this;
    }

    public function position($position) {
        $this->position = $position;
        return $this;
    }

    public static function ui() {

        return Yii::createObject(AlertNotify::className());
    }

    public function buildWidget() {
        $btnId = 'btn-' . \appxq\sdii\utils\SDUtility::getMillisecTime();
        $notifyId = 'notify-' . \appxq\sdii\utils\SDUtility::getMillisecTime();
        $divId = 'div-' . \appxq\sdii\utils\SDUtility::getMillisecTime();

        $html = '';
//        $html .= ModalForm::widget([
//                    'id' => 'modal-notify'
//        ]);
//        $html = '<div class="navbar-text pull-' . $this->position . '">';
        $html .= Html::button($this->icon . ' <span class="badge" id="' . $notifyId . '">0</span>', [
            'class' => 'btn btn-danger btn-xs pull-' . $this->position,
            'data-content' => "<div class='col-md-12 text-center'>" . Yii::t('notify', 'Loading') . "</div>",
            'id' => $btnId,
            'style' => "    margin-top: 15px;  margin-left: 10px;"
        ]);
//        $html .= Html::tag('div', '<center>' . Yii::t('notify', 'Loading Data') . '</center>', [
//                    'id' => $divId,
//                    'class' => 'border panel scrollbar square scrollbar-indigo thin',
//                    'style' => 'color:black;display:none;position: absolute;margin-top: 2px;margin-right: 5px;max-height:500px;width:400px;overflow-y:auto;']);
//        $html .= '</div>';
        $view = Yii::$app->getView();
        $view->registerJs("
                getCount();
                setInterval(getCount, $this->time*1000);
                var checkAjaxNotify = null;
                function getCount() {
                    if(checkAjaxNotify == null){
                        checkAjaxNotify = $.ajax({
                            method: 'POST',
                            url: '" . Url::to('/notify/notify/count-notify') . "',
                            dataType: 'HTML',
                            success: function (result, textStatus) {
                                if(textStatus != '0'){
                                    $('#$notifyId').html(result);
                                }
                                checkAjaxNotify = null;
                            }
                        }).fail(function(err) {
    //                        err = JSON.parse(JSON.stringify(err));
    //                        if(Array.isArray(err)){
    //                            if(err.indexOf('responseText'))
    //                            err = err['responseText'];
                                $('#$notifyId').html('0');
    //                        }
                               checkAjaxNotify = null
                       });
                   }
                }
                
                $('#$btnId').popover({ 
                    html : true,
                    container:'body',
                    title: '<h5 class=\'modal-title\' color=\'black\'>" . Yii::t('notify', 'Notification') . "</h5>',
                    trigger: \"toggle\",
                    template: '<div class=\"popover\" id=\'$divId\' role=\"tooltip\" style=\"min-width: 400px; max-width:21%; top:21%;position: fixed;\"><div class=\"arrow\"></div><h3 class=\"popover-title\"></h3><div class=\"popover-content\"><div class=\"data-content\"></div></div></div>',
                    placement:\"bottom\",
                    delay: { \"show\": 100, \"hide\": 300 }
                }).on('shown.bs.popover', function(){
                    $('#$divId').focus();
//                    if(!$('.popover').has('.btnViewAll').length){
//                        setTimeout(()=>{
//                            $('.popover').append('<div class=\'btnViewAll btn btn-default btn-block\'>" . Yii::t('notify', 'View All') . "</div>');
//                        },3000);
//                    }
//                    $('.btnViewAll').on('click', function () {
//                       window.location = '" . Url::to(['/ezmodules/ezmodule/view?id=1520785643053421500']) . "';
//                    });
                    getCount();
                    getNotify();

                });
               
                function getNotify(){
                     $.ajax({
                        method: 'POST',
                        url: '" . Url::to(['/notify/notify/get-notify', 'reloadDiv' => $btnId, 'notifyId' => $notifyId,'divId'=>$divId]) . "',
                        dataType: 'HTML',
                        success: function (result, textStatus) {
                            $('.popover-content').html(result).css({'color':'black','max-height':'700px','overflow-y':'auto'}).addClass('scrollbar');
                        }
                    }).fail(function(err) {
                       err = JSON.parse(JSON.stringify(err));
                        if(Array.isArray(err)){
                            if(err.indexOf('responseText'))
                            err = err['responseText'];
                            $('.popover-content').html(`<div class='alert alert-danger'>Server error</div>`);
                        }
                   });
                } 
                var t;
                let today = new Date().toLocaleDateString();
                $(document).ready(resetTimer);

                function reload() {
                let nowdate = new Date().toLocaleDateString();
                    location.reload();
                }
                
                function resetTimer() {
                   clearTimeout(t);
                    t= setTimeout(reload, 20*(60*1000));  // time is in milliseconds (1000 is 1 second)
                }
                
                function idleTimer() {
                    window.onready = resetTimer;
                    window.onload = resetTimer;
                    window.onmousemove = resetTimer; // catches mouse movements
                    window.onmousedown = resetTimer; // catches mouse movements
                    window.onclick = resetTimer;     // catches mouse clicks
                    window.onscroll = resetTimer;    // catches scrolling
                    window.onkeypress = resetTimer;  //catches keyboard actions
                }
                idleTimer();
                
                
            ");

        $view->registerCss("
            
                .edit-popover{
                    min-width:400px;
                    max-width:400px;
                }
                
               /* width */
                .scrollbar::-webkit-scrollbar {
                    width: 5px;
                }

                /* Track */
                .scrollbar::-webkit-scrollbar-track {
                    background: #f1f1f1; 
                }

                /* Handle */
                .scrollbar::-webkit-scrollbar-thumb {
                    background: #888; 
                }

                /* Handle on hover */
                .scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #555; 
                }

            ");
        return $html;
    }

}
