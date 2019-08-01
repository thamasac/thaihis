<?php
namespace appxq\sdii\widgets;

use kartik\file\FileInput as BaseFileInput;

/**
 * Description of FileInput
 *
 * @author appxq
 */
class FileInput extends BaseFileInput {
    
    public function init()
    {
        if(isset($this->options['multiple'])){
            $this->options['name'] = \yii\helpers\Html::getInputName($this->model, $this->attribute).'[]';
        }
        parent::init();
    }
    
    
}
