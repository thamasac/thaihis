<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\assets\CroppieAssets;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

class SDUploadIcon extends InputWidget
{
    public $ui_temp = '<div id="{widgetId}" class="upload-croppie">
                          {fileInput}
                          {hiddenInput}
                          <div class="upload-msg">
                              {img}
                          </div>
                          <div class="upload-edit"></div>
                          <div class="text-center upload-action">
                            <a class="btn btn-success save-upload">Save Icon</a>
                          </div>
                      </div>
    ';

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
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
	    $inputValue = Html::getAttributeValue($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
	    $inputValue = $this->value;
        }
	
	$template = $this->ui_temp;
	
	$html='';
        if ($this->hasModel()) {
            $html = Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            $html = Html::hiddenInput($this->name, $this->value, ['id'=>$this->name]);
        }
        
	echo strtr($template, [
		'{widgetId}' => $this->options['widgetId'],
		'{fileInput}' => Html::fileInput($inputID.'-croppie', null, ['id'=>$inputID.'-croppie', 'class' => 'upload-input']),
		'{img}' => Html::img($inputValue, ['class'=>'img-rounded preview_icon']),
		'{hiddenInput}' => $html,
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
	
	CroppieAssets::register($view);
        
        $view->registerCss("
        .upload-croppie .upload-edit, .upload-croppie .upload-action,
        .upload-croppie .upload-result,
        .upload-croppie.ready .upload-msg {
            display: none;
        }
        .upload-croppie.ready .upload-action {
            display: block;
        }
        .upload-croppie.ready .upload-edit {
            display: block;
        }
        .upload-croppie.ready .upload-result {
            display: inline-block;
        }
        .upload-msg {
            width: 260px;
            margin: 50px auto;
        }
        ");
        
	$view->registerJs("
            function initializeCroppie() {
                let uploadCrop;

                function readFile(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            uploadCrop.croppie('bind', {
                                    url: e.target.result
                            });
                            $('#{$this->options['widgetId']}.upload-croppie').addClass('ready');
                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                    else {
                        swal(\"Sorry - you're browser doesn't support the FileReader API\");
                    }
                }

                uploadCrop = $('#{$this->options['widgetId']} .upload-edit').croppie({
                    enableExif: true,
                    viewport: {
                        width: 150,
                        height: 150,
                        type: 'square' //square, circle
                    },
                    boundary: {
                        width: 300,
                        height: 300
                    }
                });

                $('#$inputID-croppie').on('change', function () { readFile(this); });

                $('#{$this->options['widgetId']} .save-upload').on('click', function() {
                    uploadCrop.croppie('result', {
                        type: 'canvas',
                        size: 'viewport'
                    }).then(function (resp) {
                        //alert( resp );
                        $('#$inputID').val(resp);
                        $('#{$this->options['widgetId']} .preview_icon').attr('src',resp);
                        $('#{$this->options['widgetId']}.upload-croppie').removeClass('ready');
                        $('#$inputID').trigger('change');
                    });
                    return false;
                });
            }
	    
            initializeCroppie();
	");
    }
}
