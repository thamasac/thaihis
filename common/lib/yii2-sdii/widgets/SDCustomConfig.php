<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\assets\CroppieAssets;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

class SDCustomConfig extends InputWidget
{
    public $ui_temp = '<div id="{widgetId}" data-depend="{depend}" class="alert alert-{theme}">
                            <div class="pull-right text-right">{create}</div>
                            <h3 class="page-header">{title}</h3>
                            {header}
                            <div class="content-items">{content}</div>
                      </div>
                    ';
    
    public $theme = 'default';
    
    public $title = '';
    public $widget_path = '';
    public $ezf_id = 0;
    public $depend = '';

    public function init()
    {
        parent::init();
	
	if(!isset($this->options['widgetId'])){
	    $this->options['widgetId'] = 'widget';
	} 
	
	$id = $this->options['id'];
	$this->options['widgetId'] = $this->options['widgetId'].'_'.$id;
	
    }
    
    public function run()
    {
	$inputID;
	$inputValue;
        $inputName;
        $varname;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
            $inputName = Html::getInputName($this->model, $this->attribute);
	    $inputValue = Html::getAttributeValue($this->model, $this->attribute);
            $varname = $this->attribute;
        } else {
	    $inputID = $this->name;
            $inputName = $this->name;
	    $inputValue = $this->value;
            $varname = $this->name;
        }
        
	$template = $this->ui_temp;
	
//	$html='';
//        if ($this->hasModel()) {
//            $html = Html::activeHiddenInput($this->model, $this->attribute);
//        } else {
//            $html = Html::hiddenInput($this->name, $this->value, ['id'=>$this->name]);
//        }
        $widget_path = '';
        if(isset($this->widget_path)){
            $widget_path = $this->widget_path;
        }
        
        $view = $this->getView();
        
        $header = '';
        if ($this->hasModel()) {
            $header = $view->renderAjax($widget_path, [
                    'ezf_id' => $this->ezf_id,
                    'ezf_field_name' => $varname,
                    'depend' => $this->depend,
                    'header' => 1,
                ]);
        }
        
        $content = '';
        if(isset($inputValue) && !empty($inputValue)){
            foreach ($inputValue as $key => $value) {
                $content .= $view->renderAjax($widget_path, [
                    'ezf_id' => $this->ezf_id,
                    'ezf_field_name' => $varname,
                    'options'=>$value,
                    'index_id' => $key,
                    'depend' => $this->depend,
                ]);
            }
        }
        echo Html::hiddenInput($inputName, '');
	echo strtr($template, [
		'{widgetId}' => $this->options['widgetId'],
		'{theme}' => isset($this->theme) && !empty($this->theme)?$this->theme:'default',
		'{create}' => Html::button('<i class="glyphicon glyphicon-plus"></i>', [
                    'class'=>'btn btn-success btn-sm btn-add', 
                    'data-url'=> Url::to(['/ezforms2/custom-config/get-widget']),
                    'data-widget'=>$widget_path, 
                    'data-ezf_id'=>$this->ezf_id, 
                    'data-depend' => $this->depend,
                    'data-ezf_field_name'=>$varname,
                    ]),
		'{title}' => isset($this->title) && !empty($this->title)?$this->title:'',
                '{header}' => $header,
                '{content}' => $content,
                '{depend}' => $this->depend,
	]);
	
        $this->registerClientScript();
    }

    
    public function registerClientScript() {
	$view = $this->getView();
	
	$inputID;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
        }
	
        
	$view->registerJs("
        $('#{$this->options['widgetId']} .btn-add').click(function(){
            let url = $(this).attr('data-url');
            let widget = $(this).attr('data-widget');
            let ezf_id = $(this).attr('data-ezf_id');
            let ezf_field_name = $(this).attr('data-ezf_field_name');
            let depend = $(this).attr('data-depend');
            
            $.ajax({
                method: 'POST',
                url: url,
                data:{widget:widget, ezf_id:ezf_id, ezf_field_name:ezf_field_name, depend:depend},
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#{$this->options['widgetId']} .content-items').append(result);
                }
            });
        });
        
        $('#{$this->options['widgetId']} ').on('click', '.del-items', function(){
            $(this).parent().parent().remove();
        });
        
	");
    }
}
