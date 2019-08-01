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

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>


<div class="panel panel-primary">
    <div class="panel-heading">
<?= Yii::t('chanpan', 'Help text config') ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">   
                <label><?= Yii::t('chanpan', 'Help type') ?></label>
<?php
echo Html::radioList('options[select_topic]', $options['select_topic'], ['1' => 'Single', '2' => 'Multiple']);
?>
            </div>
            <div class="col-md-3">
                <label><?= Yii::t('chanpan', 'Panel') ?></label>
<?php
echo Html::radioList('options[panel]', $options['panel'], ['1' => 'Yes', '2' => 'No']);
?>
            </div>
            <div class="col-md-3" id="panel-type">
                <label><?= Yii::t('chanpan', 'Panel type') ?></label>
<?php
echo Html::radioList('options[panel_type]', $options['panel_type'], [
    'primary' => 'Primary',
    'success' => 'Success',
    'info' => 'Info',
    'warning' => 'Warning',
    'danger' => 'Danger'
    ]);
?>
            </div>
            <div class="col-md-3">
                <label><?= Yii::t('chanpan', 'Icon') ?></label>
                <?= dominus77\iconpicker\IconPicker::widget([
                    'name'=>"options[icon]",
                    'value'=>isset($options['icon'])?$options['icon']:'fa-check',
                    'options'=>['class'=>'sicon-input form-control', 'id'=>"iconpicker_".$widget_config['widget_id']],
                    'clientOptions'=>[
                        'hideOnSelect'=>true,
                    ]
                ])?>
            </div>
            
            
        </div>
    </div>
</div>
<!--config start-->



<!--config end-->

<?php
$this->registerJS("
     
     function onLoad(){
        const checkType = $('input[name=\'options[panel]\']:checked').val();
        togglePanelType(checkType);
     }onLoad();

     $('input[name=\'options[panel]\']').on('change', function() {
       let value = $(this).val();
       togglePanelType(value);
     });
     
     function togglePanelType(value){
        if(value == 2){
          $('#panel-type').hide();
       }else{
          $('#panel-type').show(); 
       }
     }
");
?>