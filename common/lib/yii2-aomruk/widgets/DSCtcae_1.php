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

    public function run()
    {
	$fields;
        $htmlCtcae = '';
        
        if(isset($this->fields)){
            foreach ($this->fields as $key => $value) {
                $fields[$value['label']] = $value['attribute'];
            }
        } else {
            return 'Fields not set.';
        }
        //$fields['province'] $fields['amphur'] $fields['tumbon']
        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
	$itemsProvince = (new \yii\db\Query())->select('*')->from('const_soc')->all();
//	$itemsProvince = EzfQuery::getProvince();
//	\appxq\sdii\utils\VarDumper::dump($this->model);
	$inputProvinceID;
	$inputAmphurID;
	$inputTumbonID;
	$inputProvinceValue;
	$inputAmphurValue;
	$inputTumbonValue;
//	\appxq\sdii\utils\VarDumper::dump($fields);
	if ($this->hasModel()) {
            $inputProvinceID = Html::getInputId($this->model, $fields['soc']);
	    $inputAmphurID = Html::getInputId($this->model, $fields['ctcae']);
            $inputTumbonID = Html::getInputId($this->model, $fields['grade']);
            $inputTumbonValue = Html::getAttributeValue($this->model, $fields['grade']);
	    $inputProvinceValue = Html::getAttributeValue($this->model, $fields['soc']);
	    $inputAmphurValue = Html::getAttributeValue($this->model, $fields['ctcae']);
        }
        
        $annotatedP = '';
        $annotatedA = '';
        $annotatedT = '';
        if(isset($this->options['annotated']) && $this->options['annotated']){
            $annotatedP = "<code>{$fields['soc']}</code>";
            $annotatedA = "<code>{$fields['ctcae']}</code>";
            $annotatedT = "<code>{$fields['grade']}</code>";
        }
        
        $idSoc = $this->id.'_'.$fields['soc'];
        $idTerm = $this->id.'_'.$fields['ctcae'];
        $idGrade = $this->id.'_'.$fields['grade'];
	//$html = '<label>'.$this->model->getAttributeLabel($this->attribute).'</label>';
	$html = "<div class='row'><div class='col-md-4'>";
        $html .=  Select2::widget([
            'options' => ['placeholder' => 'SOC','id'=>$this->id.'_'.$fields['soc']],
            'data' => ArrayHelper::map($itemsProvince,'id','soc'),
            //'model' =>$this->model,
            'name'=>$this->id.'_'.$fields['soc'],
	    'value' => $inputProvinceValue,
            'pluginOptions' => [
                'allowClear' => true,
		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		'templateResult' => new JsExpression('function(result) { return result.text; }'),
		'templateSelection' => new JsExpression('function (result) { return result.text; }'),
            ],
	    'pluginEvents' => [
		"select2:select" => "function(e) { $('#$inputProvinceID').val(e.params.data.id); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }",
		"select2:unselect" => "function() { $('#$inputProvinceID').val(''); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }"
	    ]
        ]);
        $html .= $annotatedP;
	$html .= "</div>";
        $html .= "<div class='col-md-4 sdbox-col ctcaeSelect'>";
        $html .= DepDrop::widget([
            'type'=>  DepDrop::TYPE_SELECT2,
            'options'=>['id'=>$this->id.'_'.$fields['ctcae']],
            //'model'=>$this->model,
            'name'=>$this->id.'_'.$fields['ctcae'],
	    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
		'allowClear' => true,
                'depends'=>[$this->id.'_'.$fields['soc']],
                'placeholder'=>'CTCAE Term',
                'url'=>Url::to(['/ezforms2/ctcae/get-ctcae-term']),
		'params'=>[$inputAmphurID],
            ],
	    'pluginEvents' => [
		"select2:select" => "function(e) {  $('#$inputAmphurID').val(e.params.data.id); $('#$inputTumbonID').val(''); }",
		"select2:unselect" => "function() { $('#$inputAmphurID').val(''); $('#$inputTumbonID').val(''); }",
	    ]
        ]);
	$html .= $annotatedA;
	$html .= "</div>";
        
        
        
        if($this->enable_tumbon){
            $html .= "<div class='col-md-4 sdbox-col'>";
            $html .= DepDrop::widget([
                'type'=>  DepDrop::TYPE_SELECT2,
                'options'=>['id'=>$this->id.'_'.$fields['grade']],
                //'model'=>$this->model,
                'name'=>$this->id.'_'.$fields['grade'],
		'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                'pluginOptions'=>[
		    'allowClear' => true,
                    'depends'=>[$this->id.'_'.$fields['soc'],$this->id.'_'.$fields['ctcae']],
                    'placeholder'=>'Grade',
		    'initialize' => true,
		    'initDepends'=>[$this->id.'_'.$fields['soc']],
                    'url'=>Url::to(['/ezforms2/ctcae/get-grade']),
		    'params'=>[$inputTumbonID],
                ],
		'pluginEvents' => [
		    "select2:select" => "function(e) { $('#$inputTumbonID').val(e.params.data.id); }",
		    "select2:unselect" => "function() { $('#$inputTumbonID').val(''); }",
		]
            ]);
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
	ProvinceAsset::register($view);
        $view->registerJs("
                
            ");
    }
    
    public function registerClientScript() {
	$view = $this->getView();
	ProvinceAsset::register($view);
        $view->registerJs("
            
            ");
    }
    
}
