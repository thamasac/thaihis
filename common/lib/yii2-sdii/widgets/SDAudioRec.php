<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\assets\audio\AudioAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

class SDAudioRec extends InputWidget
{
    public $ui_temp = '<div id="{widgetId}">
	<button id="{startBtn}" class="btn btn-danger"><i class="glyphicon glyphicon-record"></i> Rec</button>
	<button id="{stopBtn}" class="btn btn-primary" disabled><i class="glyphicon glyphicon-stop"></i> Stop</button>
	<div class="row">
	    <div class="col-md-12">
		<div id="{audio-container}">{audio-item}</div>
	    </div>
	</div>
    </div>
    ';

    public function init()
    {
        parent::init();
	
	if(!isset($this->options['widgetId'])){
	    $this->options['widgetId'] = 'widget';
	} 
	
	if(!isset($this->options['startBtn'])){
	    $this->options['startBtn'] = 'start';
	} 
	
	if(!isset($this->options['stopBtn'])){
	    $this->options['stopBtn'] = 'stop';
	} 
	
	if(!isset($this->options['audio-container'])){
	    $this->options['audio-container'] = 'audio-container';
	}
	
	if(!isset($this->options['mediaRecorder'])){
	    $this->options['mediaRecorder'] = 'mediaRecorder';
	} 
	
	if(!isset($this->options['mediaConstraints'])){
	    $this->options['mediaConstraints'] = 'mediaConstraints';
	} 
	
	if(!isset($this->options['onMediaSuccess'])){
	    $this->options['onMediaSuccess'] = 'onMediaSuccess';
	} 
	
	if(!isset($this->options['saveUrl'])){
	    $this->options['saveUrl'] = Url::to(['//ezforms/audio/save']);
	}
	
	$id = $this->options['id'];
	$this->options['widgetId'] = $this->options['widgetId'].'_'.$id;
	$this->options['startBtn'] = $this->options['startBtn'].'_'.$id;
	$this->options['stopBtn'] = $this->options['stopBtn'].'_'.$id;
	$this->options['audio-container'] = $this->options['audio-container'].'_'.$id;
	
	$this->options['mediaRecorder'] = str_replace('-', '_', $this->options['mediaRecorder'].'_'.$id);
	$this->options['onMediaSuccess'] = str_replace('-', '_', $this->options['onMediaSuccess'].'_'.$id);
	
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
	
	$html = '';
	if(isset($inputValue) && $inputValue!=''){
	    $path = Yii::getAlias('@storageUrl').'/audio/'.$inputValue;
	    $html = '<audio controls="" src="'.$path.'"></audio>';
	}
	
	echo strtr($template, [
		'{widgetId}' => $this->options['widgetId'],
		'{startBtn}' => $this->options['startBtn'],
		'{stopBtn}' => $this->options['stopBtn'],
		'{audio-container}' => $this->options['audio-container'],
		'{audio-item}' => $html,
	    ]);
	
	
	
        $this->registerClientScript();
	
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value, ['id'=>$this->name]);
        }
	
    }

    
    public function registerClientScript() {
	$view = $this->getView();
	
	$inputID;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
        }
	
	AudioAsset::register($view);
	
	$view->registerJs("
	    var ".$this->options['mediaRecorder'].";

            document.querySelector('#".$this->options['startBtn']."').onclick = function() {
                this.disabled = true;
		$('#".$this->options['audio-container']."').html('Recording <i class=\"sdloader-icon\"></i>');
                captureUserMedia({audio: true}, ".$this->options['onMediaSuccess'].", onMediaError);
            };
	    
            document.querySelector('#".$this->options['stopBtn']."').onclick = function() {
                this.disabled = true;
                ".$this->options['mediaRecorder'].".stop();
                ".$this->options['mediaRecorder'].".stream.stop();
                document.querySelector('#".$this->options['startBtn']."').disabled = false;
            };

            function ".$this->options['onMediaSuccess']."(stream) {
                ".$this->options['mediaRecorder']." = new MediaStreamRecorder(stream);
                ".$this->options['mediaRecorder'].".stream = stream;
                ".$this->options['mediaRecorder'].".sampleRate = 22050;
                ".$this->options['mediaRecorder'].".recorderType = MediaRecorderWrapper;
                ".$this->options['mediaRecorder'].".mimeType = 'audio/ogg';
		
                ".$this->options['mediaRecorder'].".ondataavailable = function(blob) {
                   
                  var fileType = 'audio';
                  var fileName = 'fileNameAuto.ogg';

                  var formData = new FormData();
                  formData.append(fileType + '-filename', fileName);
                  formData.append(fileType + '-blob', blob);
		  formData.append('id', '".$this->options['id']."');
		  
		   //console.log(formData);
		   
		   $.ajax({
			method: 'POST',
			url:'" . $this->options['saveUrl'] . "',
			data: formData,
			dataType: 'JSON',
			enctype: 'multipart/form-data',
			processData: false,  // tell jQuery not to process the data
			contentType: false,   // tell jQuery not to set contentType
			success: function(result, textStatus) {
			    if(result.status == 'success') {
				$('#$inputID').val(result.name);
				    
				var audio = document.createElement('audio');
				audio = mergeProps(audio, {
				    controls: true,
				    src: result.link
				});

				$('#".$this->options['audio-container']."').html(audio);
			    } else {
				" . SDNoty::show('result.message', 'result.status') . "
			    }
			}
		    });

			
                    $('#".$this->options['audio-container']."').html('Converting <i class=\"sdloader-icon\"></i>');
                    
		    
		    ".$this->options['mediaRecorder'].".stop();
		    ".$this->options['mediaRecorder'].".stream.stop();
		    document.querySelector('#".$this->options['stopBtn']."').disabled = true;	
		    document.querySelector('#".$this->options['startBtn']."').disabled = false;
                };
		
                var timeInterval = 1000*60*10;
                // get blob after specific time interval
                ".$this->options['mediaRecorder'].".start(timeInterval);
		    
                document.querySelector('#".$this->options['stopBtn']."').disabled = false;
                
            }

            

	");
    }
}
