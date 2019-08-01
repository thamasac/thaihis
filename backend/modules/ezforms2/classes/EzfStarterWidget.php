<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
/**
 * Description of EzfStarterWidget
 *
 * @author appxq
 */
class EzfStarterWidget extends \yii\base\Widget {

    public $key = 'AIzaSyCq1YL-LUao2xYx3joLEoKfEkLXsEVkeuk';
    public $options;
    public $modal_ezform = true;
    //popup ezform, delete ezform, grid ezform, addon ezform
    public function init() {
        parent::init();

        $this->initOptions();
        
        echo Html::beginTag('div', ['id'=>'ezf-main-box']);
            echo Html::beginTag('div', ['id'=>'ezf-main-app']);
            
    }

    public function run() {
            echo Html::endTag('div');// ezf-main-app
            echo ModalForm::widget([
                'id' => 'modal-ezform-calendar',
                //'size' => 'modal-lg',
            ]);
            
            echo ModalForm::widget([
                'id' => 'modal-ezform-main-xl',
                'size' => 'modal-xxl',
            ]);
            
            echo ModalForm::widget([
                'id' => 'modal-ezform-main-lg',
                'size' => 'modal-lg',
            ]);
            
            echo ModalForm::widget([
                'id' => 'modal-ezform-main-md',
                //'size' => 'modal-lg',
            ]);
            
            echo ModalForm::widget([
                'id' => 'modal-ezform-main',
                'size' => 'modal-xxl',
                'tabindexEnable' => false,
            ]);
            echo Html::beginTag('div', ['id'=>'ezf-fix-modal-box']);
            echo Html::endTag('div');// ezf-fix-modal-box
            echo Html::beginTag('div', ['id'=>'ezf-modal-box']);
            echo Html::endTag('div');// ezf-modal-box
            
            echo ModalForm::widget([
                'id' => 'modal-ezform-info',
                'size' => 'modal-xxl',
                'tabindexEnable' => false,
            ]);
            echo ModalForm::widget([
                'id' => 'modal-ezform-community',
                'size' => 'modal-lg',
                'tabindexEnable' => false,
            ]);
            
            if($this->modal_ezform){
                echo ModalForm::widget([
                    'id' => 'modal-ezform',
                    'size' => 'modal-lg',
                    'tabindexEnable' => false,
                ]);
            }
            
        echo Html::endTag('div'); // ezf-main-box
        
        //$this->registerMap();
        $this->registerEzform();
        $this->registerJs();
    }
    
    protected function initOptions() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
    }
    
    protected function registerMap()
    {
        $view = $this->getView();
        
        //$op['sensor'] = $this->sensor;
	if($this->key!=''){
	    $op['key'] = $this->key;
	}
	$op['language'] = 'th';
	
	$q = array_filter($op);

        $view->registerJsFile('https://maps.google.com/maps/api/js?'.http_build_query($q), [
	    'position'=>\yii\web\View::POS_HEAD,
	    'depends'=>'yii\web\YiiAsset',
	]);
    }
    
    protected function registerEzform()
    {
        $view = $this->getView();
        
        \backend\modules\ezforms2\assets\EzfAsset::register($view);
        \backend\modules\ezforms2\assets\EzfGenAsset::register($view);
    }
    
    public function registerJs()
    {
        $view = $this->getView();
        $view->registerJs("
            
        $('body').on('hidden.bs.modal', function (e) {
            $('.daterangepicker').remove();
            $('.fr-popup').remove();
        });
        
        $('#modal-ezform-main-xl').on('hidden.bs.modal', function(e){
            $('#modal-ezform-main-xl .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-main-lg').on('hidden.bs.modal', function(e){
            $('#modal-ezform-main-lg .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-main-md').on('hidden.bs.modal', function(e){
            $('#modal-ezform-main-md .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-community').on('hidden.bs.modal', function(e){
            $('#modal-ezform-community .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-info').on('hidden.bs.modal', function(e){
            $('#modal-ezform-info .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-calendar').on('hidden.bs.modal', function(e){
            $('#modal-ezform-calendar .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform').on('hidden.bs.modal', function(e){
            $('#modal-ezform .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-ezform-main').on('hidden.bs.modal', function(e){
            $('#ezf-modal-box').html('');
            $('#modal-ezform-main .modal-content').html('');
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#ezf-main-box').on('click', '.ezform-main-open', function(){
            var url = $(this).attr('data-url');
            var modal = $(this).attr('data-modal');
            
            var lat = $(this).attr('data-lat');
            var lng = $(this).attr('data-lng');
            var lat_field = $(this).attr('data-lat-field');
            var lng_field = $(this).attr('data-lng-field');
            
            if(lat && lng){
                var data_set = {};
                data_set[lat_field] = lat;
                data_set[lng_field] = lng;
                
                data_set = btoa(JSON.stringify(data_set));
                modalEzformMain(url+data_set, modal);
            } else {
                modalEzformMain(url, modal);
            }
            return false;
        });
        
        $('#ezf-main-box').on('click', '.ezform-create', function(){
            var url = $(this).attr('data-url');
            var modal = $(this).attr('data-modal');
            
            modalEzformMain(url, modal);
        });
        
        $('#ezf-main-box').on('click', '.btn-querytool', function(){
            var url = $(this).attr('data-url');
            var modal = 'modal-ezform-community';
            
            modalEzformMain(url, modal);
        });
        
        $('#ezf-main-box').on('click', '.ezform-delete', function(){
            var url = $(this).attr('data-url');
            var url_reload = $(this).attr('data-url-reload');
            
            yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
                $.post(
                        url, {'_csrf':'".Yii::$app->request->getCsrfToken()."'}
                ).done(function(result){
                        if(result.status == 'success'){
                                ". SDNoty::show('result.message', 'result.status') ."
                                var urlreload =  $('#'+result.reloadDiv).attr('data-url');
                                if(urlreload){
                                    getUiAjax(urlreload, result.reloadDiv);
                                }
                        } else {
                                ". SDNoty::show('result.message', 'result.status') ."
                        }
                }).fail(function(){
                        ". SDNoty::show("'" . "Server Error'", '"error"') ."
                        console.log('server error');
                });
            });
        });
        
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
            
        function modalEzformMain(url, modal) {
            $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#'+modal).modal('show')
            .find('.modal-content')
            .load(url);
        }

        ");
    }

}
