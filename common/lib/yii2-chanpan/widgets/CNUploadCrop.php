<?php

namespace cpn\chanpan\widgets;

use yii\helpers\Html;
use Yii;

class CNUploadCrop extends \yii\base\Widget {

    public $upload_input = '';
    public $icon_name = '';
    public $imgPath = '';
    public $imageLocation = '';
    public $type = '';
    public $options = [];

    public function init() {
        //parent::init();
        $this->options['width'] = ($this->options['width'] != '') ? $this->options['width'] : '200';
        $this->options['height'] = ($this->options['height'] != '') ? $this->options['height'] : '200';
        $this->options['type'] = ($this->options['type'] != '') ? $this->options['type'] : 'square';
         
        $this->upload_input = ($this->upload_input != '') ? $this->upload_input : 'upload_input';
        $this->icon_name = ($this->icon_name != '') ? $this->icon_name : 'txt_icon';
        $this->imgPath = ($this->imgPath != '') ? $this->imgPath : \Yii::getAlias('@storageUrl');
        $this->imageLocation = ($this->imageLocation != '') ? $this->imageLocation : $this->imgPath . '/ezform/img/no_icon.png';
    }

    public function run() {        
        
        $html = "";
        $html .= Html::beginTag("DIV", ['class' => 'upload-edit col-md-3', 'style'=>'margin-top:10px;']);
        $html .= Html::label(Yii::t('chanpan', 'Icon'));
        $html .= Html::fileInput($this->upload_input, null, ['id' => 'upload-input']);
        $html .= Html::hiddenInput($this->icon_name, '', ['id' => 'txt_icon']);

        $html .= Html::tag("DIV", Html::img($this->imageLocation, ['id' => 'preview_icon', 'class' => 'img-rounded']), ['class' => 'upload-msg']);
        $html .= Html::tag("DIV", '', ['id' => 'upload-edit']);
        $html .= Html::tag("DIV", Html::a(\Yii::t('chanpan', 'Save Icon'), '#', [
                            'class' => 'btn btn-success',
                            'id' => 'save-upload'
                        ]), ['id' => 'upload-action', 'class' => 'text-center']);

        $html .= Html::endTag("DIV");
        $this->JsRegister();
        echo $html;
    }
 

    public function JsRegister() {
        $view = $this->getView();
        \cpn\chanpan\assets\CNCroppieAssets::register($view);
        $view->registerJs("
            var uploadCrop;
            var resize = '".$this->options['resize']."';
            if(resize == ''){resize = false;}    
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    uploadCrop.croppie('bind', {
                            url: e.target.result
                    });
                    $('.upload-edit').addClass('ready');
                }

                reader.readAsDataURL(input.files[0]);
            }
            else {
                swal(\"Sorry - you're browser doesn't support the FileReader API\");
            }
      }
    uploadCrop = $('#upload-edit').croppie({
            
            
            enableExif: true,
            viewport: {
                width: ".$this->options['width'].",
                height: ".$this->options['height'].",
                type: '".$this->options['type']."' //square, circle
            },
            boundary: {
                width: ".($this->options['width'] + 100).",
                height: ".($this->options['height'] + 100)."
            },
            //showZoomer: true,
            enableResize: resize,
            //enableOrientation: true,
           // mouseWheelZoom: 'ctrl'
    });
 

    $('#upload-input').on('change', function () { readFile(this); });
    $('#save-upload').on('click', function() {
            uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (resp) {
                //alert( resp );
                $('#txt_icon').val(resp);
                $('#preview_icon').attr('src',resp);
                $('.upload-edit').removeClass('ready');
                $('#txt_icon').trigger('change');
            });
            return false;
        });
        ");
    }

}
