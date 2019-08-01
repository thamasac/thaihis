<?php

namespace dms\aomruk\widgets;

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
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use appxq\sdii\assets\ProvinceAsset;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\JsExpression;

class DSCtcae extends InputWidget {

    public $fields;
    public $enable_tumbon = 0;

    public function init() {
        parent::init();
    }

    public function run() {
        $fields;
        $htmlCtcae = '';

        if (isset($this->fields)) {
            foreach ($this->fields as $key => $value) {
                $fields[$value['label']] = $value['attribute'];
            }
        } else {
            return 'Fields not set.';
        }
        //$fields['province'] $fields['amphur'] $fields['tumbon']
        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();


//	$itemsProvince = EzfQuery::getProvince();
        $inputSocID;
        $inputCtcaeID;
        $inputGradeID;
        $inputSocValue;
        $inputCtcaeValue;
        $inputGradeValue;

        if ($this->hasModel()) {
            $inputSocID = Html::getInputId($this->model, $fields['soc']);
            $inputCtcaeID = Html::getInputId($this->model, $fields['ctcae']);
            $inputGradeID = Html::getInputId($this->model, $fields['grade']);
            $inputGradeValue = Html::getAttributeValue($this->model, $fields['grade']);
            $inputSocValue = Html::getAttributeValue($this->model, $fields['soc']);
            $inputCtcaeValue = Html::getAttributeValue($this->model, $fields['ctcae']);
        }
//        \appxq\sdii\utils\VarDumper::dump($inputSocID);
        $valueSoc = (new \yii\db\Query())->select('soc')->from('const_soc')->where(['id' => $inputSocValue])->scalar();
        $valueCtcae = (new \yii\db\Query())->select('ctcae_term')->from('const_ctcae_term')->where(['id' => $inputCtcaeValue])->scalar();
        $valueGrade = (new \yii\db\Query())->select("grade,grade_detail")->from('const_grade')->where(['id' => $inputGradeValue])->one();
        $annotatedP = '';
        $annotatedA = '';
        $annotatedT = '';
        if (isset($this->options['annotated']) && $this->options['annotated']) {
            $annotatedP = "<code>{$fields['soc']}</code>";
            $annotatedA = "<code>{$fields['ctcae']}</code>";
            $annotatedT = "<code>{$fields['grade']}</code>";
        }
 
        $idSoc = $this->id . '_' . $fields['soc'];
        $idTerm = $this->id . '_' . $fields['ctcae'];
        $idGrade = $this->id . '_' . $fields['grade'];
        //$html = '<label>'.$this->model->getAttributeLabel($this->attribute).'</label>';
        $html = "<div class='row'><div class='col-md-4'>";
        $html .= Select2::widget([
                    'options' => ['placeholder' => 'SOC', 'id' => $idSoc],
//                    'data' => ArrayHelper::map($itemsProvince, 'id', 'soc'),
                    //'model' =>$this->model,
                    'name' => $idSoc,
                    'initValueText' => $valueSoc,
                    'value' => $inputSocValue,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => Url::to(['/ezforms2/ctcae/get-soc']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term,ctcae:$("#' . $idTerm . '").val(),grade:$("#' . $idGrade . '").val()}; }'),
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(result) { return result.text; }'),
                        'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function(e) { 
                                $('#$inputSocID').val(e.params.data.id); 
                                if($('#{$idSoc}').val() == 100){
                                    if($('#{$idTerm}').val() != ''){
                                        $('#btn-update-{$idTerm}').show();
                                    }else{
                                        $('#btn-update-{$idTerm}').hide();
                                    }
                                    $('#btn-add-{$idTerm}').show();

                                    if($('#{$idGrade}').val() != ''){
                                        $('#btn-update-{$idGrade}').show();
                                    }else{
                                        $('#btn-update-{$idGrade}').hide();
                                    }
                                    $('#btn-add-{$idGrade}').show();
                                }else{
                                    $('#btn-update-{$idTerm}').hide();
                                    $('#btn-update-{$idGrade}').hide();
                                    $('#btn-add-{$idGrade}').hide();
                                    $('#btn-add-{$idTerm}').hide();
                                }
                        }
                    ",
                        "select2:unselect" => "function() { 
                            $('#$inputSocID').val(''); 
                            $('#$inputCtcaeID').val('');
                            $('#$inputGradeID').val(''); 
                            $('#$idTerm').val('').trigger('change');
                            $('#$idGrade').val('').trigger('change'); 
                            $('#btn-update-{$idTerm}').hide();
                            $('#btn-update-{$idGrade}').hide();
                            $('#btn-add-{$idGrade}').hide();
                            $('#btn-add-{$idTerm}').hide();
                        }
                    "
                    ]
        ]);

        $html .= $annotatedP;
        $html .= "</div>";
        $html .= "<div class='col-md-4 sdbox-col ctcaeSelect'>";
        $html .= '<div class="input-group">' . Select2::widget([
                    'options' => ['placeholder' => 'CTCAE Term', 'id' => $idTerm],
//                    'data' => ArrayHelper::map($itemsProvince, 'id', 'soc'),
                    //'model' =>$this->model,
                    'name' => $idTerm,
                    'value' => $inputCtcaeValue,
                    'initValueText' => $valueCtcae,
                    'pluginOptions' => [
                        'allowClear' => true,
//                        'language' => [
//                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
//                        ],
                        'ajax' => [
                            'url' => Url::to(['/ezforms2/ctcae/get-ctcae-terms']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term,soc:$("#' . $idSoc . '").val(),grade:$("#' . $idGrade . '").val()}; }'),
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(result) { return result.text; }'),
                        'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function(e) { 
                            $('#$inputCtcaeID').val(e.params.data.id);  
                            if($('#{$idSoc}').val() == 100){    
                                if($('#{$idTerm}').val() != ''){
                                    $('#btn-update-{$idTerm}').show();
                                }else{
                                    $('#btn-update-{$idTerm}').hide();
                                }
                                $('#btn-add-{$idTerm}').show();
                            }
                            $.get('".Url::to(['/ezforms2/ctcae/get-soc'])."',{q:'',ctcae:$('#" . $idTerm . "').val(),grade:$('#" . $idGrade . "').val()},function(data){
                                    if(data.results.indexOf(0)){
                                        var option = new Option(data.results[0].text, data.results[0].id, true, true);
                                        $('#$idSoc').append(option).trigger('change');
                                        $('#$inputSocID').val(data.results[0].id);
                                    }
                                });
                                    
                        }",
                        "select2:unselect" => "function() { 
                            $('#$inputCtcaeID').val(''); 
                            $('#$inputGradeID').val('');
                            $('#$idGrade').val('').trigger('change'); 
                            $('#btn-update-{$idTerm}').hide();
                        }"
                    ]
                ]) . ' <span class="input-group-btn">' . Html::button('<i class="glyphicon glyphicon-pencil"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Update'), 'class' => 'btn btn-primary', 'id' => 'btn-update-' . $idTerm, 'data-url' => Url::to(['/ezforms2/ctcae/add-ctcae-term']), 'style' => 'display:none']) . ' '
                . Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'New'), 'class' => 'btn btn-success', 'id' => 'btn-add-' . $idTerm, 'data-url' => Url::to(['/ezforms2/ctcae/add-ctcae-term']), 'style' => 'display:none']) . '</span></div>';
//        $html .= DepDrop::widget([
//            'type'=>  DepDrop::TYPE_SELECT2,
//            'options'=>['id'=>$this->id.'_'.$fields['ctcae']],
//            //'model'=>$this->model,
//            'name'=>$this->id.'_'.$fields['ctcae'],
//	    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
//            'pluginOptions'=>[
//		'allowClear' => true,
//                'depends'=>[$this->id.'_'.$fields['soc']],
//                'placeholder'=>'CTCAE Term',
//                'url'=>Url::to(['/ezforms2/ctcae/get-ctcae-term']),
//		'params'=>[$inputAmphurID],
//            ],
//	    'pluginEvents' => [
//		"select2:select" => "function(e) {  $('#$inputAmphurID').val(e.params.data.id); $('#$inputTumbonID').val(''); }",
//		"select2:unselect" => "function() { $('#$inputAmphurID').val(''); $('#$inputTumbonID').val(''); }",
//	    ]
//        ]);
        $html .= $annotatedA;
        $html .= "</div>";



        if ($this->enable_tumbon) {
            $html .= "<div class='col-md-4 sdbox-col'>";
            $html .= //'<div class="input-group">'.
                    Select2::widget([
                        'options' => ['placeholder' => 'Grade', 'id' => $idGrade],
//                    'data' => ArrayHelper::map($itemsProvince, 'id', 'soc'),
                        //'model' =>$this->model,
                        'name' => $idGrade,
                        'value' => $inputGradeValue,
                        'initValueText' => '(' . $valueGrade['grade'] . ') ' . $valueGrade['grade_detail'],
                        'pluginOptions' => [
                            'allowClear' => true,
//                        'language' => [
//                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
//                        ],
                            'ajax' => [
                                'url' => Url::to(['/ezforms2/ctcae/get-grades']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term,soc:$("#' . $idSoc . '").val(),ctcae:$("#' . $idTerm . '").val()}; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                            'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                        ],
                        'pluginEvents' => [
                            "select2:select" => "function(e) { 
                                $('#$inputGradeID').val(e.params.data.id);
//                                if($('#{$idSoc}').val() == 100){
//                                    if($('#{$idGrade}').val() != ''){
//                                        $('#btn-update-{$idGrade}').show();
//                                    }else{
//                                        $('#btn-update-{$idGrade}').hide();
//                                    }
//                                    $('#btn-add-{$idGrade}').show();
//                                }
                                
                            }",
                            "select2:unselect" => "function() { 
                                $('#$inputGradeID').val(''); 
//                                $('#btn-update-{$idGrade}').hide();
                            }"
                        ]
            ]); //. ' <span class="input-group-btn">' . Html::button('<i class="glyphicon glyphicon-pencil"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Update'), 'class' => 'btn btn-primary','id'=>'btn-update-'.$idGrade, 'data-url' => Url::to(['/ezforms2/ctcae/add-grade']),'style'=> 'display:none']) . ' '
            //.Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'New'), 'class' => 'btn btn-success','id'=>'btn-add-'.$idGrade, 'data-url' => Url::to(['/ezforms2/ctcae/add-grade']),'style'=> 'display:none']) . '</span></div>';
//            $html .= DepDrop::widget([
//                        'type' => DepDrop::TYPE_SELECT2,
//                        'options' => ['id' => $this->id . '_' . $fields['grade']],
//                        //'model'=>$this->model,
//                        'name' => $this->id . '_' . $fields['grade'],
//                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                            'depends' => [$this->id . '_' . $fields['soc'], $this->id . '_' . $fields['ctcae']],
//                            'placeholder' => 'Grade',
//                            'initialize' => true,
//                            'initDepends' => [$this->id . '_' . $fields['soc']],
//                            'url' => Url::to(['/ezforms2/ctcae/get-grade']),
//                            'params' => [$inputTumbonID],
//                        ],
//                        'pluginEvents' => [
//                            "select2:select" => "function(e) { $('#$inputTumbonID').val(e.params.data.id); }",
//                            "select2:unselect" => "function() { $('#$inputTumbonID').val(''); }",
//                        ]
//            ]);
            $html .= $annotatedT;
            $html .= "</div>";
        }
        $html .= "</div>";

        echo $html;

        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $fields['soc']);
            echo Html::activeHiddenInput($this->model, $fields['ctcae']);
            echo Html::activeHiddenInput($this->model, $fields['grade']);
        }
        $view = $this->getView();
        $submodal = '<div id="modal-' . $this->id . '" class="fade modal" role="dialog"><div class="modal-dialog"><div class="modal-content"></div></div></div>';
        $submodalFix = '<div id="modal-fix-' . $this->id . '" class="fade modal" role="dialog"><div class="modal-dialog"><div class="modal-content"></div></div></div>';

        $view->registerJs("
            setTimeout(()=>{
                if($('#{$idSoc}').val() == 100 && $('#{$idSoc}').attr('disabled') != 'disabled'){
                    if($('#{$idTerm}').val() != ''){
                        $('#btn-update-{$idTerm}').show();
                    }
                    $('#btn-add-{$idTerm}').show();
                        
                    if($('#{$idGrade}').val() != ''){
                        $('#btn-update-{$idGrade}').show();
                    }
                    $('#btn-add-{$idGrade}').show();
                }
            },100);
                
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
                
                $('#btn-add-{$idTerm}').click(function(){
                    var url = $(this).attr('data-url')+'?soc_id='+$('#{$idSoc}').val()+'&id='+$('#{$idTerm}').val();
                    $('#modal-{$this->id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-{$this->id}').modal('show')
                    .find('.modal-content')
                    .load(url);
                });
                
                $('#btn-update-{$idTerm}').click(function(){
                    var url = $(this).attr('data-url')+'?soc_id='+$('#{$idSoc}').val()+'&id='+$('#{$idTerm}').val();
                    $('#modal-{$this->id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-{$this->id}').modal('show')
                    .find('.modal-content')
                    .load(url);
                });
                
                $('#btn-add-{$idGrade}').click(function(){
                    var url = $(this).attr('data-url')+'?soc_id='+$('#{$idSoc}').val()+'&id='+$('#{$idGrade}').val()+'&ctcae_id='+$('#{$idTerm}').val();
                    $('#modal-{$this->id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-{$this->id}').modal('show')
                    .find('.modal-content')
                    .load(url);
                });
                
                $('#btn-update-{$idGrade}').click(function(){
                    var url = $(this).attr('data-url')+'?soc_id='+$('#{$idSoc}').val()+'&id='+$('#{$idGrade}').val()+'&ctcae_id='+$('#{$idTerm}').val();
                    $('#modal-{$this->id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $('#modal-{$this->id}').modal('show')
                    .find('.modal-content')
                    .load(url);
                });

            ");
//        ProvinceAsset::register($view);
    }

    public function registerClientScript() {
        $view = $this->getView();
        ProvinceAsset::register($view);
        $view->registerJs("
            
            ");
    }

}
