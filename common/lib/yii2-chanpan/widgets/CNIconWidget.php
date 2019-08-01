<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cpn\chanpan\widgets;

use appxq\sdii\utils\SDUtility;
use dominus77\iconpicker\IconPicker;

/**
 * Description of CNIconWidget
 *
 * @author AR9
 */
class CNIconWidget extends \yii\jui\InputWidget {

    //put your code here

    public function init() {
        parent::init();
        $this->id = SDUtility::getMillisecTime();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id;
        }
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' dicon-input form-control';
        } else {
            $this->options['class'] = 'dicon-input form-control';
        }
    }

    public function run() {
        if ($this->hasModel()) {
            echo IconPicker::widget([
                'model' => $this->model,
                'attribute' => $this->attribute,
                'options' => $this->options,
                'clientOptions' => [
                    'hideOnSelect' => true,
                ]
            ]);
        } else {
            echo IconPicker::widget([
                'name' => $this->name,
                'value' => $this->value,
                'options' => $this->options,
                'clientOptions' => [
                    'hideOnSelect' => true,
                ]
            ]);
        }
    }

    public function registerJs() {
        
    }

}
