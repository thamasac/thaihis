<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$id = appxq\sdii\utils\SDUtility::getMillisecTime();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="row">
   
    <div class="col-md-12">
        <label>Button type</label>
        <?php 
            $items = ['input'=>'Input','button' => "Button", 'a' => "Link", 'div' => "Div", 'table' => "Table"]; 
            echo kartik\widgets\Select2::widget([
                'name' => 'options[input_type]',
                'value'=> isset($options['input_type']) ? $options['input_type'] : '',
                'data' => $items,
                'options' => [
                    'id'=> \appxq\sdii\utils\SDUtility::getMillisecTime(),
                    'placeholder' => 'Select provinces ...',
                    'multiple' => true
                ],
            ])
            //echo Html::checkboxList('options[element_type]', isset($options['element_type']) ? $options['element_type'] : '', $items, []);
                     
        ?>
    </div>
    
</div>

<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>