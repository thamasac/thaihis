<?php
namespace appxq\sdii\widgets;
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

class SDProvincev2 extends InputWidget {

    public $fields;
    public $enable_tumbon = 0;
    
    public function init() {
	parent::init();
    }

    public function run()
    {
	$fields;
        
        if(isset($this->fields)){
            foreach ($this->fields as $key => $value) {
                $fields[$value['label']] = $value['attribute'];
            }
        } else {
            return 'Fields not set.';
        }
        //$fields['province'] $fields['amphur'] $fields['tumbon']
        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
	$itemsProvince = EzfQuery::getProvince();
	
	$inputProvinceID;
	$inputAmphurID;
	$inputTumbonID;
	$inputProvinceValue;
	$inputAmphurValue;
	$inputTumbonValue;
	
	if ($this->hasModel()) {
            $inputProvinceID = Html::getInputId($this->model, $fields['province']);
	    $inputAmphurID = Html::getInputId($this->model, $fields['amphur']);
            $inputTumbonID = Html::getInputId($this->model, $fields['tumbon']);
            $inputTumbonValue = Html::getAttributeValue($this->model, $fields['tumbon']);
	    $inputProvinceValue = Html::getAttributeValue($this->model, $fields['province']);
	    $inputAmphurValue = Html::getAttributeValue($this->model, $fields['amphur']);
        }
        
        $annotatedP = '';
        $annotatedA = '';
        $annotatedT = '';
        if(isset($this->options['annotated']) && $this->options['annotated']){
            $annotatedP = "<code>{$fields['province']}</code>";
            $annotatedA = "<code>{$fields['amphur']}</code>";
            $annotatedT = "<code>{$fields['tumbon']}</code>";
        }
	//$html = '<label>'.$this->model->getAttributeLabel($this->attribute).'</label>';
	$html = "<div class='row'><div class='col-md-4'>";
        $html .=  Select2::widget([
            'options' => ['placeholder' => 'จังหวัด','id'=>$this->id.'_'.$fields['province']],
            'data' => ArrayHelper::map($itemsProvince,'PROVINCE_CODE','PROVINCE_NAME'),
            //'model' =>$this->model,
            'name'=>$this->id.'_'.$fields['province'],
	    'value' => $inputProvinceValue,
            'pluginOptions' => [
                'allowClear' => true,
		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		'templateResult' => new JsExpression('function(result) { return result.text; }'),
		'templateSelection' => new JsExpression('function (result) { return result.text; }'),
            ],
	    'pluginEvents' => [
		//"select2:select" => "function(e) { $('#$inputProvinceID').val(e.params.data.id); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }",
		//"select2:unselect" => "function() { $('#$inputProvinceID').val(''); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }"
                "select2:select" => "function(e) { $('#$inputProvinceID').val(e.params.data.id); }",
		"select2:unselect" => "function() { $('#$inputProvinceID').val(''); }"
	    ]
        ]);
        $html .= $annotatedP;
	$html .= "</div>";
        $html .= "<div class='col-md-4 sdbox-col'>";
        $html .= DepDrop::widget([
            'type'=>  DepDrop::TYPE_SELECT2,
            'options'=>['id'=>$this->id.'_'.$fields['amphur']],
            //'model'=>$this->model,
            'name'=>$this->id.'_'.$fields['amphur'],
	    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
		'allowClear' => true,
                'depends'=>[$this->id.'_'.$fields['province']],
                'placeholder'=>'อำเภอ',
                //'initialize' => true,
                'url'=>Url::to(['/ezforms2/province/get-amphur']),
		'params'=>[$inputAmphurID],
            ],
	    'pluginEvents' => [
		"select2:select" => "function(e) {  $('#$inputAmphurID').val(e.params.data.id); }",
		"select2:unselect" => "function() { $('#$inputAmphurID').val(''); }",
                "depdrop:change" => "function() { $('#$inputAmphurID').val($(this).val()); }",
	    ]
        ]);
	$html .= $annotatedA;
	$html .= "</div>";
        
        if($this->enable_tumbon){
            $html .= "<div class='col-md-4 sdbox-col'>";
            $html .= DepDrop::widget([
                'type'=>  DepDrop::TYPE_SELECT2,
                'options'=>['id'=>$this->id.'_'.$fields['tumbon']],
                //'model'=>$this->model,
                'name'=>$this->id.'_'.$fields['tumbon'],
		'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                'pluginOptions'=>[
		    'allowClear' => true,
                    'depends'=>[$this->id.'_'.$fields['amphur']],
                    'placeholder'=>'ตำบล',
		    'initialize' => true,
		    'initDepends'=>[$this->id.'_'.$fields['province']],
                    'url'=>Url::to(['/ezforms2/province/get-tumbon']),
		    'params'=>[$inputTumbonID],
                ],
		'pluginEvents' => [
		    "select2:select" => "function(e) { $('#$inputTumbonID').val(e.params.data.id); }",
		    "select2:unselect" => "function() { $('#$inputTumbonID').val(''); }",
                    "depdrop:change" => "function() { $('#$inputTumbonID').val($(this).val()); }",
		]
            ]);
            $html .= $annotatedT;
            $html .= "</div>";
        }
	$html .= "</div>";

	echo $html;
	
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $fields['province']);
	    echo Html::activeHiddenInput($this->model, $fields['amphur']);
            echo Html::activeHiddenInput($this->model, $fields['tumbon']);
        } 
    }
    
    public function registerClientScript() {
	$view = $this->getView();
	ProvinceAsset::register($view);
    }
    
}
