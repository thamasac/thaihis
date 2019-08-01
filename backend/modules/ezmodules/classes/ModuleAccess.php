<?php
 
namespace backend\modules\ezmodules\classes;

class ModuleAccess extends \yii\base\Widget{
    public $id = ''; 
    public function run() {
        parent::run();  
        return $this->JsRegister(); 
    }
    public function JsRegister() {
        $view = $this->getView();
        $accessButton = \backend\modules\ezforms2\classes\EzfAuthFuncManage::auth()->accessManage($this->id, 2);
        $accessButton = (empty($accessButton) || $accessButton == FALSE) ? 0 : 1; 
        //\backend\modules\ezmodules\assets\ModuleAsset::register($view);
        $js = "
            function onLoadings(ele){
                $(ele).waitMe({
                    effect : 'facebook',
                    text : 'Please wait...',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });
            }
            function hideLoadings(ele){
                 $(ele).waitMe('hide');
            }
            deleteButton=function(){
                let ele = '#ezmodule-main-app';
                let access = '".$accessButton."';
                //onLoadings(ele);
                $('.btn-auth-create , .btn-auth-update, .btn-auth-del, .btn-auth-view, .btn-auth-version, .btn-auth-config').attr('disabled', true);
                setTimeout(function(){
                        if(access == '1'){
                            $('.btn-auth-create , .btn-auth-update, .btn-auth-del, .btn-auth-view, .btn-auth-version, .btn-auth-config').remove();
 
//                           $('.table tbody tr td .btn-danger').remove();
                            $('.table thead tr td').remove();                            
                            let span = $('.table thead tr th a');
                            span.replaceWith(function () {
                                return $('<label/>', {
                                    class: 'myClass',
                                    html: this.innerHTML
                                });
                            });
                             
                            //hideLoadings(ele);
                        }else{
                            $('.btn-auth-create , .btn-auth-update, .btn-auth-del, .btn-auth-view, .btn-auth-version, .btn-auth-config').attr('disabled', false);
                            //hideLoadings(ele)
                        }
                }, 1500);
            }
            //deleteButton();

        ";
        
        $view->registerJs($js);
    }
}
