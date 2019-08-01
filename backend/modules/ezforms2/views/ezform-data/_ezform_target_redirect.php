<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfStarterWidget;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php EzfStarterWidget::begin(); ?>
<div id="<?=$reloadDiv?>">
<?php 
$formName = 'ezform-' . $ezf_id;
$form = EzActiveForm::begin([
    'id' => $formName,
    'action' => ['/ezforms2/ezform-data/ezform-redirect', 
        'ezf_id' => $ezf_id , 
        'modal'=>$modal, 
        'dataid' => $dataid,
        'initdata' => $initdata,
        'reloadDiv' => $reloadDiv,
        'initdata'=>$initdata,
        'targetField' => $targetField,
        'v' => $modelVersion['ver_code'],
        'db2' => $db2,
                'redirect' => $redirect,
        ],
    'options' => [
        'enctype' => 'multipart/form-data-redirect',
        'data-ezf_id' => $ezf_id,
        'data-dataid' => $dataid,
        'data-modal' => $modal,
        'data-reloadDiv' => $reloadDiv,
        'data-initdata' => $initdata,
        'data-v' => $modelVersion['ver_code'],
        'data-db2' => $db2,
        'data-redirect' => $redirect,
    ]
]); 
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
</div>
<div class="modal-body">
    <?php if($type==1):?>
    <div class="alert alert-info " role="alert" style="font-size: 20px;"> <strong><i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('ezform', 'Target selection phase')?></strong> <?= Yii::t('ezform', 'Please select a goal to go to the next step.')?> </div>
    <?php else:?>
    <div class="alert alert-warning" role="alert" style="font-size: 20px;"> <strong><i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('ezform', 'Filling procedure')?></strong> <?= Yii::t('ezform', 'Please fill in the required information to proceed to the next step.')?> </div>
    <?php endif;?>
    
    <div id="formPanel" class="row">
        <?php
        
        foreach ($modelFields as $field) {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            if ($field['ezf_field_ref'] == NULL || empty($field['ezf_field_ref'])) {
                if($targetField!=''){
                    $options = \appxq\sdii\utils\SDUtility::string2Array($field['ezf_field_options']);
                    $options['id'] = 'target_' . $field['ezf_id'] . '_' . $field['ezf_field_name'].'_'. \appxq\sdii\utils\SDUtility::getMillisecTime();
                    $options['options']['id'] = $options['id'];
                    $options['pluginOptions']['ajax']['url'] = Url::to([$options['pluginOptions']['ajax']['url'], 'filter_id'=>$target, 'filter_name'=>$targetField]);
                    $field['ezf_field_options'] = \appxq\sdii\utils\SDUtility::array2String($options);
                }
                
                echo EzfFunc::generateInput($form, $model, $field, $dataInput, 0 , $modelEzf);
                
            }
        }
        $fieldID = backend\modules\ezforms2\classes\EzfQuery::getFieldByName($ezf_id, 'id');
        if($fieldID){
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = EzfFunc::getInputByArray($fieldID['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            echo EzfFunc::generateInput($form, $model, $fieldID, $dataInput, 0 , $modelEzf);
        }
        
        ?>
    </div>
    <div id="error-alert-ezf"></div>
</div>

<?php EzActiveForm::end(); ?>
</div>
  
<?php EzfStarterWidget::end(); ?>
<?php

$this->registerJs("

        
$('form#$formName').on('beforeSubmit', function(e) {
      
    return false;
});

");
?>