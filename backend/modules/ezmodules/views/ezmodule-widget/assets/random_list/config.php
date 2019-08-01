<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);


?>

    <!--config start-->
    <div class="form-group row">
        <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Widget</h4></div>
        <div class="col-md-6">
            <?= Html::hiddenInput('options[reloadDiv]', isset($options['reloadDiv']) ? $options['reloadDiv'] : 'random-' . \appxq\sdii\utils\SDUtility::getMillisecTime()) ?>
            <?= Html::label('Color', ['for' => 'select-color']) ?>
            <?= \kartik\select2\Select2::widget([
                'name' => 'options[color]',
                'value' => isset($options['color']) ? $options['color'] : '',
                'data' => [
                    'panel-default' => 'Default',
                    'panel-primary' => 'Primary',
                    'panel-info' => 'Info',
                    'panel-warning' => 'Warning',
                    'panel-danger' => 'Danger',
                ],
                'options' => ['placeholder' => 'Select a color ...', 'id' => 'select-color'],
            ]) ?>
        </div>
    </div>


    <!--config end-->

<?php
$this->registerJS("
    
");
?>