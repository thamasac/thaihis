<?php
namespace appxq\sdii\widgets;
/**
 * SDProvince class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

class SDTarget extends Select2 {

    public $ezf_id;
    public $ezf_field_id;
    public $modal_size = 'modal-xxl';
    
    public function init() {
	parent::init();
        
//        if(count($this->id)<10){
//            $this->id = 'auto'.\appxq\sdii\utils\SDUtility::getMillisecTime();
//        }
        //$this->options['id'] = $this->id;
    }
    
    protected function renderInput()
    {
        if ($this->pluginLoading) {
            $this->_loadIndicator = '<div class="kv-plugin-loading loading-' . $this->options['id'] . '">&nbsp;</div>';
            Html::addCssStyle($this->options, 'display:none');
        }
        $input = $this->getInput('dropDownList', true);
        $inputId = $this->id;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
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
        
        echo '<div class="form-group">';
        echo '<div class="input-group" id="target-'.$this->ezf_field_id.$inputId.'">';
        echo $this->_loadIndicator . $this->embedAddon($input);
        echo '<span class="input-group-btn">';   
        echo Html::button('<i class="glyphicon glyphicon-eye-open"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('ezform', 'Open Form'), 'class'=>'btn btn-primary btn-open-ezform btn-edit btn-auth-update', 'data-url'=>Url::to(['/ezforms2/ezform-data/'.$widgetName, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}", 'dataid'=>'']), 'data-id'=>$value, 'style'=>$value>0?'':'display: none;']).' ';
        if(!$disabled){
            echo Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle'=>'tooltip', 'title'=> Yii::t('app', 'New'), 'class'=>'btn btn-success btn-open-ezform btn-add btn-auth-create', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}" ]) ]).' ';
        }
        echo '</span>';
        echo '</div>';
        echo '</div> ';
        $this->registerJs();
    }
    
    protected function registerJs()
    {
        $disabled = 0;
        $widgetName = 'ezform';
        if(isset($this->options['disabled']) && $this->options['disabled']==1){
            $disabled = 1;
            $widgetName = 'ezform-view';
        } elseif(isset($this->options['disabled']) && $this->options['disabled']==2){
            $disabled = 1;
            //$widgetName = 'ezform-view';
        }
        
        $view = $this->getView();
        $submodal = '<div id="modal-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        $submodalFix = '<div id="modal-fix-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        
        $inputId = $this->id;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        } 
        $inputMain = "ez{$this->ezf_id}-id";
        
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm){
            $('#target-{$this->ezf_field_id}{$inputId} .btn-open-ezform').hide();
                console.log(55);
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
                    $('#target-{$this->ezf_field_id}{$inputId} .btn-edit').attr('data-url', '".Url::to(['/ezforms2/ezform-data/'.$widgetName, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-fix-{$this->ezf_field_id}", 'dataid'=>''])."');    
                    $('#target-{$this->ezf_field_id}{$inputId} .btn-add').attr('data-url', '".Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-fix-{$this->ezf_field_id}" ])."');
                }
            }
            
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
        }, 500);
        
        $('#target-{$this->ezf_field_id}{$inputId} select').change(function(){
            var \$form = $('#ezform-'+$(this).attr('data-ezfid'));
            var url = \$form.attr('action');
            
            if(\$form.attr('data-modal')!=''){
                $.ajax({
                   method: 'GET',
                   url: url,
                   data:{target:$(this).val()},
                   dataType: 'HTML',
                   success: function(result, textStatus) {
                       $('#'+\$form.attr('data-modal')+' .modal-content').html(result);
                   }
               });
            } else {
                location.href = url
            }
           
        });
        
        $('#target-{$this->ezf_field_id}{$inputId} .btn-open-ezform').click(function(){
            var url = $(this).attr('data-url');
            var dataid = $(this).attr('data-id');
            
            if(dataid){
                url = url+dataid;
            } 
            
            if($('body .modal').hasClass('in')){
                modal_{$this->ezf_field_id}(url);
            } else {
                modal_fix_{$this->ezf_field_id}(url);
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
