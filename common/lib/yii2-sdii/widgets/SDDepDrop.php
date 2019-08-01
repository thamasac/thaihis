<?php
namespace appxq\sdii\widgets;
/**
 * SDDepDrop class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class SDDepDrop extends DepDrop {

    public $ezf_id;
    public $ezf_field_id;
    public $modal_size = 'modal-xxl';

    public function init() {
	parent::init();
        
//        if(count($this->id)<10){
//            $this->id = 'auto'.\appxq\sdii\utils\SDUtility::getMillisecTime();
//        }
        //$this->options['id'] = $this->id;
        if ($this->hasModel()) {
            $dependId = isset($this->options['data-depend'])?Html::getInputId($this->model, $this->options['data-depend']):0;
            $inputId = Html::getInputId($this->model, $this->attribute);
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $dependId = isset($this->options['data-depend'])?$this->id:0;
            $inputId = $this->id;
            $value = $this->value;
        }

        $this->type = \kartik\depdrop\DepDrop::TYPE_SELECT2;
        $inputHideId = "$inputId-hide";
        
        $btn_cog = Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['data-container'=>'body', 'class'=>'btn btn-default btn-cong btn-auth-config', 'data-active'=>0, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}", 'dataid'=>'']), 'data-id'=>$value]).' ';
        
        $this->pluginOptions['initialize'] = $this->pluginOptions['initialize']==1;
        if($this->pluginOptions['initialize'] && isset($this->options['data-parent']) && !empty($this->options['data-parent']) && isset($this->options['data-ezfid']) && !empty($this->options['data-ezfid'])){
            $this->pluginOptions['initDepends'] = ["ez{$this->options['data-ezfid']}-".$this->options['data-parent']];
        }
        $this->pluginOptions['depends'] = [$dependId];
        $this->pluginOptions['params'] = [$inputHideId];
        $this->pluginOptions['url'] = \yii\helpers\Url::to(['/ezforms2/ezform/get-depdrop', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id]);
        $this->pluginEvents = [
		    "select2:select" => "function(e) {
                        $('#$inputHideId').val(e.params.data.id); 
                            if($('#btn-box-{$this->ezf_field_id}{$this->id} .btn-cong').attr('data-active')==1){
                                $('#btn-box-{$this->ezf_field_id}{$this->id} .btn-cong').click();
                            }
                            
                    }",
		    "select2:unselect" => "function() { 
                        $('#$inputHideId').val(''); 
                            if($('#btn-box-{$this->ezf_field_id}{$this->id} .btn-cong').attr('data-active')==1){
                                var url = $('#btn-box-{$this->ezf_field_id}{$this->id} .btn-cong').attr('data-url');
                                var dataid = '';
                                var target = $('#$dependId').val();
                                if(dataid!=''){
                                    url = url+dataid;
                                } 

                                $('#btn-box-{$this->ezf_field_id}{$this->id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                                $('#btn-box-{$this->ezf_field_id}{$this->id}').load(url, {target:target});
                            }
                    }",
                    "depdrop:change" => "function() { 
                        $('#$inputHideId').val($(this).val()); 
                        if($('#$dependId').val() != ''){
                            $('#btn-box-{$this->ezf_field_id}{$this->id}').show();
                            $('#btn-box-{$this->ezf_field_id}{$this->id}').html('$btn_cog');
                            
                        } else {
                            $('#btn-box-{$this->ezf_field_id}{$this->id}').hide();
                        }
                }",
		];
    }
    
    public function registerAssets()
    {
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
            $inputId = Html::getInputId($this->model, $this->attribute);
        } else {
            $value = $this->value;
            $inputId = $this->id;
        }
        $disabled = 0;
        $widgetName = 'ezform';
        if(isset($this->options['disabled']) && $this->options['disabled']==1){
            $disabled = 1;
            $widgetName = 'ezform-view';
        } elseif(isset($this->options['disabled']) && $this->options['disabled']==2){
            $disabled = 1;
            //$widgetName = 'ezform-view';
        }
        
        $inputHide = "$inputId-hide";
        
        $view = $this->getView();
        \kartik\depdrop\DepDropAsset::register($view)->addLanguage($this->language, 'depdrop_locale_');
        \kartik\depdrop\DepDropExtAsset::register($view);
        $this->registerPlugin($this->pluginName);
        
        echo '<div class="form-group">';
        echo '<div class="input-group" id="comp-'.$this->ezf_field_id.$this->id.'" >';
        if ($this->type === self::TYPE_SELECT2) {
            $loading = ArrayHelper::getValue($this->pluginOptions, 'loadingText', 'Loading ...');
            $this->select2Options['data'] = $this->data;
            $this->select2Options['options'] = $this->options;
            if ($this->hasModel()) {
                $settings = ArrayHelper::merge($this->select2Options, [
                    'model' => $this->model,
                    'attribute' => $this->attribute
                ]);
            } else {
                $settings = ArrayHelper::merge($this->select2Options, [
                    'name' => $this->name,
                    'value' => $this->value
                ]);
            }
            echo \kartik\select2\Select2::widget($settings);
            $id = $this->options['id'];
            $view->registerJs("initDepdropS2('{$id}','{$loading}');");
        } else {
            echo $this->getInput('dropdownList', true);
        }
        
        echo Html::hiddenInput($inputHide, $value, ['id'=>$inputHide]);
        //echo Html::hiddenInput('ref_field_id-'.$inputHide, $value, ['id'=>'ref_field_id-'.$inputHide]);
        
        echo '<span id="btn-box-'.$this->ezf_field_id.$this->id.'" class="input-group-btn">';  
        if(!$disabled){
            echo Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['data-container'=>'body', 'class'=>'btn btn-default btn-cong btn-auth-config', 'data-active'=>0, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}", 'dataid'=>'']), 'data-id'=>$value]).' ';
        }
        echo '</span>';
        echo '</div>';
        echo '</div> ';
        
        $this->registerJs();
    }
    
    protected function registerJs()
    {
        $view = $this->getView();
        $submodal = '<div id="modal-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        $submodalFix = '<div id="modal-fix-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        
        $inputId = $this->id;
        $dependId = isset($this->options['data-depend'])?$this->id:0;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
            $dependId = isset($this->options['data-depend'])?Html::getInputId($this->model, $this->options['data-depend']):0;
        } 
        $inputMain = "ez{$this->ezf_id}-id";
        $inputHide = "$inputId-hide";
        
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm){
            $('#comp-{$this->ezf_field_id}{$this->id} .btn-cong').hide();
        }
        
        var hasMyModal = $( 'body' ).has( '#modal-{$this->ezf_field_id}' ).length;
        var hasMainModal = $( 'body' ).has( '#modal-fix-{$this->ezf_field_id}' ).length;
        
        setTimeout(function(){ 
            if($('body .modal').hasClass('in')){
                if(hasMyModal==0){
                    $('#ezf-modal-box').append('$submodal');
                }
            } else {
                if(hasMainModal==0){
                    $('#ezf-fix-modal-box').append('$submodalFix');
                    $('#comp-{$this->ezf_field_id}{$this->id} .btn-cong').attr('data-url', '".Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-fix-{$this->ezf_field_id}", 'dataid'=>''])."');    
                }
            }
        }, 500);
        
        $('#comp-{$this->ezf_field_id}{$this->id}').on('click', '.btn-reload', function(){
            $('#$dependId').trigger('depdrop:change');
        });
        
        $('#comp-{$this->ezf_field_id}{$this->id}').on('click', '.btn-cong', function(){
            var url = $(this).attr('data-url');
            var dataid = $('#comp-{$this->ezf_field_id}{$this->id} select').val();
            var target = $('#$dependId').val();    
            if(dataid!=''){
                url = url+dataid;
            } 
            
                
            $('#btn-box-{$this->ezf_field_id}{$this->id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#btn-box-{$this->ezf_field_id}{$this->id}').load(url, {target:target});
            
        });
        
        $('#comp-{$this->ezf_field_id}{$this->id}').on('click', '.btn-edit', function(){
            var url = $(this).attr('data-url');
            var dataid = $(this).attr('data-id');
            if(dataid!=''){
                url = url+dataid;
            } 
            
            if($('body .modal').hasClass('in')){
                modal_{$this->ezf_field_id}(url);
            } else {
                modal_fix_{$this->ezf_field_id}(url);
            }
        });
        
        $('#comp-{$this->ezf_field_id}{$this->id}').on('click', '.btn-add', function(){
            var url = $(this).attr('data-url');
            
            if($('body .modal').hasClass('in')){
                modal_{$this->ezf_field_id}(url);
            } else {
                modal_fix_{$this->ezf_field_id}(url);
            }
        });
        
        $('#modal-{$this->ezf_field_id}').on('hidden.bs.modal', function(e){
            $('#modal-{$this->ezf_field_id} .modal-content').html('');
                
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
        });
        
        $('#modal-fix-{$this->ezf_field_id}').on('hidden.bs.modal', function(e){
            $('#modal-fix-{$this->ezf_field_id} .modal-content').html('');
                
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } else {
                $('#ezf-modal-box').html('');
            }
        });

        function modal_{$this->ezf_field_id}(url) {
            $('#modal-{$this->ezf_field_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-{$this->ezf_field_id}').modal('show')
            .find('.modal-content')
            .load(url);
        }
        
        function modal_fix_{$this->ezf_field_id}(url) {
            
            $('#modal-fix-{$this->ezf_field_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-fix-{$this->ezf_field_id}').modal('show')
            .find('.modal-content')
            .load(url);
        }

        ");
    }
}
