<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\assets\drawing\DrawingAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use dosamigos\fileupload\FileUpload;
use Yii;

class SDDrawingv2 extends InputWidget
{
    public $ui_temp = '<div id="{canvasDraw}" class="canvasDraw">
			    <div class="text-center">
					<div class="drawingToo btn-toolbar" role="toolbar">
                                                <div class="btn-group" style="margin-bottom: 5px;">
							<button class="fileUpload btn btn-sm btn-default" >
							<i class="glyphicon glyphicon-picture"></i> 
							{input-upload}
							</button>
						</div>
                                                <div class="btn-group" style="margin-bottom: 5px;">
							<button class="clearDrawing btn btn-sm btn-default" type="button"><i class="iconcolor-clear_draw"></i> </button>
							<button class="fullscreen btn btn-sm btn-default" type="button"><i class="glyphicon glyphicon-fullscreen"></i> </button>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="paintTool btn-sm btn btn-default active" data-type="paint">
								<input type="radio" name="paintTool" checked> <i class="iconcolor-paint"></i>
							</label>
							<label class="paintTool btn-sm btn btn-default" data-type="text">
								<input type="radio" name="paintTool" checked> <i class="glyphicon glyphicon-font"></i>
							</label>
							<label class="paintTool btn-sm btn btn-default" data-type="eraser">
								<input type="radio" name="paintTool" checked> <i class="iconcolor-eraser"></i>
							</label>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="lineTool btn-sm btn btn-default" data-type="2">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line2"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default active" data-type="4">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line4"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default" data-type="6">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line6"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default" data-type="8">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line8"></i>
							</label>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="colorTool btn-sm btn btn-default" data-type="ba">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-black"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default active" data-type="r">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-red"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default" data-type="b">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-blue"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default" data-type="y">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-yellow"></i>
							</label>
						</div>
						
                                                
					</div>
			    </div>
				<div id="{canvasDraw}_box" class="canvasDraw_box" style="width: {widthDraw}px;height: {heightDraw}px;"></div>
			</div>
			<div class="text-center save-btn-drawing">
			    <button id="{saveBtn}" class="btn btn-primary disabledDisplay" name="yt0" type="button">ยืนยันการบันทึก</button>
			</div>
		    ';
	
	public $allow_bg = 0;
	public $default_bg = '';
	
	public function init()
    {
        parent::init();
        
	if(!isset($this->options['width'])){
	    $this->options['width'] = 800;
	} 
	
	if(!isset($this->options['height'])){
	    $this->options['height'] = 600;
	} 
	
	if(!isset($this->options['canvasDraw'])){
	    $this->options['canvasDraw'] = 'canvasDrawDiv';
	} 
	
	if(!isset($this->options['saveBtn'])){
	    $this->options['saveBtn'] = 'saveDrawing';
	} 
	
	if(!isset($this->options['drawingName'])){
	    $this->options['drawingName'] = 'myCanvasDrawing';
	} 
	
	if(!isset($this->options['outlineName'])){
	    $this->options['outlineName'] = '';
	} 
	
	if(!isset($this->options['outlinePath'])){
	    $this->options['outlinePath'] = '';
	} 
	
	if(!isset($this->options['outlineBg'])){
	    $this->options['outlineBg'] = '';
	} 
	
	if(!isset($this->options['saveUrl'])){
	    $this->options['saveUrl'] = Url::to(['//ezforms2/drawing/save-image']);
	} 
	
	if(!isset($this->options['bgUrl'])){
	    $inputID;
	    if ($this->hasModel()) {
		$inputID = Html::getInputId($this->model, $this->attribute);
	    } else {
		$inputID = $this->name;
	    }
	    $this->options['bgUrl'] = Url::to(['//ezforms2/drawing/bg-image', 'name'=>$inputID]);
	}
	
	$id = $this->options['id'];
	$this->options['canvasDraw'] = $this->options['canvasDraw'].'_'.$id;
	$this->options['saveBtn'] = $this->options['saveBtn'].'_'.$id;
	$this->options['drawingName'] = $this->options['drawingName'].'_'.$id;
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
	
	$fileUpload = FileUpload::widget([
	    'name' => 'outline-bg',
	    'url' => $this->options['bgUrl'], 
	    'plus' => true,
            'useDefaultButton'=>false,
	    'options' => ['accept' => 'image/*', 'class'=>'upload'],
	    'clientOptions' => [
		'maxFileSize' => 3000000
	    ],
	    // Also, you can specify jQuery-File-Upload events
	    // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
	    'clientEvents' => [
		'fileuploaddone' => "function(e, data) {
					var bgsize = 'auto auto';
					if(data.result.files.width > data.result.files.height){
					    bgsize = '{$this->options['width']}px auto';
					} else {
					    bgsize = 'auto {$this->options['height']}px';
					}
					$('input[name=\"outline-bg\"]').attr('data-url', data.result.files.newurl);
					$('input[name=\"outline-bg\"]').fileupload({'maxFileSize':3000000,'url':$('input[name=\"outline-bg\"]').attr('data-url')});
					
					if($('#".$inputID."').val()==''){
					    $('#".$inputID."').val(\"'',\"+data.result.files.name);
					} else {
					    var str = $('#".$inputID."').val();
					    var valueArr = str.split(',');
					    var valueStr = valueArr[0]+','+data.result.files.name;
					    $('#".$inputID."').val(valueStr);
					}

					$('#{$this->options['drawingName']}').css('background-image', 'url('+data.result.files.url+')');
					$('#{$this->options['drawingName']}').css('background-size', bgsize);
					$('#{$this->options['drawingName']}').css('background-position', 'center center');
					$('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
				    }",
		'fileuploadfail' => "function(e, data) {
					console.log(e);
					console.log(data);
				    }",
	    ],
	]);
                                        
	$width = $this->options['width']>0?$this->options['width']:800;
        $height = $this->options['height']>0?$this->options['height']:600;
        
	echo strtr($template, [
		'{canvasDraw}' => $this->options['canvasDraw'],
		'{saveBtn}' => $this->options['saveBtn'],
		'{input-upload}' => $fileUpload,
                '{widthDraw}' => $width,
                '{heightDraw}' => $height,
	    ]);
	
	//filename,filebg
	
	if(isset($inputValue) && $inputValue!=''){
	    $fileArr = explode(',', $inputValue);
		if(count($fileArr)>1){
			$fileName = $fileArr[0];
			$fileBg = $fileArr[1];

			if(stristr($fileName, '.png') == TRUE){
                            
                            
				$idName = '';
				if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
					$idName = 'mycom';
				} else {
					$idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
				}
				$nowFileName = $idName . '_'.$inputID.'_'.\appxq\sdii\utils\SDUtility::getMillisecTime().'_tmp.png';

                                $pathTmp = Yii::getAlias('@app/web/drawing/').$idName;
                                $pathTmpData = Yii::getAlias('@app/web/drawing/').$idName.'/'. date('Y_m_d');

                                if (!file_exists($pathTmpData)) {
                                    if (file_exists($pathTmp)) {
                                        $folders = array_diff(scandir($pathTmp), array('.','..')); 
                                        
                                        foreach ($folders as $folder) { 
                                            $pathFolder = $pathTmp.'/'.$folder;
                                            $unlink = @unlink("$pathTmp/$folder");
                                            if($unlink){
                                                continue;
                                            }
                                          if (file_exists($pathFolder)) {
                                                $files = array_diff(scandir($pathFolder), array('.','..')); 
                                                foreach ($files as $file) { 
                                                  @unlink("$pathFolder/$file"); 
                                                }
                                                 rmdir($pathFolder);
                                          } 
                                        }
                                         rmdir($pathTmp);
                                    } else {
                                        mkdir($pathTmp, 0777, true);
                                    }
                                    
                                    mkdir($pathTmpData, 0777, true);
                                }
                                
				@copy(Yii::getAlias('@storage/web/ezform/drawing/data/') . $fileName, $pathTmpData.'/' . $nowFileName);

                                $this->options['outlineName'] = $nowFileName;
				$this->options['outlinePath'] = Yii::getAlias('@web').'/drawing/'.$idName.'/'. date('Y_m_d');
			}
		}
	}
	//echo $this->ui_temp;
	
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value);
        }
	
	if (isset($this->default_bg) && $this->default_bg!='') {
		
	    $fileBg = $this->default_bg;
	
	    if(stristr($fileBg, '.png') == TRUE){
                $view = $this->getView();
                try {
                    list($width, $height, $type, $attr) = @getimagesize(Yii::getAlias('@storage/web/ezform/drawing/bg/').$fileBg);
                    $disable = ($this->allow_bg)?'':"$('#{$this->options['canvasDraw']} .fileUpload').addClass('disabledDisplay');";
                    if($width){
                        $view->registerJs("
                                $disable
                                var bgsize = 'auto auto';
                                if({$width} > {$height}){
                                bgsize = '{$this->options['width']}px auto';
                                } else {
                                bgsize = 'auto {$this->options['height']}px';
                                }
                                $('#{$this->options['drawingName']}').css('background-image', 'url(".Url::to(Yii::getAlias('@storageUrl').'/ezform/drawing/bg/'.$fileBg).")');
                                $('#{$this->options['drawingName']}').css('background-size', bgsize);
                                $('#{$this->options['drawingName']}').css('background-position', 'center center');
                                $('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
                        ");
                    }
                    
                } catch (Exception $exc) {

                }
			
            }
	}
		
	if(isset($inputValue) && $inputValue!=''){
	    $fileArr = explode(',', $inputValue);
		if(count($fileArr)>1){
			$fileName = $fileArr[0];
			$fileBg = $fileArr[1];

			if(stristr($fileBg, '.png') == TRUE){
				$view = $this->getView();
				list($width, $height, $type, $attr) = getimagesize(Yii::getAlias('@storage/web/ezform/drawing/bg/').$fileBg);

				$view->registerJs("
					var bgsize = 'auto auto';
					if({$width} > {$height}){
						bgsize = '{$this->options['width']}px auto';
					} else {
						bgsize = 'auto {$this->options['height']}px';
					}
					$('#{$this->options['drawingName']}').css('background-image', 'url(".Url::to(Yii::getAlias('@storageUrl').'/ezform/drawing/bg/'.$fileBg).")');
					$('#{$this->options['drawingName']}').css('background-size', bgsize);
					$('#{$this->options['drawingName']}').css('background-position', 'center center');
					$('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
				");
			}
		}
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
	
	DrawingAsset::register($view);
	$view->registerJs("
            var colorRed = \"#ff0000\";
            var colorBlue = \"#0033ff\";
            var colorYellow = \"#ffcf33\";
            var colorBlack = \"#333\";
            
//	    drawingName = '".$this->options['drawingName']."';
//	    saveBtn = '".$this->options['saveBtn']."';
//	    canvasDraw = '".$this->options['canvasDraw']."';
		
	    drawCanvas('".$this->options['drawingName']."', '".$this->options['canvasDraw']."', '".$this->options['saveBtn']."', ".$this->options['width'].", ".$this->options['height'].", '".$this->options['outlineName']."', '".$this->options['outlinePath']."', '".$this->options['outlineBg']."');

		$('#{$this->options['canvasDraw']} .fullscreen').click(function(){
			if ($.fullscreen.isFullScreen()) {
				$.fullscreen.exit();
				return false;
			} else {
				$('#{$this->options['canvasDraw']}').parent().fullscreen({toggleClass:'fullscreen'});
				return false;
			}
		});

	    $('#".$this->options['saveBtn']."').click(function(){
		    var canvas = document.getElementById('".$this->options['drawingName']."');
		    var img = canvas.toDataURL('image/png');
		    $.ajax({
			method: 'POST',
			url:'" . $this->options['saveUrl'] . "',
			data: {type: 'data', image: img, name: '".$inputID."', '_csrf':'".Yii::$app->request->getCsrfToken()."'},
			dataType: 'JSON',
			success: function(result, textStatus) {
			    if(result.status == 'success') {
					if($('#".$inputID."').val()==''){
						$('#".$inputID."').val(result.data+\",''\");
					} else {
						var str = $('#".$inputID."').val();
						var valueArr = str.split(',');
						var valueStr = result.data+','+valueArr[1];
						$('#".$inputID."').val(valueStr);
					}
			    } else {
				" . SDNoty::show('result.message', 'result.status') . "
			    }
			}
		    });
	    });
            
            // -- js drawing --
            
            
           
            
            function drawCanvas(drawingName, canvasDraw, saveBtn, width, height, outlineName, outlinePath, outlineBg)
            {
              var context;
              var imageObj = new Image();
              var imageBgObj = new Image();
              var paint = true;
              var textTool = false;
              var saveId = saveBtn;
              var lastEvent;
              var mouseDown = false;

              var canvasDiv = document.getElementById(canvasDraw + '_box');
              var canvas = document.createElement('canvas');
              canvas.setAttribute('width', width);
              canvas.setAttribute('height', height);
              canvas.setAttribute('class', 'drawingBox');
              canvas.setAttribute('id', drawingName);
              canvasDiv.appendChild(canvas);
              if (typeof G_vmlCanvasManager != 'undefined') {
                canvas = G_vmlCanvasManager.initElement(canvas);
              }

              context = canvas.getContext('2d');


              context.strokeStyle = colorRed;//สีเส����
              context.lineCap = 'round';//lineJoin เหลี��ยม  , lineCap ม��
              context.lineWidth = 4;//ข��า��เส����

              $(canvasDiv).on('click', '.saveText', function () {
                var x = $(this).attr('data-x');
                var y = $(this).attr('data-y');
                //get the value of the textarea then destroy it and the save button
                var text = $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').val();
                $(canvasDiv).children('.textAreaPopUp').remove();
                //break the text into arrays based on a text width of 100px
                var phraseArray = getLines(context, text, 100);
                // this adds the text functions to the context
                CanvasTextFunctions.enable(context);
                var counter = 0;
                //set the font styles
                var font = 'Helvetica';
                var fontsize = 16;

                //draw each phrase to the screen, making the top position 20px more each time so it appears there are line breaks
                $.each(phraseArray, function () {
                  //set the placement in the canvas
                  var lineheight = fontsize * 1.5;
                  var newline = ++counter;
                  newline = newline * lineheight;
                  var topPlacement = y - $(canvas).position().top + newline;
                  var leftPlacement = x - $(canvas).position().left;
                  text = this;
                  //draw the text
                  context.drawText(font, fontsize, leftPlacement, topPlacement, text);
                  context.save();
                  context.restore();
                });

                $('#' + saveId).trigger('click');
              });

              $(canvas).on('mousedown touchstart', function (e) {
                let rect = e.target.getBoundingClientRect();
                let event = (e.type.toLowerCase() === 'mousedown')? e.originalEvent : e.originalEvent.touches[0];

                let mouseX = event.pageX - rect.left;
                let mouseY = event.pageY - rect.top;

                if(e.type.toLowerCase() === 'mousemove'){
                  mouseX = event.offsetX;
                  mouseY = event.offsetY;
                }

                if (textTool) {
                  context.globalCompositeOperation = 'source-over';

                  if ($(canvasDiv).children('.textAreaPopUp').length == 0) {
                    var appendString = '<div class=\"textAreaPopUp form-inline\" style=\"position:absolute;top:' + mouseY + 'px;left:' + mouseX + 'px;z-index:300;\">' +
                            '<div class=\"form-group\">' +
                            '<div class=\"input-group\">' +
                            '<textarea type=\"text\" class=\"textareaInput form-control\" style=\"width:150px;height:35px;\" onkeyup=\"if(event.keyCode == 13) { $(this).parent().find(\'.saveText\').trigger(\'click\'); return false; }\"></textarea>' +
                            '<a type=\"button\" class=\"saveText btn btn-primary input-group-addon\" data-x=\"' + mouseX + '\" data-y=\"' + mouseY + '\">Save</a>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    $(canvasDiv).append(appendString);
                    setTimeout(function () {
                      $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').focus();
                    }, 200);

                  } else {
                    $(canvasDiv).children('.textAreaPopUp').remove();
                    var appendString = '<div class=\"textAreaPopUp form-inline\" style=\"position:absolute;top:' + mouseY + 'px;left:' + mouseX + 'px;z-index:300;\">' +
                            '<div class=\"form-group\">' +
                            '<div class=\"input-group\">' +
                            '<textarea type=\"text\" class=\"textareaInput form-control\" style=\"width:150px;height:35px;\" onkeyup=\"if(event.keyCode == 13) { $(this).parent().find(\'.saveText\').trigger(\'click\'); return false; }\"></textarea>' +
                            '<a type=\"button\" class=\"saveText btn btn-primary input-group-addon\" data-x=\"' + mouseX + '\" data-y=\"' + mouseY + '\">Save</a>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    $(canvasDiv).append(appendString);
                    setTimeout(function () {
                      $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').focus();
                    }, 200);
                  }
                } else {
                  mouseDown = true;
                  lastEvent = event;

                  context.beginPath();
                  context.fillStyle = context.strokeStyle;
                  context.arc(mouseX,mouseY,1,0,Math.PI*2,true);
                  context.fill();
                  context.closePath();
                  context.stroke();
                }
              }).on('mousemove touchmove',function(e) {
               if(e.type.toLowerCase() === 'mousemove'){
               
               } else {
                    e.preventDefault();
               }
                
                let rect = e.target.getBoundingClientRect();
                let event = (e.type.toLowerCase() === 'mousemove')? e.originalEvent : e.originalEvent.touches[0];

                let mouseX = event.pageX - rect.left;
                let mouseY = event.pageY - rect.top;

                let lastMouseX = event.pageX - rect.left;
                let lastMouseY = event.pageY - rect.top;
                if (lastEvent === undefined || lastEvent === null) {

                 } else {
                   lastMouseX = lastEvent.pageX - rect.left;
                   lastMouseY = lastEvent.pageY - rect.top;
                 }

                if(e.type.toLowerCase() === 'mousemove'){
                  mouseX = event.offsetX;
                  mouseY = event.offsetY;

                  if (lastEvent === undefined || lastEvent === null) {
                      lastMouseX = event.offsetX;
                      lastMouseY = event.offsetY;
                 } else {
                   lastMouseX = lastEvent.pageX - rect.left;
                   lastMouseY = lastEvent.pageY - rect.top;
                 }

                }

                if (mouseDown) {
                    //Draw lines
                    context.beginPath();
                    context.moveTo(lastMouseX, lastMouseY);
                    context.lineTo(mouseX, mouseY);
                    //context.strokeStyle = color;

                    if (paint) {
                      context.globalCompositeOperation = 'source-over';//paint
                    } else {
                      context.globalCompositeOperation = \"destination-out\";//ยา��ล�� eraser
                    }
                    context.closePath();
                    context.stroke();
                    
                    lastEvent = event;
                }
            }).on('mouseup touchend',function() {
                if (!textTool) {
                  mouseDown = false;
                  $('#' + saveId).trigger('click');
                }
            }).on('mouseleave touchleave',function(e) {
                if(e.type.toLowerCase() === 'mouseleave'){
                  $(canvas).trigger('mouseup');
                } else {
                  $(canvas).trigger('touchend');
                }

            });

              imageObj.onload = function () {
                context.drawImage(imageObj, 0, 0);
              };

              imageBgObj.onload = function () {
                context.drawImage(imageBgObj, 0, 0);
              };

              if (outlineName != '') {

                imageObj.src = outlinePath + '/' + outlineName; //ภาพเ��ิม
              }
              if (outlineBg != '') {
                imageBgObj.src = outlineBg; //ภาพพื����หลั��
              }

              $('#' + canvasDraw + ' .paintTool').click(function () {
                if ($(this).attr('data-type') == 'paint') {
                  paint = true;
                } else {
                  paint = false;
                }
              });

              $('#' + canvasDraw + ' .paintTool').click(function () {
                if ($(this).attr('data-type') == 'text') {
                  textTool = true;
                } else {
                  textTool = false;
                }
              });

              $('#' + canvasDraw + ' .colorTool').click(function () {
                if ($(this).attr('data-type') == 'ba') {
                  context.strokeStyle = colorBlack;
                } else if ($(this).attr('data-type') == 'r') {
                  context.strokeStyle = colorRed;
                } else if ($(this).attr('data-type') == 'b') {
                  context.strokeStyle = colorBlue;
                } else if ($(this).attr('data-type') == 'y') {
                  context.strokeStyle = colorYellow;
                }
              });

              $('#' + canvasDraw + ' .lineTool').click(function () {
                if ($(this).attr('data-type') == '2') {
                  context.lineWidth = 2;
                } else if ($(this).attr('data-type') == '4') {
                  context.lineWidth = 4;
                } else if ($(this).attr('data-type') == '6') {
                  context.lineWidth = 6;
                } else if ($(this).attr('data-type') == '8') {
                  context.lineWidth = 8;
                }
              });

              $('#' + canvasDraw + ' .clearDrawing').click(function () {
                context.clearRect(0, 0, width, height);//ล����ั����หม��
                context.drawImage(imageBgObj, 0, 0);
                //$('#'+saveId).addClass('disabledDisplay');
                $('#' + saveId).trigger('click');
              });

            }

            function getLines(context, phrase, maxPxLength) {
              var wa = phrase.split(' '),
                      phraseArray = [],
                      lastPhrase = '',
                      l = maxPxLength,
                      measure = 0;
              context.font = '16px Helvetica';
              for (var i = 0; i < wa.length; i++) {
                var w = wa[i];
                measure = context.measureText(lastPhrase + w).width;
                if (measure < l) {
                  lastPhrase += (' ' + w);
                } else {
                  phraseArray.push(lastPhrase);
                  lastPhrase = w;
                }
                if (i === wa.length - 1) {
                  phraseArray.push(lastPhrase);
                  break;
                }
              }
              return phraseArray;
            }
	");
    }
}
