<?php
namespace appxq\sdii\widgets;
/**
 * SDSubSelect class file UTF-8
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

class SDSubSelect extends \yii\widgets\InputWidget {

    public $ezf_id;
    public $ezf_field_id;
    public $input_box;
    public $multiple = 0;
    public $url;
    public $ref_id;

    public function init() {
	parent::init();
        
        if ($this->hasModel()) {
            $this->input_box = 'ssbox_'. str_replace('-', '_', Html::getInputId($this->model, $this->attribute));
        } else {
            $this->input_box = 'ssbox_'.$this->name;
        }
        
        $this->renderInput();
    }
    
    protected function renderInput()
    {
        
        if ($this->hasModel()) {
            $name = Html::getInputName($this->model, $this->attribute);
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $name = $this->name;
            $value = $this->value;
        }
        
        echo Html::tag('div', '', ['id'=>$this->input_box.'-'.$this->id]);
        
        $this->registerJs($name, $value);
    }
    
    protected function registerJs($name, $value)
    {
        $view = $this->getView();
        
        if(isset($this->ezf_id)){
            $ref_id = isset($this->ref_id)?'ez'.$this->ezf_id.'-'.$this->ref_id:'';
        } else {
            $ref_id = isset($this->ref_id)?$this->ref_id:'';
        }
        
        $changeJs = '';
        if($ref_id != ''){
            $changeJs ="getFields_{$this->input_box}_{$this->id}($('#{$ref_id}').val());
             
            $('#{$ref_id}').on('change',function(){
                var ref_id = $(this).val();
                getFields_{$this->input_box}_{$this->id}(ref_id);
            });";
        }
        
        $view->registerJs("
            $changeJs
            function getFields_{$this->input_box}_{$this->id}(ref_id){
                $.post('{$this->url}',{ ref_id: ref_id, multiple:{$this->multiple}, name: '{$name}', value: '{$value}' ,id:'fields-{$this->input_box}-{$this->id}'}
                  ).done(function(result){
                     $('#{$this->input_box}-{$this->id}').html(result);
                  }).fail(function(){
                      ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
                      console.log('server error');
                  });
            }
        ");
    }
}
