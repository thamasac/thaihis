<?php

namespace dms\joke\widgets;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\JsExpression;

class ScheduleList extends InputWidget {

    public $fields;
    public $ezf_id;
    public $ezf_field_id;

    public function init() {
        parent::init();
    }

    public function run() {
        $fields;
        if ($this->hasModel()) {
            $enableVisitName = Html::getInputName($this->model, $this->attribute);
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $enableVisitName = $this->name;
            $value = $this->value;
        }
        if ($this->hasModel()) {
            $enableVisitID = Html::getInputId($this->model, $this->attribute);
        }
        $field = $this->model->ezf_field;

        $ezf_field = EzfQuery::getFieldByName($field['ezf_id'], $this->attribute);
        $field_data = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_data']);
        $field_options = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_options']);

        $key = "id";
        $val = "visit_name";

        $dataItems = [];
        if (isset($field_data['schedule_type'])) {
            eval("\$dataItems = {$field_data['schedule_type']};");
        }
        if ($this->attribute == "group_name") {
            $key = "id";
            $val = "group_name";
        }
        $multiple = FALSE;
        if (isset($field_options['options']['multiple'])) {
            $multiple = $field_options['options']['multiple'];
        }

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $html ='<div class="form-group">';
        $html .='<div class="input-group" id="comp-'.$this->ezf_field_id.$this->id.'" >';
        $html .= Select2::widget([
                    'options' => ['placeholder' => 'Visit schedule', 'id' => $enableVisitID, 'multiple' => $multiple],
                    'data' => ArrayHelper::map($dataItems, $key, $val),
                    'name' => $enableVisitName,
                    'value' => $value,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'initialize' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(result) { return result.text; }'),
                        'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function(e) { $('#$enableVisitID').val(e.params.data.id); }",
                        "select2:unselect" => "function() { $('#$enableVisitID').val('');}",
                    ]
        ]);
        $html.= '</div>';
        $html.= '</div> ';
        
        echo $html;
        
    }

    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
            
            ");
    }

}
