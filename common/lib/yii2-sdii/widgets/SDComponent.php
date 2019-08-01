<?php
namespace appxq\sdii\widgets;
/**
 * SDComponent class file UTF-8
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

class SDComponent extends Select2 {

    public $ezf_id;
    public $ezf_field_id;
    public $modal_size = 'modal-xxl';
    public $target = '';
    public $initdata = 0;
    
    public function init() {
	parent::init();
        
        if ($this->hasModel()) {
            if(isset($this->options['unfiltered']) && $this->options['unfiltered']==1){
                $this->target = '';
            } else {
                $this->target = "{$this->model->target}";
            }
            
        }
        
        $this->pluginOptions['ajax']['url'] = Url::to([$this->pluginOptions['ajax']['url'], 'target'=> $this->target, 'initdata'=> $this->initdata]);
        
    }
    
    protected function renderInput()
    {
        if ($this->pluginLoading) {
            $this->_loadIndicator = '<div class="kv-plugin-loading loading-' . $this->options['id'] . '">&nbsp;</div>';
            Html::addCssStyle($this->options, 'display:none');
        }
        $input = $this->getInput('dropDownList', true);
        
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        $disabled = 0;
        if(isset($this->options['disabled']) && $this->options['disabled']){
            $disabled = 1;
        }
        
        echo '<div class="form-group">';
        echo '<div class="input-group" id="comp-'.$this->ezf_field_id.$this->id.'" >';
        echo $this->_loadIndicator . $this->embedAddon($input);
        echo '<span id="btn-box-'.$this->ezf_field_id.$this->id.'" class="input-group-btn">';  
        if(!$disabled){
            echo Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['data-container'=>'body', 'class'=>'btn btn-default btn-cong btn-auth-config', 'data-active'=>0, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}", 'target'=>$this->target, 'dataid'=>'']), 'data-id'=>$value]).' ';
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
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        } 
        $inputMain = "ez{$this->ezf_id}-id";
        
        $initdata_js = '';
        $resetdata_js = '';
        if($this->initdata){
            //e.params.data
            $fields = \backend\modules\ezforms2\classes\EzfQuery::getFieldsListAllVersion($this->ezf_id);
            if($fields){
                foreach ($fields as $keyf => $valuef) {
                    if(!in_array($valuef['ezf_field_name'], ['id'])){
                        if(in_array($valuef['ezf_field_type'], [61,62])){
                            $initdata_js .= "$('input[name=\"EZ{$this->options['data-ezfid']}[{$valuef['ezf_field_name']}]\"][value=\"' + e.params.data.{$valuef['ezf_field_name']} + '\"]').attr('checked', true); ";
                            $resetdata_js .= "$('input[name=\"EZ{$this->options['data-ezfid']}[{$valuef['ezf_field_name']}]\"]').attr('checked', false); ";
                        } else {
                            
                            $ref_ezf_id = isset($valuef['ref_ezf_id'])?$valuef['ref_ezf_id']:'0';
                            $ref_field_id = isset($valuef['ref_field_id'])?$valuef['ref_field_id']:'id';
                            $initdata_js .= "
                            let input_type_{$valuef['ezf_field_name']} = $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').attr('type');
                            let input_tag_{$valuef['ezf_field_name']} = $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').prop('tagName');
                            if(input_type_{$valuef['ezf_field_name']}=='checkbox'){
                                $('input[name=\"EZ{$this->options['data-ezfid']}[{$valuef['ezf_field_name']}]\"][value=\"' + e.params.data.{$valuef['ezf_field_name']} + '\"]').attr('checked', true);

                            } else if(input_tag_{$valuef['ezf_field_name']}=='SELECT'){
                                if($('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').find('option[value=\"' + e.params.data.{$valuef['ezf_field_name']} + '\"]').length){
                                    $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').val(e.params.data.{$valuef['ezf_field_name']}).trigger('change');
                                } else {
                                    $.ajax({
                                        method: 'POST',
                                        url: '".Url::to(['/ezforms2/select2/get-init-select', 'ezf_id'=>$this->ezf_id, 'ezf_field_id'=>$valuef['ezf_field_id']])."',
                                        data: {dataid:e.params.data.{$valuef['ezf_field_name']}+''},
                                        dataType: 'JSON',
                                        success: function(result, textStatus) {
                                            if(result.id!=''){
                                                let newOption = new Option(result.text, result.id, true, true);
                                                $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').append(newOption).trigger('change');
                                            }
                                        }
                                    });
                                    
                                }
                                
                            } else {
                                $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').val(e.params.data.{$valuef['ezf_field_name']});
                                $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').trigger('change');
                            }
                            
                            ";
                            $resetdata_js .= "
                            let input_type_reset_{$valuef['ezf_field_name']} = $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').attr('type');
                            if(input_type_reset_{$valuef['ezf_field_name']}=='checkbox'){
                                $('input[name=\"EZ{$this->options['data-ezfid']}[{$valuef['ezf_field_name']}]\"]').attr('checked', false);

                            } else {
                                $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').val('');
                                $('#ez{$this->options['data-ezfid']}-{$valuef['ezf_field_name']}').trigger('change');
                            }
                            ";
                        }
                    }
                }
            }
        }
        
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm){
            $('#comp-{$this->ezf_field_id}{$this->id} .btn-cong').hide();
        }
        
        var hasMyModal = $( 'body' ).has( '#modal-{$this->ezf_field_id}' ).length;
        var hasMainModal = $( 'body' ).has( '#modal-fix-{$this->ezf_field_id}' ).length;
        
        //setTimeout(function(){ 
            if($('body .modal').hasClass('in')){
                if(hasMyModal==0){
                    $('#ezf-modal-box').append('$submodal');
                }
            } else {
                if(hasMainModal==0){
                    $('#ezf-fix-modal-box').append('$submodalFix');
                    $('#comp-{$this->ezf_field_id}{$this->id} .btn-cong').attr('data-url', '".Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-fix-{$this->ezf_field_id}", 'target'=>$this->target, 'dataid'=>''])."');    
                }
            }
        //}, 500);
        
        $('#comp-{$this->ezf_field_id}{$this->id} select').change(function(e){
            if($('.btn-cong').attr('data-active')==1){
                var url = $('.btn-cong').attr('data-url');
                var dataid = $(this).val();
                if(dataid!=''){
                    url = url+dataid;
                } 

                $('#btn-box-{$this->ezf_field_id}{$this->id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $('#btn-box-{$this->ezf_field_id}{$this->id}').load(url);
                
            }
           
        });
        
        $('#comp-{$this->ezf_field_id}{$this->id} select').on('select2:select', function(e){
            $resetdata_js
            $initdata_js
        });

        $('#comp-{$this->ezf_field_id}{$this->id} select').on('select2:unselect', function(e){
            $resetdata_js
        });
        
        $('#comp-{$this->ezf_field_id}{$this->id}').on('click', '.btn-cong', function(){
            var url = $(this).attr('data-url');
            var dataid = $('#comp-{$this->ezf_field_id}{$this->id} select').val();
            if(dataid!=''){
                url = url+dataid;
            } 
            
            $('#btn-box-{$this->ezf_field_id}{$this->id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#btn-box-{$this->ezf_field_id}{$this->id}').load(url);
            
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
