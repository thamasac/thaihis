<?php

namespace dms\aomruk\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;

class DrugInput extends InputWidget {

    //put your code here

    public $name = 'damasac-druginput';
    public $maxlength = 6;
    public $rows = 5;
    public $drug_opd = 0;
    public $drug_ipd = 0;
    public $modal_size = 'modal-lg';

    public function init() {
        parent::init();
    }

    public function run() {
        $this->id = 'drug_' . \appxq\sdii\utils\SDUtility::getMillisecTime();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id;
        }
        if ($this->drug_opd == 0 && $this->drug_ipd == 0) {
            $this->drug_opd = 1;
            $this->drug_ipd = 1;
        }
        $this->options['rows'] = $this->rows;

        if (isset($this->options['class'])) {
            $this->options['class'] = $this->options['class'] . " form-control ";
        } else {
            $this->options['class'] = "form-control ";
        }

        if ($this->hasModel()) {
            $textArea = Html::activeTextarea($this->model, $this->attribute, $this->options);

//            return Html::textarea($this->model, $this->attribute,$this->options);
        } else {
            $textArea = Html::textarea($this->name, $this->value, $this->options);
//            return Html::textarea($this->name, $this->value, $this->options);
        }
        $btnAdd = Html::button("<i class='fa fa-plus'> </i>", [
                    'class' => 'btn btn-success btn-block',
                    'style' => 'margin-top:5px',
        ]);
        $this->registerClientScript();
        return Html::tag('div', $textArea, ['form-group']) . $btnAdd . "<br>" . $modal;
    }

    public function registerClientScript() {
        $view = $this->getView();
        $submodal = '<div id="modal-' . $this->id . '" class="fade modal" role="dialog"><div class="modal-dialog ' . $this->modal_size . '"><div class="modal-content"></div></div></div>';
        $submodalFix = '<div id="modal-fix-' . $this->id . '" class="fade modal" role="dialog"><div class="modal-dialog ' . $this->modal_size . '"><div class="modal-content"></div></div></div>';


        $view->registerJs("
        
        var hasMyModal = $( 'body' ).has( '#modal-{$this->id}' ).length;
        var hasMainModal = $( 'body' ).has( '#modal-fix-{$this->id}' ).length;
        
        if($('body .modal').hasClass('in')){
            if(!hasMyModal){
                $('#ezf-modal-box').append('$submodal');
            }
        } else {
            if(!hasMainModal){
                $('#ezf-fix-modal-box').append('$submodalFix');
               
            }
        }
        
        $('#modal-{$this->id}').on('hidden.bs.modal', function(e){
            $('#modal-{$this->id} .modal-content').html('');
                
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
        });
        
        
        
        $('.btn-block').on('click',function(){
            modal_{$this->id}('" . \yii\helpers\Url::to(['/ezforms2/drug-input/load-drug',
                    'drug_ipd' => $this->drug_ipd,
                    'drug_opd' => $this->drug_opd,
                    'id' => $this->options['id']
                ]) . "');
        });

        function modal_{$this->id}(url) {
            $('#modal-{$this->id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-{$this->id}').modal('show')
            .find('.modal-content')
            .load(url);
        }
        
//        console.log($('#{$this->options['id']}').is('[readonly]'));

        ");
    }

}
