<?php
namespace appxq\sdii\widgets;
/**
 * SDInputCid class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

class SDInputCid extends MaskedInput {

    public $ezf_id;
    public $ezf_field_id;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            $input = Html::activeInput($this->type, $this->model, $this->attribute, $this->options);
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $input = Html::input($this->type, $this->name, $this->value, $this->options);
            $value = $this->value;
        }
        
        $inputId = $this->id;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        } 
        $this->ezf_field_id = $inputId;
        
        $disabled = 0;
        if(isset($this->options['disabled']) && $this->options['disabled']){
            $disabled = 1;
        } 

        $initdata = Yii::$app->request->get('initdata', '');
        
        echo '<div class="form-group">';
        if(empty($value)){
            echo '<div class="input-group" id="special-'.$this->ezf_field_id.'" >';
            echo $input;
            echo '<span class="input-group-btn">';   
            if(!$disabled){
                echo Html::button('<i class="glyphicon glyphicon-search"></i> ', ['data-toggle'=>'tooltip', 'class'=>'btn btn-default btn-cid', 'data-foreigner'=> 0, 'data-init'=>$initdata, 'data-url'=>Url::to(['/ezforms2/target/checkcid'])]).' ';//, 'style'=>'display: none;'
                echo Html::button('<i class="glyphicon glyphicon-plane"></i> '.Yii::t('ezform', 'Foreigner Registration'), ['data-toggle'=>'tooltip', 'class'=>'btn btn-warning btn-cid', 'data-foreigner'=> 1, 'data-init'=>$initdata, 'data-url'=>Url::to(['/ezforms2/target/checkcid'])]).' ';
            }
            echo '</span>';
            echo '</div>';
            
        } else {
            echo $input;
        }
        echo '</div> ';
        
        $this->registerJs();
    }
    
    protected function registerJs()
    {
        $view = $this->getView();
        
        $inputId = $this->id;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        } 
        
        $view->registerJs("
                
        $('#special-{$this->ezf_field_id} .btn-cid').click(function(){
            var foreigner = $(this).attr('data-foreigner'); 
            var ezf_id = $('#$inputId').attr('data-ezfid');
            var ezf_field_id = $('#$inputId').attr('data-ezf_field_id');
            var url = $(this).attr('data-url');
            var error = $('#$inputId').attr('aria-invalid');
            var initdata = $(this).attr('data-init');    
                
            if(foreigner==1){
                $.ajax({
                    method: 'GET',
                    url: '" . Url::to(['/ezforms2/target/gencid']) . "',
                    data:{ezf_id:ezf_id, ezf_field_id:ezf_field_id, initdata:initdata },
                    dataType: 'JSON',
                    success: function(result, textStatus) {
                        if(result.status == 'success') {
                            $('#$inputId').val(result.data);
                            var cid = parseInt($('#$inputId').val());
                            getcid(cid, ezf_id, ezf_field_id, url);    
                        } else {
                            " . SDNoty::show('result.message', 'result.status') . "
                        }
                    }
                });
            } else {
                var cid = parseInt($('#$inputId').val());
                getcid(cid, ezf_id, ezf_field_id, url, initdata);
            }
            
        });
        
        function getcid(cid, ezf_id, ezf_field_id, url, initdata){
            var \$form = $('#ezform-'+$('#$inputId').attr('data-ezfid'));
            var selection_div = \$form.attr('data-modal')!=''?\$form.attr('data-modal'):\$form.attr('data-reloaddiv');
            
            if(cid>0){
                $.ajax({
                    method: 'GET',
                    url: url,
                    data:{cid:cid, ezf_id:ezf_id, ezf_field_id:ezf_field_id , initdata:initdata},
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#'+selection_div+' #error-alert-ezf').html(result);
                    }
                });
            } else {
                $('#'+selection_div+' #error-alert-ezf').html('');
            }
        }
        
        ");
    }
}
